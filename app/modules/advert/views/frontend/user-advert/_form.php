<?php
/**
 * Форма редактирования/добавления объявления
 */

use yii\helpers\Url;
use advert\forms\Form;
use kartik\widgets\FileInput;
use advert\models\Advert;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\base\View */
/* @var $model \advert\forms\Form */
/* @var $user \user\models\User */

$existImages = [];
if ($advert = $model->getExists()) {
    foreach ($advert->getImages()->all() as $file) {
        /* @var $file \advert\models\AdvertImage */
        $existImages[] = Html::img($file->getFile()->getUrl(), [
            'class' => 'file-preview-image',
        ]);
    }
}

$form = ActiveForm::begin([
    'id' => 'create-advert',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]);
?>
    <div class="col-sm-12 col-lg-4">
        <?=$form->field($model, 'name')->textInput()?>
    </div>
    <div class="col-sm-12 col-lg-4">
        <?=$form->field($model, 'category_id')->dropDownList(Advert::getCategoryDropDownList(true))?>
    </div>
    <div class="col-sm-12 col-lg-2">
        <?=$form->field($model, 'condition_id')->dropDownList(Advert::getConditionDropDownList(true))?>
    </div>
    <div class="col-sm-12 col-lg-2">
        <?=$form->field($model, 'price')->textInput()?>
    </div>

    <div class="col-xs-12">
        <?=Html::tag('h3', Yii::t('frontend/advert', 'Images'))?>
    </div>

    <div class="col-sm-12">
        <?=$form->field($model, 'uploadImage', [
            'template' => '{input}',
        ])->widget(FileInput::className(), [
            'options' => [
                'accept' => 'image/*',
                'multiple' => true,
                'name' => Html::getInputName($model, 'uploadImage') . '[]',
            ],
            'pluginOptions' => [
                'initialPreview' => $existImages,
                'uploadUrl' => 'ss',
                'multiple' => 'multiple',
                'maxFileCount' => Advert::UPLOAD_MAX_FILES,
                'allowedFileExtensions' => Advert::$_imageFileExtensions,
                'layoutTemplates' => [
                    'actions' => '{delete}',
                ],
                'overwriteInitial' => false,
            ],
        ])?>
    </div>

    <div class="col-xs-12">
        <?=Html::tag('h3', Yii::t('frontend/advert', 'Automobiles'))?>
    </div>

    <div class="col-xs-12 block-info block-info-primary">
        Обязательным выбором обладает марка. Вы можете выбрать не более 10 марок автомобилей и не более 10 моделей. На кузова и двигатели ограчений нет.
    </div>

    <div class="col-xs-12">
        <?=$form->field($model, 'mark', [
            'template' => '{input}{error}',
        ])->hiddenInput(['value' => ''])?>
    </div>

    <div class="col-sm-12">
        <?=$this->render('_form_choose_auto', [
            'form' => $form,
            'model' => $model,
        ])?>
    </div>

    <div class="col-sm-12">
        <?=$form->field($model, 'description')->textarea(['maxlength' => Form::DESCRIPTION_MAX_LENGTH])?>
    </div>

    <div class="col-xs-12">
        <?=Html::tag('h3', Yii::t('frontend/advert', 'Contacts'))?>
    </div>

    <div class="col-xs-12">
        <div class="form-group">
            <div class="col-xs-12 block-info block-info-primary">
                В объявлении будет отображаться следующая контактная информация:<br />

                <strong><?=$model->getAttributeLabel('user_name')?></strong><br />
                <?=Html::encode($user->name)?><br />
                <strong><?=$model->getAttributeLabel('user_phone')?></strong><br />
                <?=Html::encode($user->phone)?><br />

                Для редактирования контактной информации используйте <a href="<?=Url::toRoute(['/user/profile/index'])?>">профиль</a>.
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <?php
        $button = $model->getExists() ? Yii::t('frontend/advert', 'Update advert') : Yii::t('frontend/advert', 'Create advert');
        ?>
        <?=Html::submitButton($button, ['class' => 'btn btn-success'])?>
    </div>
<?php $form->end(); ?>