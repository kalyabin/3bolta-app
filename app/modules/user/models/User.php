<?php
/**
 * Модель пользователя
 */

namespace user\models;

use Yii;
use app\components\ActiveRecord;
use yii\web\IdentityInterface;
use app\components\PhoneValidator;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @var string[] новые роли для установки
     */
    protected $_roles = [];

    /**
     * @var \yii\rbac\Role[] роли пользователя
     */
    protected $_oldRoles;

    /**
     * @var string новый пароль
     */
    public $new_password;

    const STATUS_ACTIVE = 1; // статус - активен
    const STATUS_WAIT_CONFIRMATION = 2; // статус - требует активации
    const STATUS_LOCKED = 3; // статус - заблокирован

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['status'], 'integer', 'min' => 0],
            [['last_login', 'roleCodes'], 'safe'],
            [['new_password'], 'required', 'on' => 'create'],
            [['new_password'], 'safe'],
            [['phone'], PhoneValidator::className(), 'canonicalAttribute' => 'phone_canonical'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Name'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'status' => Yii::t('user', 'Status'),
            'last_login' => Yii::t('user', 'Last login'),
            'roleCodes' => Yii::t('user', 'User roles'),
            'new_password' => Yii::t('user', 'New password'),
        ];
    }

    /**
     * Обертка для метода сохранения
     * @param boolean $runValidation
     * @param [] $attributeNames
     * @return boolean
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (is_array($attributeNames) && in_array('phone', $attributeNames)) {
            // телефон без валидации сохранить невозможно,
            // т.к. необходимо записать его в канонической форме
            // через валидатор PhoneValidator
            $runValidation = true;
            if (!in_array('phone_canonical', $attributeNames)) {
                $attributeNames[] = 'phone_canonical';
            }
        }
        return parent::save($runValidation, $attributeNames);
    }

    /**
     * Перед сохранением
     */
    public function beforeSave($insert)
    {
        if ($this->new_password) {
            $this->password = Yii::$app->getModule('user')->getPasswordHash($this->new_password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::find()->where(['id' => $id])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::find()->where(['email' => $username])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return md5(serialize([
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() == $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Returns username
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Returns full username
     * @return string
     */
    public function getFullUserName()
    {
        return $this->name;
    }

    /**
     * Returns user roles as string array
     * @return []
     */
    public function getRolesStr()
    {
        $ret = [];

        $roles = $this->getRoles();

        foreach ($roles as $role) {
            $ret[] = $role->description;
        }

        return $ret;
    }

    /**
     * Returns user roles as object array
     * @return \yii\rbac\Role[]
     */
    public function getRoles()
    {
        $ret = !empty($this->_oldRoles) ? $this->_oldRoles : [];

        if (empty($this->_oldRoles) && !$this->isNewRecord) {
            /* @var $authManager \yii\rbac\DbManager */
            $authManager = Yii::$app->authManager;
            /* @var $db \yii\db\Connection */
            $db = Yii::$app->db;

            $sql = "SELECT item_name "
                . "FROM " . $authManager->assignmentTable . " "
                . "WHERE user_id = '" . $this->id . "'";

            $assignments = $db->createCommand($sql)
                ->queryAll();

            foreach ($assignments as $assignment) {
                $role = $authManager->getRole($assignment['item_name']);
                if ($role) {
                    $ret[] = $role;
                }
            }

            $this->_oldRoles = $ret;
        }

        return $ret;
    }

    /**
     * Returns user roles as code array
     * @return \yii\rbac\Role[]
     */
    public function getRoleCodes()
    {
        $ret = [];

        $roles = $this->getRoles();

        foreach ($roles as $role) {
            $ret[] = $role->name;
        }

        return $ret;
    }

    /**
     * Установить новые роли из формы
     */
    public function setRoleCodes($roles)
    {
        $roles = is_array($roles) ? $roles : [];

        $oldRoles = $this->getRoleCodes();

        $this->_roles = $roles;
    }

    /**
     * Обновить роли, если требуется
     */
    public function updateRoles()
    {
        $ret = true;

        if (is_array($this->_roles)) {
            /* @var $db \yii\db\Connection */
            $db = Yii::$app->db;
            /* @var $authManager \yii\rbac\DbManager */
            $authManager = Yii::$app->authManager;

            $transaction = $db->beginTransaction();

            try {
                $db->createCommand()
                    ->delete($authManager->assignmentTable, "user_id='" . $this->id . "'")
                    ->execute();

                foreach ($this->_roles as $role) {
                    if (!$db->createCommand()
                        ->insert($authManager->assignmentTable, [
                            'item_name' => $role,
                            'user_id' => $this->id,
                            'created_at' => time()
                        ])
                        ->execute()) {
                        throw new \Exception();
                    }
                }

                $transaction->commit();
            }
            catch (Exception $e) {
                $transaction->rollBack();
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Вернуть запись из таблички подтверждений.
     * В случае, если ее нет - создает новую.
     *
     * @return UserConfirmation
     */
    public function getConfirmation()
    {
        $res = $this->hasOne(UserConfirmation::className(), ['user_id' => 'id']);
        if (!$res->exists()) {
            $confirmation = new UserConfirmation();
            $confirmation->setAttribute('user_id', $this->id);
            if ($confirmation->save()) {
                return $confirmation;
            }
        }
        return $res->one();
    }
}
