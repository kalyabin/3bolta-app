<?php
namespace handbook;

use Yii;

/**
 * Модуль справочников
 */
class Module extends \app\components\Module
{
    public function init()
    {
        parent::init();

        Yii::configure($this, include __DIR__ . '/config.php');
    }
}