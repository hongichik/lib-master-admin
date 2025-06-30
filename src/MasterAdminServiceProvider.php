<?php

namespace Hongdev\MasterAdmin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Hongdev\MasterAdmin\Http\Middleware\MasterAdminMiddleware;

class MasterAdminServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../config/masteradmin.php', 'masteradmin');
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

        // Publish config
        $this->publishes([
            __DIR__.'/../config/masteradmin.php' => config_path('masteradmin.php'),
        ], 'config');

        // Đăng ký middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('master-admin', MasterAdminMiddleware::class);

        // Ensure the middleware group has session handling
        Route::middlewareGroup('master-admin', [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Hongdev\MasterAdmin\Http\Middleware\MasterAdminMiddleware::class,
        ]);
    }
}