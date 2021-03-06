<?php
namespace app\components;

use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Валидатор телефона.
 *
 * Запрещает ввести номер телефона в неправильном формате.
 * Формат установлен в константе PHONE_PATTERN.
 * Маска для отображения в инпуте используется в PHONE_MASK.
 *
 * Проверяет уникальность телефона вокруг всех классов, передаваемых функцией getUnqiueClasses.
 */
class PhoneValidator extends Validator
{
    /**
     * Атрибут для канонической записи телефона
     * @var string
     */
    public $canonicalAttribute;

    /**
     * Паттерн для проверки валидности телефона
     */
    const PHONE_PATTERN = '#^\+7[\s]\([\d]{3}\)[\s][\d]{3}-[\d]{2}-[\d]{2}$#';

    /**
     * Маска для ввода телефона
     */
    const PHONE_MASK = '+7 (999) 999-99-99';

    /**
     * Валидация телефона
     *
     * @param mixed $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        /* @var $model Model */
        if (!preg_match(self::PHONE_PATTERN, $model->$attribute)) {
            // валидация формата телефона
            $this->addError($model, $attribute, Yii::t('main', 'Wrong phone format'));
        }
        else if (!empty($this->canonicalAttribute)) {
            // валидация уникальности телефона
            $canonicalAttribute = $this->canonicalAttribute;
            $canonicalPhone = self::getCanonicalPhone($model->$attribute);
            $model->$canonicalAttribute = $canonicalPhone;
        }
    }

    /**
     * Вернуть телефон в каноническом виде.
     *
     * @param string $phone
     * @return string
     */
    public static function getCanonicalPhone($phone)
    {
        $phone = preg_replace('#[\D]+#', '', $phone);
        if (strlen($phone) >= 11 && $phone[0] == 8) {
            // телефон начинается с восьмерки, необходимо ее заменить на 7
            $phone = '7' . substr($phone, 1, strlen($phone) - 1);
        }
        return $phone;
    }
}