<?php
namespace handbook\models;

use Yii;
use app\components\ActiveRecord;

class HandbookValue extends ActiveRecord
{
    /**
     * Название таблицы
     * @return string
     */
    public static function tableName()
    {
        return '{{%handbook_value}}';
    }

    /**
     * Правила валидации
     * @return []
     */
    public function rules()
    {
        return [
            [['handbook_code', 'sort', 'name'], 'required'],
            [['handbook_code'], 'in', 'range' => Handbook::getAvailCode()],
            [['sort'], 'integer', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Подписи к полям
     * @return []
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'handbook_code' => Yii::t('handbook', 'Handbook code'),
            'sort' => Yii::t('handbook', 'Sort'),
            'name' => Yii::t('handbook', 'Values name'),
        ];
    }

    /**
     * Получить справочник
     * @return \yii\db\ActiveQuery
     */
    public function getHandbook()
    {
        return $this->hasOne(Handbook::className(), ['code' => 'handbook_code']);
    }

    /**
     * Условие по умолчанию
     * @return yii\db\ActiveQuery
     */
    public static function find()
    {
        return parent::find()->orderBy('sort ASC');
    }
}