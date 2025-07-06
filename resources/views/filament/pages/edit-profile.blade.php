<x-filament-panels::page>
    {{-- Form pertama untuk Profil --}}
    <form wire:submit.prevent="saveProfile">
        {{-- Ini akan merender form yang didefinisikan di method profileForm() --}}
        {{ $this->profileForm }}

        <div class="pt-4">
            <x-filament::button type="submit">
                Save Profile
            </x-filament::button>
        </div>
    </form>

    {{-- Garis pemisah untuk kerapian --}}
    <hr class="my-6 border-gray-200 dark:border-gray-700" />

    {{-- Form kedua untuk Password --}}
    <form wire:submit.prevent="savePassword">
        {{-- Ini akan merender form yang didefinisikan di method passwordForm() --}}
        {{ $this->passwordForm }}

        <div class="pt-4">
            <x-filament::button type="submit">
                Save Password
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
