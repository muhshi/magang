<?php

namespace App\Providers;

use App\Filament\Resources\TicketResource\Pages\EditCommentModal;
use App\Http\Responses\SsoLogoutResponse;
use App\Models\User;
use App\Observers\UserObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (interface_exists(LogoutResponse::class)) {
            $this->app->singleton(LogoutResponse::class, SsoLogoutResponse::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https'); // paksa semua route & asset pakai HTTPS
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id', 'en']) // Ganti dengan kode bahasa yang diinginkan
                ->visible(outsidePanels: true);
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('super_admin');
        });
        // Livewire::component('edit-comment-modal', EditCommentModal::class);

        User::observe(UserObserver::class);

        // Registrasi SIPETRA SSO Socialite Driver
        $socialite = $this->app->make(Factory::class);
        $socialite->extend('sipetra', function ($app) use ($socialite) {
            $config = $app['config']['services.sipetra'];

            return $socialite->buildProvider(SipetraSocialiteProvider::class, $config);
        });
    }
}
