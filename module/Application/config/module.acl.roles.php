<?php
return array(
    'guest'=> array(
        'User\Controller\User-login',
        'User\Controller\User-create',
        'Category\Controller\Category-get',
        'Category\Controller\Item-categoryItems',
        'Category\Controller\Item-get',
    ),
    'all' => array(
        'User\Controller\User-invalidAccess',
        'Category\Controller\Category-get',
        'Category\Controller\Item-get',
        'User\Controller\User-update',
        'Category\Controller\Item-categoryItems',
    ),
    'user' => array(
        'User\Controller\User-get',
        'User\Controller\User-update',
        'User\Controller\User-logout',
        'Category\Controller\Cart-create',
        'Category\Controller\Cart-get',
        'Category\Controller\Cart-delete',
        'Category\Controller\Transaction-create',
        'Category\Controller\Transaction-update',
        'Category\Controller\Transaction-get',
        'Category\Controller\Cart-getSavedItemsLoggedUser',
        'Category\Controller\Cart-deleteAllCartItems',
        'Category\Controller\Transaction-getOrderDetails',
        'Category\Controller\Transaction-getOrders'
    ),
    'vendor' => array(
        'Category\Controller\Category-create',
        'User\Controller\User-update',
        'Category\Controller\Category-update',
        'Category\Controller\Category-delete',
        'Category\Controller\Item-create',
        'Category\Controller\Item-update',
        'Category\Controller\Cart-create',
        'Category\Controller\Cart-get',
        'Category\Controller\Cart-delete',
        'User\Controller\User-logout',
        'User\Controller\User-get',
        'Category\Controller\Transaction-create',
        'Category\Controller\Transaction-update',
        'Category\Controller\Transaction-getOrderDetails',
        'Category\Controller\Transaction-get',
        'Category\Controller\Cart-getSavedItemsLoggedUser',
        'Category\Controller\Cart-deleteAllCartItems',
        'Category\Controller\Transaction-getOrdersInprogress',
        'Category\Controller\Transaction-getOrders',
        'User\Controller\User-walletRecharge'
    )
);