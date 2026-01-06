<?php

namespace App\Providers;

use App\Filament\Resources\TicketResource\Pages\EditCommentModal;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Internship;
use App\Observers\InternshipObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

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

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id', 'en']) // Ganti dengan kode bahasa yang diinginkan
                ->visible(outsidePanels: true);
        });

        Gate::define('viewPulse', function (User $user) {
            return Auth::user()->roles[0]->name === 'super_admin';
        });

        // Super Admin Bypass
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
        //Livewire::component('edit-comment-modal', EditCommentModal::class);

        User::observe(UserObserver::class);
    }
}
