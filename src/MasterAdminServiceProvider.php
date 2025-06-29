<?php

namespace Hongdev\MasterAdmin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Hongdev\MasterAdmin\Http\Middleware\MasterAdminMiddleware;

class MasterAdminServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any application services.
    }

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'master-admin');

        // Publish assets
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/master-admin'),
        ], 'public');

        // Đăng ký middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('master-admin', MasterAdminMiddleware::class);
    }
}