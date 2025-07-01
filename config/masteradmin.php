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
                'text' => 'Settings',
                'icon' => 'bi bi-gear-fill',
                'url' => '/master-admin/settings',
                'active' => 'master-admin/settings*',
                'submenu' => [
                    [
                        'text' => 'Environment',
                        'icon' => 'bi bi-server',
                        'url' => '/master-admin/settings/environment',
                        'active' => 'master-admin/settings/environment*',
                    ],
                    [
                        'text' => 'Database',
                        'icon' => 'bi bi-database-fill',
                        'url' => '/master-admin/settings/database',
                        'active' => 'master-admin/settings/database*',
                    ],
                    [
                        'text' => 'Mail Configuration',
                        'icon' => 'bi bi-envelope',
                        'url' => '/master-admin/settings/mail',
                        'active' => 'master-admin/settings/mail*',
                    ],
                    [
                        'text' => 'Google Drive',
                        'icon' => 'bi bi-cloud-arrow-up',
                        'url' => '/master-admin/settings/drive',
                        'active' => 'master-admin/settings/drive*',
                    ],
                ],
            ],
            [
                'text' => 'Logs',
                'icon' => 'bi bi-file-text-fill',
                'url' => '/master-admin/logs/view',
                'active' => 'master-admin/logs*',
            ],
            [
                'text' => 'Backup',
                'icon' => 'bi bi-hdd-stack',
                'url' => '/master-admin/backup',
                'active' => 'master-admin/backup*',
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
        'prefix' => 'master-admin',
        'middleware' => ['web', 'master-admin'],
    ],

    'google' => [
        'driver' => 'google',
        'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folder' => env('GOOGLE_DRIVE_FOLDER'),
    ]
];
