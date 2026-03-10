<div>
    @php
        $settings = app(\App\Settings\SystemSettings::class);
        $templatePath = $settings->certificate_template_path ?? 'images/TEMPLATE.png';

        // Check if file exists in public disk (storage/app/public)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($templatePath)) {
            $imageUrl = asset('storage/' . $templatePath);
        } else {
            // Fallback: check in public directory directly
            $imageUrl = asset($templatePath);
        }
    @endphp

    <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <img src="{{ $imageUrl }}" alt="Template Sertifikat Aktif"
             style="width: 100%; height: auto; display: block;">
    </div>
    <p style="font-size: 12px; color: #64748b; margin-top: 6px;">
        File: <code>{{ $templatePath }}</code>
    </p>
</div>
