<?php
/**
 * Роутеры для фронтенда
 */

return [
    '/' => 'site/index',

    // выбор автомобилей
    '/auto/choose/<action:(\w+)>' => '/auto/choose-auto/<action>',

    // объявления
    '/ads/search' => '/advert/catalog/search',
    '/ads/details/<id:(\d+)>' => '/advert/catalog/details',

    // работа с объявлениями
    '/ads/append' => '/advert/advert/append',
    '/ads/confirmation/<code:(\w+)>' => '/advert/advert/confirm',

    // работа с пользователями
    '/registration' => '/user/user/register',
    '/registration/confirmation/<code:(\w+)>' => '/user/user/confirmation',
    '/login' => '/user/user/login',
    '/logout' => '/user/user/logout',
    '/restore/lost-password' => '/user/user/lost-password',
    '/restore/change-password/<code:(\w+)>' => '/user/user/change-password',

    // профиль
    '/profile' => '/user/profile/index',
    '/profile/change-email/<code:(\w+)>' => '/user/profile/change-email',
    '/profile/<action:(change-password|update-company-data|update-email|update-contact-data)>' => '/user/profile/<action>',

    // объявления пользователя
    '/my-ads' => '/advert/user-advert/list',
    '/my-ads/update-publication/<id:(\d+)>' => '/advert/user-advert/update-publication',
    '/my-ads/stop-publication/<id:(\d+)>' => '/advert/user-advert/stop-publication',
    '/my-ads/edit/<id:(\d+)>' => '/advert/user-advert/edit',
    '/my-ads/append' => '/advert/user-advert/append',
];