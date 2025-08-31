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
            'logo' => 'vendor/master-admin/assets/img/logoIT.png',
            'logo_mini' => 'vendor/master-admin/assets/img/logoIT.png',
            'route' => 'master-admin.dashboard',
        ],
        'theme' => [
            'dark_mode' => false,
            'class' => 'bg-body-tertiary shadow',
        ],
        'menu' => [
            [
                'text' => 'Dashboard',
                'icon' => 'bi bi-speedometer',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
            ],
            [
                'text' => 'Quản lý quản trị',
                'icon' => 'bi bi-shield-lock',
                'active' => 'admin/role*',
                        'submenu' => [
                            [
                                'text' => 'Quyền',
                                'icon' => 'bi bi-key',
                                'route' => 'admin.role.permission.index',
                                'active' => 'admin/role/permission*',
                            ],
                            [
                                'text' => 'Vai trò',
                                'icon' => 'bi bi-person-badge',
                                'route' => 'admin.role.role.index',
                                'active' => 'admin/role/role*',
                            ],
                            [
                                'text' => 'Quản trị viên',
                                'icon' => 'bi bi-people',
                                'route' => 'admin.role.admin.index',
                                'active' => 'admin/role/admin*',
                            ],
                        ],
            ],
        ],
    ],
];
