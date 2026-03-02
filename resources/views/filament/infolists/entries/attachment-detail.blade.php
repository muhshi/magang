@php
    $attachment = $getRecord()->attachment;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $filename = $attachment ? basename($attachment) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment && $url)
    <div class="space-y-3">
        @if($isImage)
            {{-- Image preview --}}
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ $url }}" alt="{{ $filename }}" class="max-h-64 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition" />
            </a>
        @else
            {{-- Document icon --}}
            <div class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $filename }}</p>
                    <p class="text-xs text-gray-500">{{ strtoupper($extension) }}</p>
                </div>
            </div>
        @endif

        {{-- Download button --}}
        <div>
            <a href="{{ $url }}" download="{{ $filename }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Unduh Lampiran
            </a>
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 ml-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Buka di Tab Baru
            </a>
        </div>
    </div>
@else
    <p class="text-sm text-gray-400 italic">Tidak ada lampiran</p>
@endif
