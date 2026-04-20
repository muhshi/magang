@php
    $attachment = $getRecord()->attachment;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

<div class="flex items-center">
    @if($attachment)
        <x-filament::link
            :href="$url"
            target="_blank"
            color="gray"
        >
            <div class="flex items-center gap-2">
                @if($isImage)
                    <div class="h-9 w-9 overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                        <img 
                            src="{{ $url }}" 
                            alt="preview" 
                            class="h-full w-full object-cover"
                        />
                    </div>
                @else
                    <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="h-5 w-5 text-gray-400"
                        />
                    </div>
                @endif
                
                @if(!$isImage)
                    <span class="text-xs font-bold text-gray-500 uppercase">{{ $extension }}</span>
                @endif
            </div>
        </x-filament::link>
    @else
        <span class="text-gray-400 text-xs">—</span>
    @endif
</div>
