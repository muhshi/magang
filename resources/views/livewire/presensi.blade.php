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
            <div class="presensi-clock-container">
                <div id="presensiTime" class="presensi-clock-time">--:-- WIB</div>
                <div id="presensiDate" class="presensi-clock-date">Memuat waktu...</div>
            </div>
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
                    <span class="presensi-info-value">{{ $presensiData['shiftName'] }} ({{ substr($presensiData['workStart'], 0, 5) }} - {{ substr($presensiData['workEnd'], 0, 5) }}) WIB</span>
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
                <span class="presensi-time-value">{{ $attendance ? substr($attendance->start_time, 0, 5) . ' WIB' : '-' }}</span>
                @if ($attendance)
                    <span class="presensi-status-pill {{ $attendance->isLate() ? 'pill-late' : 'pill-ontime' }}">
                        {{ $attendance->isLate() ? '🔴 Terlambat' : '🟢 Tepat Waktu' }}
                    </span>
                @endif
            </div>
            <div class="presensi-card presensi-time-card">
                <span class="presensi-time-label">Jam Pulang</span>
                <span class="presensi-time-value">
                    {{ ($attendance && $attendance->hasCheckedOut()) ? substr($attendance->end_time, 0, 5) . ' WIB' : '-' }}
                </span>
                @if ($attendance)
                    @if ($attendance->hasCheckedOut())
                        <span class="presensi-status-pill pill-done">🏁 Selesai Pulang</span>
                    @else
                        <span class="presensi-status-pill pill-waiting">⏳ Belum Pulang</span>
                    @endif
                @endif
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

                <div id="distanceBox" class="presensi-distance-box hidden">
                    <span id="distanceText"></span>
                </div>

                @if (session()->has('error'))
                    <div class="presensi-error-alert">
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                @endif

                @if ($attendance && $attendance->hasCheckedOut())
                    <div class="presensi-completed-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="presensi-completed-icon">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="presensi-completed-text">
                            <strong>Presensi Hari Ini Lengkap</strong>
                            <p>Anda sudah melakukan presensi datang & pulang. Selamat beristirahat!</p>
                        </div>
                    </div>
                @else
                    <form action="" class="presensi-actions" wire:submit="store" enctype="multipart/form-data">
                        <button type="button" onclick="tagLocation()" class="presensi-btn presensi-btn-tag">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                            </svg>
                            Deteksi Lokasi Saya
                        </button>
                        @if ($isWithinRadius)
                            @if (!$attendance)
                                <button type="submit" class="presensi-btn presensi-btn-submit presensi-btn-in">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                        <polyline points="10 17 15 12 10 7"/>
                                        <line x1="15" y1="12" x2="3" y2="12"/>
                                    </svg>
                                    Presensi Masuk (Datang)
                                </button>
                            @else
                                <button type="submit" class="presensi-btn presensi-btn-submit presensi-btn-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Presensi Pulang
                                </button>
                            @endif
                        @endif
                    </form>
                @endif
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

        /* Clock */
        .presensi-clock-container {
            margin-top: 8px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 8px;
            display: inline-block;
        }
        .presensi-clock-time {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
            font-variant-numeric: tabular-nums;
        }
        .presensi-clock-date {
            font-size: 11px;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Status Pills */
        .presensi-status-pill {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
        }
        .pill-ontime {
            background: #dcfce7;
            color: #15803d;
        }
        .pill-late {
            background: #fee2e2;
            color: #b91c1c;
        }
        .pill-waiting {
            background: #fef3c7;
            color: #b45309;
        }
        .pill-done {
            background: #e0e7ff;
            color: #4338ca;
        }

        /* Distance Box */
        .presensi-distance-box {
            margin-bottom: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        .presensi-distance-box.hidden {
            display: none;
        }
        .presensi-distance-ok {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .presensi-distance-fail {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Completed Card */
        .presensi-completed-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 14px;
            color: #166534;
        }
        .presensi-completed-icon {
            flex-shrink: 0;
            color: #16a34a;
        }
        .presensi-completed-text strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .presensi-completed-text p {
            margin: 0;
            font-size: 11px;
            color: #15803d;
        }
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
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .presensi-btn-tag:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }
        .presensi-btn-in {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .presensi-btn-in:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .presensi-btn-out {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        .presensi-btn-out:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
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
        .dark .presensi-completed-card {
            background: #064e3b;
            border-color: #047857;
            color: #a7f3d0;
        }
        .dark .presensi-completed-text p {
            color: #6ee7b7;
        }
        .dark .presensi-distance-ok {
            background: #064e3b;
            border-color: #047857;
            color: #a7f3d0;
        }
        .dark .presensi-distance-fail {
            background: #450a0a;
            border-color: #7f1d1d;
            color: #fca5a5;
        }
        .dark .pill-ontime { background: #064e3b; color: #a7f3d0; }
        .dark .pill-late { background: #450a0a; color: #fca5a5; }
        .dark .pill-waiting { background: #451a03; color: #fcd34d; }
        .dark .pill-done { background: #1e1b4b; color: #a5b4fc; }
    </style>

    <script>
        function updatePresensiClock() {
            const timeEl = document.getElementById('presensiTime');
            const dateEl = document.getElementById('presensiDate');
            if (!timeEl || !dateEl) return;

            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            timeEl.textContent = `${hours}:${minutes} WIB`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('id-ID', options);
        }

        setInterval(updatePresensiClock, 1000);
        updatePresensiClock();
        document.addEventListener('livewire:navigated', updatePresensiClock);
    </script>

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

                const distBox = document.getElementById('distanceBox');
                const distText = document.getElementById('distanceText');

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

                        const isWfa = @json($presensiData['isWfa']);
                        const distance = Math.round(map.distance([lat, lng], office));

                        if (distBox && distText) {
                            distBox.classList.remove('hidden');
                            if (isWfa || distance <= radius) {
                                distBox.className = 'presensi-distance-box presensi-distance-ok';
                                distText.innerHTML = isWfa
                                    ? '📍 Status WFA Aktif: Lokasi presensi fleksibel.'
                                    : `🎯 Jarak Anda: <strong>${distance} meter</strong> dari kantor (Maks ${radius}m). Lokasi valid!`;
                            } else {
                                distBox.className = 'presensi-distance-box presensi-distance-fail';
                                distText.innerHTML = `⚠️ Jarak Anda: <strong>${distance} meter</strong> dari kantor (Maks ${radius}m). Di luar jangkauan presensi!`;
                            }
                        }

                        if (isWithinRadius(lat, lng, office, radius)) {
                            component.set('isWithinRadius', true);
                            component.set('latitude', lat);
                            component.set('longitude', lng);
                        } else {
                            component.set('isWithinRadius', false);
                            alert("Anda berada di luar radius lokasi presensi.");
                        }

                    }, (err) => {
                        alert("Gagal mendeteksi lokasi: " + err.message);
                    });
                } else {
                    alert("Browser Anda tidak mendukung geolokasi.");
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