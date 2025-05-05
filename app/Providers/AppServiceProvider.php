<?php

namespace App\Providers;

use App\Observers\UserObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        if (app()->environment('production')) {
            URL::forceScheme('https'); // paksa semua route & asset pakai HTTPS
        }

        Gate::define('viewPulse', function (User $user) {
            return Auth::user()->roles[0]->name === 'super_admin';
        });
        //Livewire::component('edit-comment-modal', EditCommentModal::class);

        User::observe(UserObserver::class);
    }
}
