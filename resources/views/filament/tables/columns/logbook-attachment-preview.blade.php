@php
    $attachment = $getRecord()->lampiran;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment)
    <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-1">
        @if($isImage)
            <img src="{{ $url }}" alt="preview" class="h-10 w-10 rounded object-cover border border-gray-200 dark:border-gray-700" style="width: 40px; height: 40px;" />
        @else
            <div class="h-10 w-10 rounded bg-gray-100 flex flex-col items-center justify-center p-1 border border-gray-200 dark:bg-gray-800 dark:border-gray-700" style="width: 40px; height: 40px;">
                <x-heroicon-o-document class="h-4 w-4 text-gray-500" style="width: 16px; height: 16px;" />
                <span class="text-[10px] font-bold">{{ strtoupper($extension) }}</span>
            </div>
        @endif
    </a>
@else
    <span class="text-gray-400 text-xs">-</span>
@endif
