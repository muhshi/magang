@php
    $attachment = $getRecord()->attachment;
    $url = $attachment ? asset('storage/' . $attachment) : null;
    $extension = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $isImage = in_array($extension, $imageExtensions);
@endphp

@if($attachment)
    @if($isImage)
        <a href="{{ $url }}" target="_blank" style="display:inline-block;line-height:0;">
            <img
                src="{{ $url }}"
                alt="preview"
                style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid rgba(128,128,128,0.2);display:block;"
            >
        </a>
    @else
        <a
            href="{{ $url }}"
            target="_blank"
            style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:500;color:#6b7280;text-decoration:none;"
            onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#6b7280'"
        >
            <span style="font-size:13px;">📎</span>
            <span style="text-transform:uppercase;letter-spacing:0.05em;">{{ $extension }}</span>
        </a>
    @endif
@else
    <span style="color:#9ca3af;font-size:14px;">—</span>
@endif
