<?php
namespace partner\forms;

use auto\models\Mark;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Модель формы для поиска торговых точек на карте:
 * - координаты левого нижнего угла карты;
 * - координаты правого верхнего угла карты;
 * - специализация;
 * - поиск по вхождению строки;
 */
class TradePointMap extends Model
{
    /**
     * @var array массив координат вида: array('ne' => array('lat' => <float>, 'lng' => <float>), 'sw' => ...);
     */
    protected $_coordinates;

    /**
     * @var string название специализации
     */
    public $specialization;

    /**
     * @var string поиск по вхождению строки
     */
    public $name;

    /**
     * @var string поиск по адресу
     */
    public $address;

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['address', 'string', 'max' => 255],
            ['specialization', 'string', 'max' => 255],
            [['coordinates'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'specialization' => Yii::t('frontend/partner', 'Search by auto mark'),
            'name' => Yii::t('frontend/partner', 'Search by organization name'),
            'coordinates' => Yii::t('frontend/partner', 'Coordinates'),
            'address' => Yii::t('frontend/partner', 'Search by address'),
        ];
    }

    /**
     * Установка координат в переменную _coordinates.
     * Будут установлены всегда только валидные координаты, иначе - null.
     *
     * @param string $val
     */
    public function setCoordinates($val)
    {
        $val = (string) $val;

        $this->_coordinates = null;

        try {
            $arr = Json::decode($val, true);
            $arr['ne'] = [
                'lat' => isset($arr['ne']['lat']) ? (float) $arr['ne']['lat'] : null,
                'lng' => isset($arr['ne']['lng']) ? (float) $arr['ne']['lng'] : null,
            ];
            $arr['sw'] = [
                'lat' => isset($arr['sw']['lat']) ? (float) $arr['sw']['lat'] : null,
                'lng' => isset($arr['sw']['lng']) ? (float) $arr['sw']['lng'] : null,
            ];
            if (!is_null($arr['ne']['lat']) || !is_null($arr['ne']['lng']) ||
                !is_null($arr['sw']['lat']) || !is_null($arr['sw']['lat'])) {
                $this->_coordinates = $arr;
            }
        }
        catch (Exception $ex) {
            $this->_coordinates = null;
        }
    }

    /**
     * Получить координаты в виде строки
     * @return string
     */
    public function getCoordinates()
    {
        return is_array($this->_coordinates) ? Json::encode($this->_coordinates) : '';
    }

    /**
     * Получить координаты в виде массива
     * @return array
     */
    public function getCoordinatesArray()
    {
        return $this->_coordinates;
    }

    /**
     * Получить массив специализаций (марок) для вставки в автокомплит
     *
     * @return array
     */
    public function getSpecializationAutocomplete()
    {
        return array_values(ArrayHelper::map(Mark::find()->all(), 'id', function($data) {
            /* @var $data Mark */
            return [
                'label' => $data->name,
                'value' => $data->id,
            ];
        }));
    }
}