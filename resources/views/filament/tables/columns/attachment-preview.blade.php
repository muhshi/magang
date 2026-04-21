@php
    $attachment = $getRecord()->attachment;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment)
    @if($isImage)
        <a href="{{ $url }}" target="_blank" class="inline-block">
            <img
                src="{{ $url }}"
                alt="preview"
                style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(0,0,0,0.08); display: block;"
            >
        </a>
    @else
        <a
            href="{{ $url }}"
            target="_blank"
            class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
            </svg>
            <span class="uppercase">{{ $extension }}</span>
        </a>
    @endif
@else
    <span class="text-gray-400 dark:text-gray-600 text-sm">—</span>
@endif
