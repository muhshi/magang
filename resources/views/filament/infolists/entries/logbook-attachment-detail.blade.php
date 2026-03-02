@php
    $attachment = $getRecord()->lampiran;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment)
    <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-2">
        @if($isImage)
            <img src="{{ $url }}" alt="lampiran" class="h-32 rounded-lg border border-gray-200 dark:border-gray-700 object-cover" />
        @else
            <span class="inline-flex items-center gap-2 rounded-md bg-gray-100 dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                <x-heroicon-o-document class="h-5 w-5" />
                {{ strtoupper($extension) }}
            </span>
        @endif
    </a>
@else
    <span class="text-gray-400 text-sm">Tidak ada lampiran</span>
@endif
