<?php
namespace partner\models;

use Yii;

use app\components\PhoneValidator;
use yii\db\Expression;

/**
 * Модель торговой точки партнера. Привязывается к модели Partner
 */
class TradePoint extends \yii\db\ActiveRecord
{
    /**
     * Название таблицы
     * @return string
     */
    public static function tableName()
    {
        return '{{%partner_trade_point}}';
    }

    /**
     * Правила валидации
     * @return string
     */
    public function rules()
    {
        return [
            [['partner_id', 'latitude', 'longitude', 'address', 'phone'], 'required'],
            [['latitude', 'longitude'], 'number', 'min' => 0, 'max' => 180],
            ['address', 'string', 'max' => 255],
            [['phone'], PhoneValidator::className(), 'canonicalAttribute' => 'phone_canonical'],
        ];
    }

    /**
     * Подписи атрибутов
     * @return []
     */
    public function attributeLabels()
    {
        return [
            'created' => Yii::t('main', 'Created'),
            'edited' => Yii::t('main', 'Edited'),
            'partner_id' => Yii::t('partner', 'Partner'),
            'latitude' => Yii::t('partner', 'Latitude'),
            'longitude' => Yii::t('partner', 'Longitude'),
            'address' => Yii::t('partner', 'Address'),
            'phone' => Yii::t('partner', 'Contact phone'),
        ];
    }

    /**
     * Получить партнера
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->latitude && $this->longitude) {
            $this->coordinates = new Expression('POINT(' . (float) $this->latitude . ', ' . (float) $this->longitude . ')');
        }
        if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s');
        }
        $this->edited = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}