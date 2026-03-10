<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-black dark:text-white">Presensi Magang</h2>

                    <div class="bg-gray-100 p-4 rounded-lg shadow-lg mt-4">
                        <p><strong>Nama Peserta:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Kantor : </strong>{{ $presensiData['officeName'] }}</p>
                        <p><strong>Shift :</strong> {{ $presensiData['shiftName'] }} ({{ $presensiData['workStart'] }} -
                            {{ $presensiData['workEnd'] }}) WIB
                        </p>
                        @if ($presensiData['isWfa'])
                            <p class="text-green-500"><strong>Status :</strong> WFA</p>
                        @elseif ($presensiData['isBanned'])
                            <p class="text-red-500"><strong>Status :</strong> Banned</p>
                        @else
                            <p><strong>Status :</strong> WFO</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-100 p-4 rounded-lg shadow-lg mt-4">
                            <h4 class="text-l font-bold mb-2"> Jam Datang</h4>
                            <p><strong>{{ $attendance ? $attendance->start_time : '-' }}</strong></p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow-lg mt-4">
                            <h4 class="text-l font-bold mb-2"> Jam Pulang</h4>
                            <p><strong>{{ $attendance ? $attendance->end_time : '-' }}</strong></p>
                        </div>
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

                @if (!$presensiData['isBanned'])
                    <div class="bg-gray-100 p-4 rounded-lg shadow-lg mt-4">
                        <h2 class="text-2xl font-semibold text-black dark:text-white"> Presensi </h2>
                        <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore></div>
                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif
                        <form action="" class="row g-3 mt-4" wire:submit="store" enctype="multipart/form-data">
                            <button type="button" onclick="tagLocation()"
                                class="px-4 py-2 bg-blue-500 text-white rounded">Tag
                                Location</button>
                            @if ($isWithinRadius)
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Submit</button>
                            @endif
                        </form>
                    </div>
                @else
                    <div class="bg-red-50 p-4 rounded-lg shadow-lg mt-4 text-center">
                        <p class="text-red-600 font-semibold">Anda sedang diblokir dari sistem presensi.</p>
                        <p class="text-gray-500 text-sm">Silakan hubungi admin.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

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