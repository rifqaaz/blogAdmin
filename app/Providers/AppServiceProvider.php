<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
    public function boot(Router $router): void
    {
        Paginator::useBootstrapFive(); // Forces Bootstrap 5 styles
        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);

        $protectedRoles = ['admin', 'editor', 'user'];

        Blade::directive('protected', function($expression) use ($protectedRoles) {
            return "<?php if (in_array($expression, " . var_export($protectedRoles, true) . ")) : ?>";
        });

        Blade::directive('endprotected', function() {
            return "<?php endif; ?>";
        });

        Blade::directive('notprotected', function ($expression) use ($protectedRoles) {
            return "<?php if (!in_array($expression, " . var_export($protectedRoles, true) . ")) : ?>";
        });
        
        Blade::directive('endnotprotected', function () {
            return "<?php endif; ?>";
        });
    }
}
