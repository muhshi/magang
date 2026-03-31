<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke halaman Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/admin/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // User sudah ada — langsung login dengan role yang sudah ada
            Auth::login($user, remember: true);
        } else {
            // User baru — buat akun dan assign role Calon Magang
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'password'          => bcrypt(Str::random(24)), // random password (login via Google)
            ]);

            $user->assignRole('Calon Magang');

            Auth::login($user, remember: true);
        }

        return redirect('/admin');
    }
}
