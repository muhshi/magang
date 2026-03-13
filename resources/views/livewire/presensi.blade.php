<div>
    <div class="presensi-wrapper">
        {{-- Header Card --}}
        <div class="presensi-card presensi-header-card">
            <div class="presensi-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <h1 class="presensi-title">Presensi Magang</h1>
            <p class="presensi-subtitle">Sistem Absensi Digital BPS Kabupaten Demak</p>
        </div>

        {{-- Info Peserta --}}
        <div class="presensi-card">
            <div class="presensi-info-grid">
                <div class="presensi-info-item">
                    <span class="presensi-info-label">Nama Peserta</span>
                    <span class="presensi-info-value">{{ Auth::user()->name }}</span>
                </div>
                <div class="presensi-info-item">
                    <span class="presensi-info-label">Kantor</span>
                    <span class="presensi-info-value">{{ $presensiData['officeName'] }}</span>
                </div>
                <div class="presensi-info-item">
                    <span class="presensi-info-label">Shift</span>
                    <span class="presensi-info-value">{{ $presensiData['shiftName'] }} ({{ $presensiData['workStart'] }} - {{ $presensiData['workEnd'] }}) WIB</span>
                </div>
                <div class="presensi-info-item">
                    <span class="presensi-info-label">Status</span>
                    @if ($presensiData['isWfa'])
                        <span class="presensi-status-badge presensi-status-wfa">WFA</span>
                    @elseif ($presensiData['isBanned'])
                        <span class="presensi-status-badge presensi-status-banned">Banned</span>
                    @else
                        <span class="presensi-status-badge presensi-status-wfo">WFO</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jam Datang / Pulang --}}
        <div class="presensi-time-row">
            <div class="presensi-card presensi-time-card">
                <span class="presensi-time-label">Jam Datang</span>
                <span class="presensi-time-value">{{ $attendance ? $attendance->start_time : '-' }}</span>
            </div>
            <div class="presensi-card presensi-time-card">
                <span class="presensi-time-label">Jam Pulang</span>
                <span class="presensi-time-value">{{ $attendance ? $attendance->end_time : '-' }}</span>
            </div>
        </div>

        @if (session()->has('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif

        {{-- Map & Presensi Button --}}
        @if (!$presensiData['isBanned'])
            <div class="presensi-card">
                <h2 class="presensi-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    Lokasi Presensi
                </h2>
                <div id="map" class="presensi-map" wire:ignore></div>

                @if (session()->has('error'))
                    <div class="presensi-error-alert">
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                @endif

                <form action="" class="presensi-actions" wire:submit="store" enctype="multipart/form-data">
                    <button type="button" onclick="tagLocation()" class="presensi-btn presensi-btn-tag">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                        </svg>
                        Tag Location
                    </button>
                    @if ($isWithinRadius)
                        <button type="submit" class="presensi-btn presensi-btn-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Submit Presensi
                        </button>
                    @endif
                </form>
            </div>
        @else
            <div class="presensi-card presensi-banned-card">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
                <p class="presensi-banned-text">Anda sedang diblokir dari sistem presensi.</p>
                <p class="presensi-banned-sub">Silakan hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        @endif
    </div>

    <style>
        .presensi-wrapper {
            max-width: 480px;
            margin: 8px auto;
            padding: 0 12px;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .presensi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        /* Header */
        .presensi-header-card {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            border: none;
            text-align: center;
            padding: 12px 10px;
            color: white;
            border-radius: 12px;
        }
        .presensi-header-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            backdrop-filter: blur(10px);
        }
        .presensi-header-icon svg {
            width: 20px;
            height: 20px;
        }
        .presensi-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 2px;
            letter-spacing: -0.3px;
        }
        .presensi-subtitle {
            font-size: 11px;
            opacity: 0.85;
            margin: 0;
        }

        /* Info Grid */
        .presensi-info-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .presensi-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
        }
        .presensi-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .presensi-info-label {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }
        .presensi-info-value {
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            max-width: 60%;
        }

        /* Status Badge */
        .presensi-status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .presensi-status-wfo {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .presensi-status-wfa {
            background: #dcfce7;
            color: #15803d;
        }
        .presensi-status-banned {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Time Cards */
        .presensi-time-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .presensi-time-card {
            text-align: center;
            padding: 8px;
            border-radius: 12px;
        }
        .presensi-time-label {
            display: block;
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 2px;
        }
        .presensi-time-value {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Section Title */
        .presensi-section-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 8px;
        }
        .presensi-section-title svg {
            width: 16px;
            height: 16px;
        }

        /* Map */
        .presensi-map {
            height: 100px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 8px;
            overflow: hidden;
        }

        /* Error Alert */
        .presensi-error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        /* Buttons */
        .presensi-actions {
            display: flex;
            gap: 6px;
        }
        .presensi-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .presensi-btn svg {
            width: 14px;
            height: 14px;
        }
        .presensi-btn-tag {
            background: #3b82f6;
            color: white;
        }
        .presensi-btn-tag:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }
        .presensi-btn-submit {
            background: #22c55e;
            color: white;
        }
        .presensi-btn-submit:hover {
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.35);
        }

        /* Banned Card */
        .presensi-banned-card {
            text-align: center;
            padding: 32px 20px;
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .presensi-banned-text {
            font-size: 15px;
            font-weight: 600;
            margin: 12px 0 4px;
        }
        .presensi-banned-sub {
            font-size: 13px;
            color: #b91c1c;
            margin: 0;
        }

        /* ======= DARK MODE ======= */
        .dark .presensi-card {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        .dark .presensi-header-card {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border: none;
        }
        .dark .presensi-info-item {
            border-bottom-color: #334155;
        }
        .dark .presensi-info-label {
            color: #94a3b8;
        }
        .dark .presensi-info-value {
            color: #e2e8f0;
        }
        .dark .presensi-status-wfo {
            background: #1e3a5f;
            color: #93c5fd;
        }
        .dark .presensi-status-wfa {
            background: #14532d;
            color: #86efac;
        }
        .dark .presensi-status-banned {
            background: #7f1d1d;
            color: #fca5a5;
        }
        .dark .presensi-time-label { color: #94a3b8; }
        .dark .presensi-time-value { color: #e2e8f0; }
        .dark .presensi-section-title { color: #e2e8f0; }
        .dark .presensi-map { border-color: #334155; }
        .dark .presensi-error-alert {
            background: #450a0a;
            border-color: #7f1d1d;
            color: #fca5a5;
        }
        .dark .presensi-banned-card {
            background: #450a0a;
            border-color: #7f1d1d;
            color: #fca5a5;
        }
        .dark .presensi-banned-sub { color: #fca5a5; }
    </style>

    @if (!$presensiData['isBanned'])
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            let map;
            let marker;
            let component;
            let lat, lng;
            const office = @json([$presensiData['officeLat'], $presensiData['officeLng']]);
            const radius = @json($presensiData['radius']);

            function initPresensiMap() {
                if (map) return; // Already initialized
                component = @this;
                map = L.map('map').setView(office, 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.circle(office, {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: radius
                }).addTo(map);
            }

            document.addEventListener('livewire:initialized', initPresensiMap);
            document.addEventListener('livewire:navigated', initPresensiMap);
            window.addEventListener('load', initPresensiMap);

            function tagLocation() {
                if (!map) initPresensiMap();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        lat = position.coords.latitude;
                        lng = position.coords.longitude;

                        if (marker && map) {
                            map.removeLayer(marker);
                        }

                        if (map) {
                            marker = L.marker([lat, lng]).addTo(map);
                            map.setView([lat, lng], 16);
                        }

                        if (isWithinRadius(lat, lng, office, radius)) {
                            component.set('isWithinRadius', true);
                            component.set('latitude', lat);
                            component.set('longitude', lng);
                        } else {
                            component.set('isWithinRadius', false);
                            alert("Anda berada di luar radius lokasi presensi.");
                        }

                    })
                } else {
                    alert("Tidak bisa tag location.");
                }
            }

            function isWithinRadius(lat, lng, center, radius) {
                const is_wfa = @json($presensiData['isWfa']);
                if (is_wfa) {
                    return true;
                } else {
                    if (!map) return false;
                    let distance = map.distance([lat, lng], center);
                    return distance <= radius;
                }
            }
        </script>
    @endif

</div>