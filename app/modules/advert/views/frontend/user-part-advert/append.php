<?php
/**
 * Добавить объявление
 */

use yii\helpers\Html;

/* @var $this \yii\base\View */
/* @var $model \advert\forms\PartForm */
/* @var $user \user\models\User */

print Html::beginTag('div', ['class' => 'col-lg-12']);
    print Html::tag('h1', Yii::t('frontend/advert', 'Place an advert'));
print Html::endTag('div');

print $this->render('_form', [
    'model' => $model,
    'user' => $user,
]);