<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        //
        Schema::defaultStringLength(191);
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::except(['/stripe/webhook']);
        Paginator::useBootstrap();

        View::composer('components.user-notification', function ($view) {
            $user = Auth::user();
            if ($user) {
                $userNotifications = $user->userNotifications()->where('deleted', 0)->latest()->get();
                $notificationCount = $userNotifications->where('read', 0)->where('deleted', 0)->count();
                $view->with(compact('userNotifications', 'notificationCount'));
            }
        });
    }
}
