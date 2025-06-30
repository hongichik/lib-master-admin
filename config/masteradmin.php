<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the admin sidebar menu, icons, routes, and permissions.
    |
    */
    'sidebar' => [
        'brand' => [
            'text' => 'Master Admin',
            'logo' => 'vendor/master-admin/assets/img/AdminLTELogo.png',
            'logo_mini' => 'vendor/master-admin/assets/img/AdminLTELogo.png',
            'url' => '/master-admin',
        ],
        'theme' => [
            'dark_mode' => false,
            'class' => 'bg-body-tertiary shadow',
        ],
        'menu' => [
            [
                'text' => 'Dashboard',
                'icon' => 'bi bi-speedometer',
                'url' => '/master-admin',
                'active' => 'master-admin',
            ],
            [
                'text' => 'Users',
                'icon' => 'bi bi-people-fill',
                'submenu' => [
                    [
                        'text' => 'All Users',
                        'icon' => 'bi bi-circle',
                        'url' => '/admin/users',
                    ],
                ],
            ],
            [
                'text' => 'Settings',
                'icon' => 'bi bi-gear-fill',
                'submenu' => [
                    [
                        'text' => 'General',
                        'icon' => 'bi bi-circle',
                        'url' => '/admin/settings/general',
                    ],
                    [
                        'text' => 'Security',
                        'icon' => 'bi bi-circle',
                        'url' => '/admin/settings/security',
                    ],
                ],
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the admin route prefix and middleware.
    |
    */
    'route' => [
        'prefix' => 'admin',
        'middleware' => ['web', 'auth', 'master-admin'],
    ],
];
