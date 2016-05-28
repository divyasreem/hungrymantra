<?php
namespace Category;

return array(
    'router' => array(
        'routes' => array(
            'category' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/category[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Controller\Category',
                    ),
                ),
            ),
            'item' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/item[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Controller\Item',
                    ),
                ),
            ),
            'categoryItems' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/categoryItems',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Item',
                        'action'        => 'categoryItems',
                    ),
                ),
            ),
            'cart' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cart[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Controller\Cart',
                    ),
                ),
            ),
            'getSavedItemsLoggedUser'  => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/getSavedItemsLoggedUser',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Cart',
                        'action'        => 'getSavedItemsLoggedUser',
                    ),
                ),
            ),
            'deleteAllCartItems' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deleteAllCartItems',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Cart',
                        'action'        => 'deleteAllCartItems',
                    ),
                ),
            ),
            'transaction' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/transaction[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Controller\Transaction',
                    ),
                ),
            ),
            'getOrderDetails' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/getOrderDetails',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Transaction',
                        'action'        => 'getOrderDetails',
                    ),
                ),
            ),
            'getOrders' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/getOrders',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Transaction',
                        'action'        => 'getOrders',
                    ),
                ),
            ),
            'getOrdersInprogress'=> array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/getOrdersInprogress',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Transaction',
                        'action'        => 'getOrdersInprogress',
                    ),
                ),
            ),
            'deleteAllCartItems'=> array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deleteAllCartItems',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Category\Controller',
                        'controller'    => 'Cart',
                        'action'        => 'deleteAllCartItems',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Category\Controller\Category'   => 'Category\Controller\CategoryController',
            'Category\Controller\Item'       => 'Category\Controller\ItemController',
            'Category\Controller\Cart'       => 'Category\Controller\CartController',
            'Category\Controller\Transaction'=> 'Category\Controller\TransactionController',
            'User\Controller\AbstractRestfulJsonController' => '..\User\Controller\AbstractRestfulJsonController',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    )
);
