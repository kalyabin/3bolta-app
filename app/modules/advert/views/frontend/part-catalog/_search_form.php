<?php
/**
 * Поиск в верхней части
 */

use advert\forms\PartSearch;
use advert\models\Part;
use app\widgets\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model PartSearch */
/* @var $this View */

$form = ActiveForm::begin([
    'id' => 'topSearch',
    'method' => 'get',
    'action' => Url::toRoute(['/advert/part-catalog/search']),
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'template' => '{input}{icon}',
        'parts' => ['{icon}' => ''],
    ]
]);
?>
<div class="row">
    <div class="col-xs-12">
        <?=$this->render('_choose_auto', [
            'form' => $form,
            'model' => $model,
        ])?>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-xs-12">
        <?=$form->field($model, 'con', [
            'parts' => ['{icon}' => '<span class="form-control-icon glyphicon glyphicon-wrench"></span>'],
            'inputOptions' => [
                'class' => 'form-control form-control-with-icon',
                'placeholder' => Yii::t('frontend/advert', 'Condition'),
            ],
        ])->dropDownList(Part::getConditionDropDownList(true))?>
    </div>
    <div class="col-md-4 col-xs-12">
        <?=$form->field($model, 'cat', [
            'parts' => ['{icon}' => '<span class="form-control-icon glyphicon glyphicon-tag"></span>'],
            'inputOptions' => [
                'class' => 'form-control form-control-with-icon',
                'placeholder' => Yii::t('frontend/advert', 'Category'),
            ],
        ])->dropDownList(Part::getCategoryDropDownList(true))?>
    </div>
    <div class="col-md-4 col-xs-12">
        <?=$form->field($model, 'q', [
            'parts' => ['{icon}' => '<span class="form-control-icon glyphicon glyphicon-search"></span>'],
            'inputOptions' => [
                'class' => 'form-control form-control-with-icon',
                'placeholder' => Yii::t('frontend/advert', 'Name or catalogue number'),
            ]
        ])->textInput(['maxlength' => PartSearch::MAX_QUERY_LENGTH])?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-4">
        <?=$form->field($model, 'r', [
            'parts' => ['{icon}' => '<span class="form-control-icon icon-location"></span>'],
            'template' => '{input}{icon}<br />' . Html::activeCheckbox($model, 'sor', ['labelOptions' => ['class' => 'top-search-all-regions']]),
            'inputOptions' => [
                'class' => 'form-control form-control-with-icon js-select-region-dropdown',
                'data-live-search' => 'true',
                'placeholder' => Yii::t('frontend/advert', 'Region'),
            ],
        ])->dropDownList(PartSearch::getRegionsDropDownList())?>
    </div>
    <div class="col-xs-12 col-md-4 top-search-price form-group ">
        <?=Yii::t('frontend/advert', 'Price from')?>
        <?=Html::activeTextInput($model, 'p1', ['maxlength' => 6, 'class' => 'form-control'])?>
        <?=Yii::t('frontend/advert', 'to')?>
        <?=Html::activeTextInput($model, 'p2', ['maxlength' => 6, 'class' => 'form-control'])?>
    </div>
    <div class="col-xs-12 col-md-4 form-group" data-title="<?=Yii::t('frontend/advert', 'Seller')?>">
        <?=$form->field($model, 'st', [
            'parts' => ['{icon}' => '<span class="form-control-icon icon icon-user"></span>'],
            'inputOptions' => [
                'class' => 'form-control form-control-with-icon',
                'placeholder' => Yii::t('frontend/advert', 'Seller'),
            ],
        ])->dropDownList(PartSearch::getSellerTypeDropDown())?>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-xs-12">
        <?=Html::submitButton(Yii::t('frontend/advert', 'Search'), ['class' => 'form-control btn btn-primary'])?>
    </div>
</div>
<?php $form->end(); ?>
