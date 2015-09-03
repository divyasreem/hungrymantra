<?php
return array(
    'guest'=> array(
        'User\Controller\User-login',
        'User\Controller\User-create',
        'Category\Controller\Category-get',
        'User\Controller\User-get',
    ),
    'all' => array(
        'User\Controller\User-invalidAccess',
    ),
    'user' => array(
        'User\Controller\User-get',
        'User\Controller\User-update',
        'User\Controller\User-logout',
    )
);