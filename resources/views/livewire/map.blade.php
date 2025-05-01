<div class="grid grid-cols-1 md:grid-cols-12 gap-4 overflow-x-hidden" wire:ignore>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div id="map" class="w-full rounded-lg overflow-hidden" style="height: 75vh;"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('livewire:initialized', function() {
            component = @this;
            const office = @json([$schedule->office->latitude, $schedule->office->longitude]);
            const radius = @json($schedule->office->radius);

            let map = L.map('map').setView([{{ $schedule->office->latitude }},
                {{ $schedule->office->longitude }}
            ], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const markers = @json($attendance);
            console.log(markers);

            markers.forEach(marker => {
                const str = `Nama : ${marker.user.name} <br> Jam Masuk : ${marker.start_time} <br> Jam Keluar : ${marker.end_time}`;
                L.marker([marker.start_latitude, marker.start_longitude]).addTo(map).bindPopup(str);
            });

            const circle = L.circle(office, {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
        })
    </script>

</div>
