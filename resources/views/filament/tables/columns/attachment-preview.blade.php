@php
    $attachment = $getRecord()->attachment;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment)
    <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1">
        @if($isImage)
            <img src="{{ $url }}" alt="preview" class="h-10 w-10 rounded object-cover border border-gray-200 dark:border-gray-700" />
        @else
            <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                <x-heroicon-o-document class="h-4 w-4" />
                {{ strtoupper($extension) }}
            </span>
        @endif
    </a>
@else
    <span class="text-gray-400 text-xs">-</span>
@endif
