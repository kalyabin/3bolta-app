<?php
/**
 * Вывести сообщение об успешном добавлении объявления
 */

use app\widgets\JS;
use yii\bootstrap\Modal;
use advert\models\Advert;

$model = null;
if ($id = Yii::$app->session->getFlash('advert_success_created')) {
    $model = Advert::find()->where(['id' => $id])->one();
}

/* @var $this yii\base\View */

if ($model instanceof Advert && !$model->user_id) {
    Modal::begin([
        'id' => 'advertWasCreatedModal',
        'header' => '<h2 class="primary-title"><span class="glyphicon glyphicon-info-sign"></span> ' . Yii::t('frontend/advert', 'Advert was created') . '</h2>',
        'toggleButton' => false,
    ]);
    ?>
    Ваше объявление успешно добавлено!
    <br /><br />
    Для того, чтобы это объявление было опубликовано
    и его смогли просматривать другие пользователи - мы отправили на ваш e-mail адрес
    <strong><?=$model->user_email?></strong> с ссылкой для подтверждения публикации.<br /><br />
    Пожалуйста, пройдите по этой ссылке и ваше объявление будет видно другим пользователям.
    <?php
    Modal::end();
    JS::begin();
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#advertWasCreatedModal').modal('toggle');
        });
    </script>
    <?php
    JS::end();
}