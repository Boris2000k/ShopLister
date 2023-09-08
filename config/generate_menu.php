<?php

return [
    'User Management' => [
        'type'       => 'group',
        'icon'       => 'fas fa-chart-pie',
        'list_items' => [
            'Permissions' => [
                'permission_required' => 'user-permissions-management',
                'route'               => 'user-management.permissions.index',
            ],
        ],
    ],
    'Shop Management' => [
        'type'       => 'group',
        'icon'       => 'fa-solid fa-shop',
        'list_items' => [
            'My Stores' => [
                'permission_required' => 'shop-owner',
                'route'               => 'shop-management.index',
            ],
            'My Products' => [
                'permission_required' => 'product-management',
                'route' => 'product-management.index',
            ]
        ],
    ],
    'Shopping' => [
        'show_all' => true,
        'type'       => 'group',
        'icon'       => 'fa-solid fa-cart-shopping',
        'list_items' => [
            'Stores' => [
                'route' => 'shops.index',
            ],
            'Shopping Carts' => [
                'route' => 'shopping-carts.index',
            ],
            'My Statistics' => [
                'route' => 'shopping-carts.statistics',
            ]

        ],

    ],

    'Logout' => [
        'show_all' => true,
        'type'     => 'single',
        'icon'     => 'fa fa-sign-out-alt',
        'route'    => 'home.logout',
    ],
];
