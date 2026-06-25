<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.dashboard', function ($view) {
            if (auth()->check()) {
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
                $view->with('unreadNotificationsCount', $unreadCount);
            } else {
                $view->with('unreadNotificationsCount', 0);
            }
        });
    }
}
