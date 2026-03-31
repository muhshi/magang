<x-filament-panels::page.simple>

    @if (session('error'))
        <div class="rounded-lg bg-danger-50 dark:bg-danger-950 p-4 mb-4">
            <div class="flex items-center gap-2">
                <x-heroicon-o-exclamation-circle class="h-5 w-5 text-danger-500" />
                <p class="text-sm font-medium text-danger-700 dark:text-danger-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ $this->registrationAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    {{-- Divider --}}
    <div class="relative flex items-center justify-center my-2">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200 dark:border-white/10"></div>
        </div>
        <div class="relative bg-white dark:bg-gray-900 px-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">atau</span>
        </div>
    </div>

    {{-- Google Login Button --}}
    <a href="{{ url('/auth/google/redirect') }}"
       class="w-full inline-flex items-center justify-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
              bg-white dark:bg-gray-800
              text-gray-700 dark:text-gray-200
              ring-1 ring-gray-300 dark:ring-white/20
              shadow-sm
              hover:bg-gray-50 dark:hover:bg-gray-700
              focus:outline-none focus:ring-2 focus:ring-primary-500
              transition duration-150 ease-in-out">
        {{-- Google Icon SVG --}}
        <svg class="h-5 w-5" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Masuk dengan Google
    </a>

</x-filament-panels::page.simple>
