<?php
return array(
    'guest'=> array(
        'User\Controller\User-login',
        'User\Controller\User-create',
        'User\Controller\User-get',
    ),
    'all' => array(
        'User\Controller\User-invalidAccess',
        'Category\Controller\Category-get',
        'Category\Controller\Item-get',
    ),
    'user' => array(
        'User\Controller\User-get',
        'User\Controller\User-update',
        'User\Controller\User-logout',
    ),
    'vendor' => array(
        'Category\Controller\Category-create',
        'Category\Controller\Category-update',
        'Category\Controller\Item-create',
        'Category\Controller\Item-update',
    )
);