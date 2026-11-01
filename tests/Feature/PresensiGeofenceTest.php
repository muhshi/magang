<?php

namespace Tests\Feature;

use App\Livewire\Presensi;
use App\Models\User;
use App\Settings\SystemSettings;
use Tests\TestCase;

class PresensiGeofenceTest extends TestCase
{
    public function test_debug_routes_are_not_accessible(): void
    {
        $responseInfo = $this->get('/info');
        $this->assertEquals(404, $responseInfo->status());

        $responseDebug = $this->get('/debug-log-config');
        $this->assertEquals(404, $responseDebug->status());

        $responseTestWrite = $this->get('/test-write');
        $this->assertEquals(404, $responseTestWrite->status());
    }

    public function test_haversine_distance_calculation_is_accurate(): void
    {
        $component = new Presensi();
        // Office Demak: -6.894561, 110.637492
        // Same point should be 0 meters
        $distSame = $component->calculateDistance(-6.894561, 110.637492, -6.894561, 110.637492);
        $this->assertEqualsWithDelta(0, $distSame, 0.5);

        // Point roughly 500km away (Jakarta: -6.2088, 106.8456)
        $distJkt = $component->calculateDistance(-6.894561, 110.637492, -6.2088, 106.8456);
        $this->assertGreaterThan(400000, $distJkt); // > 400 km
    }
}
