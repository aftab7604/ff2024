<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // if (request()->isSecure() || env('FORCE_HTTPS', true)) {
        //     URL::forceScheme('https');
        // }

        $router = $this->app['router'];

        $router->aliasMiddleware('auth.admin', AdminMiddleware::class);
        $router->aliasMiddleware('auth.user', UserMiddleware::class);

        View::composer('admin.includes.header', function($view) {
            $view->with('user', Auth::user());
        });

        View::composer('user.includes.header', function($view) {
            $view->with('user', Auth::user());
        });
    }
}
