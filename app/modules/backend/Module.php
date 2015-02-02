<?php
namespace app\modules\backend;

use Yii;
use app\modules\handbook\models\Handbook;

/**
 * Модуль для бекенда
 */
class Module extends \yii\base\Module
{
    /**
     * Генерация левого меню для бекенда
     * @return []
     */
    public function getMenu()
    {
        $user = Yii::$app->user;

        // вывод всех справочников
        $handbookMenu = [];
        $res = Handbook::find()->all();
        foreach ($res as $i) {
            $handbookMenu[] = [
                'label' => $i->name,
                'icon' => '',
                'url' => ['/handbook/handbook-value-backend/index', 'code' => $i->code],
                'visible' => $user->can('backendViewHandbookValues'),
                'active' => Yii::$app->controller->id == 'handbook-value-backend',
            ];
        }

        return [
            [
                'label' => Yii::t('backend', 'Users'),
                'icon' => '',
                'visible' => $user->can('backendViewUser') || $user->can('backendRoleAdmin'),
                'options'=>['class'=>'treeview'],
                'active' => !empty(Yii::$app->controller->module) && Yii::$app->controller->module->id == 'user',
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Users list'),
                        'icon' => '',
                        'url' => ['/user/user-backend/index'],
                        'visible' => $user->can('backendViewUser'),
                        'active' => Yii::$app->controller->id == 'user-backend',
                    ],
                    [
                        'label' => Yii::t('backend', 'Roles list'),
                        'icon' => '',
                        'url' => ['/user/role-backend/index'],
                        'visible' => $user->can('backendRoleAdmin'),
                        'active' => Yii::$app->controller->id == 'role-backend',
                    ]
                ]
            ],
            [
                'label' => Yii::t('backend', 'Advert categories'),
                'icon' => '',
                'url' => ['/advert/category-backend/index'],
                'visible' => $user->can('backendViewAdvertCategory'),
                'options'=>[],
                'active' => !empty(Yii::$app->controller->module) && Yii::$app->controller->module->id == 'advert' && Yii::$app->controller->id == 'category-backend',
            ],
            [
                'label' => Yii::t('backend', 'Handbook'),
                'icon' => '',
                'visible' => $user->can('backendViewHandbookValues'),
                'options'=>['class'=>'treeview'],
                'active' => !empty(Yii::$app->controller->module) && Yii::$app->controller->module->id == 'handbook',
                'items' => $handbookMenu,
            ],
            [
                'label' => Yii::t('backend', 'File storage'),
                'icon' => '',
                'url' => ['/storage/storage-backend/index'],
                'visible' => $user->can('backendViewFile'),
                'options'=>[],
                'active' => !empty(Yii::$app->controller->module) && Yii::$app->controller->module->id == 'advert' && Yii::$app->controller->id == 'category-backend',
            ],
        ];
    }
}