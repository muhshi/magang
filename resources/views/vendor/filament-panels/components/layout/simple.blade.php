@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <style>
        .page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb);
            padding: 1rem;
        }
        .split-layout-card { 
            display: flex; 
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden; 
        }
        .split-layout-left { 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            flex: 1; 
            padding: 3rem 1.5rem; 
        }
        .split-layout-right { 
            display: none; 
            flex: 1; 
            position: relative; 
            align-items: center; 
            justify-content: center; 
            background-color: #ffffff;
        }
        .split-layout-right-bg { 
            position: absolute; 
            top: 0; right: 0; bottom: 0; left: 0;
            background-color: #ffffff; 
        }
        
        /* Dark mode support */
        :is(.dark .page-wrapper) { background: linear-gradient(to bottom right, #111827, #0f172a); }
        :is(.dark .split-layout-card) { background-color: #1f2937; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        :is(.dark .split-layout-right) { background-color: #1f2937; }
        :is(.dark .split-layout-right-bg) { background: linear-gradient(to bottom right, #1f2937, #111827); }

        @media (min-width: 1024px) {
            .split-layout-left { 
                flex: none; 
                width: 50%; 
                padding: 4rem 5rem; 
            }
            .split-layout-right { 
                display: flex; 
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="split-layout-card">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

            <!-- Left Side: Auth Form -->
            <div class="split-layout-left">
                <div style="margin: 0 auto; width: 100%; max-width: 24rem;">
                    <!-- Notifications and User Menu if authenticated -->
                    @if (($hasTopbar ?? true) && filament()->auth()->check())
                        <div style="margin-bottom: 1rem; display: flex; justify-content: flex-end; gap: 1rem;">
                            @if (filament()->hasDatabaseNotifications())
                                @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                                    'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                                    'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                                ])
                            @endif

                            @if (filament()->hasUserMenu())
                                @livewire(Filament\Livewire\SimpleUserMenu::class)
                            @endif
                        </div>
                    @endif
                    
                    <main>
                        {{ $slot }}
                    </main>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
                </div>
            </div>

            <!-- Right Side: Logo Display -->
            <div class="split-layout-right">
                <div class="split-layout-right-bg"></div>
                
                <img src="{{ asset('landing-assets/img/logo2.png') }}" 
                     alt="Mangga Muda Large Logo"
                     style="position: relative; z-index: 10; max-width: 80%; max-height: 500px; object-fit: contain; filter: drop-shadow(0 25px 25px rgba(0,0,0,0.15)); transition: transform 0.5s ease;"
                     onmouseover="this.style.transform='scale(1.05)'"
                     onmouseout="this.style.transform='scale(1)'">
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
        </div>
    </div>
</x-filament-panels::layout.base>
