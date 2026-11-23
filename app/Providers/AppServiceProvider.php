<?php

namespace App\Providers;

use App\Support\PushOnNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middleware\PermissionMiddleware as MiddlewarePermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware as MiddlewareRoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Route::aliasMiddleware('role', MiddlewareRoleMiddleware::class);
        Route::aliasMiddleware('permission', MiddlewarePermissionMiddleware::class);

        // Member ko website notification mile to mobile app me push bhi (KisanMandi app API)
        Event::listen(NotificationSent::class, [PushOnNotification::class, 'handle']);
    }
}
