<?php
use yii\helpers\Html;
use auto\widgets\ChooseAutomobile;

/* @var $this yii\base\View */
/* @var $model advert\forms\PartForm */
/* @var $form yii\bootstrap\ActiveRecord */

$chooseAutomobile = ChooseAutomobile::begin([
    'containerClass' => 'container advert-form-auto-widget',
    'pluginOptions' => [
        'multipleSelect' => true,
        'markName' => Html::getInputName($model, 'mark') . '[]',
        'modelName' => Html::getInputName($model, 'model') . '[]',
        'serieName' => Html::getInputName($model, 'serie') . '[]',
        'modificationName' => Html::getInputName($model, 'modification') . '[]',
        'markIds' => $model->mark,
        'modelIds' => $model->model,
        'serieIds' => $model->serie,
        'modificationIds' => $model->modification,
        'markWrapper' => '.choose-auto-mark',
        'modelWrapper' => '.choose-auto-model',
        'serieWrapper' => '.choose-auto-serie',
        'modificationWrapper' => '.choose-auto-modification',
        'renderItem' => new yii\web\JsExpression('function(type, jsClass, selected, attributeName, id, name, full_name) {
            var obj = {
                "type"          : type,
                "jsClass"       : jsClass,
                "checked"       : selected ? "checked=\"checked\"" : "",
                "attributeName" : attributeName,
                "id"            : id,
                "name"          : full_name
            };
            var tpl = \'<div class="checkbox {$jsClass}"><label><input data-type="{$type}" type="checkbox" {$checked} name="{$attributeName}" value="{$id}" />{$name}</label></div>\';
            $.each(obj, function(k, i) {
                tpl = tpl.replace(\'{$\' + k + \'}\', i);
            });
            return tpl;
        }'),
    ],
]);
    print Html::input('hidden', $chooseAutomobile->pluginOptions['markName'], '');
    print Html::input('hidden', $chooseAutomobile->pluginOptions['modelName'], '');
    print Html::input('hidden', $chooseAutomobile->pluginOptions['serieName'], '');
    print Html::input('hidden', $chooseAutomobile->pluginOptions['modificationName'], '');
    $chooseAutomobile->renderPanel('form-group choose-auto-mark', Yii::t('advert', 'Choose mark'));
    $chooseAutomobile->renderPanel('form-group choose-auto-model', Yii::t('advert', 'Choose model'));
    $chooseAutomobile->renderPanel('form-group choose-auto-serie', Yii::t('advert', 'Choose serie'));
    $chooseAutomobile->renderPanel('form-group choose-auto-modification', Yii::t('advert', 'Choose modification'));
$chooseAutomobile->end();
?>
<?php app\widgets\JS::begin(); ?>
<script type="text/javascript">
    $(function() {
        var chooser = $('#<?=$chooseAutomobile->id?>');
        chooser.on('change', 'input[type="checkbox"]', function(e) {
            var type = $(this).data('type');
            if (type != 'modification' && type) {
                var method = $(this).is(':checked') ? 'choose' : 'unchoose';
                method += type.charAt(0).toUpperCase() + type.substr(1, type.length - 1);
                chooser.chooseAutomobile(method, $(this).val());
            }
        });
    });
</script>
<?php app\widgets\JS::end(); ?>
