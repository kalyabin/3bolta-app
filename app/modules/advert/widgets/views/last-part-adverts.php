<?php
/**
 * Вывод виджета последних объявлений
 */

use advert\models\PartIndex;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $list PartIndex[] */
?>
<div class="index-last-adverts">
    <div class="col-lg-12"><h3><?=Yii::t('main', 'Last adverts')?></h3></div>
    <?php foreach ($list as $model):?>
        <?php
        /* @var $model PartIndex */
        ?>
        <div class="col-lg-6 col-sm-12 col-md-6 list-item">
            <div class="panel panel-default list-item-internal">
                <div class="panel-body">
                    <div class="list-item-internal-desc <?php if (!empty($model->preview_url)):?>col-xs-8<?php else:?>col-xs-12<?php endif;?>">
                        <div class="list-item-title list-item-title-internal">
                            <h3><?=Html::a(
                                Html::encode($model->name),
                                Url::toRoute(['/advert/part-catalog/details', 'id' => $model->id])
                            )?></h3>
                        </div>
                        <div class="list-item-date">
                            <i class="publish-date">
                                <?=Yii::t('frontend/advert', 'Published at')?>
                                <?=$model->getPublishedFormatted()?>
                            </i>
                        </div>
                        <div class="list-item-row list-item-price">
                            <span class="label label-primary">
                                <span class="glyphicon glyphicon-ruble"></span>
                                <?=$model->getPriceFormated()?>
                            </span>
                        </div>
                        <div class="list-item-row list-item-seller-type">
                            <i class="icon icon-user"></i>
                            <?=$model->getSeller()?>
                        </div>
                    </div>
                    <?php if (!empty($model->preview_url)):?>
                        <div class="list-item-internal-preview col-xs-4">
                            <?=Html::img($model->preview_url)?>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>