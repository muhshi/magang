<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    public string $default_office_name;
    public float $default_office_lat;
    public float $default_office_lng;
    public int $default_geofence_radius_m;
    public string $default_work_start;
    public string $default_work_end;
    public array $default_workdays;
    public string $kepala_bps_name;
    public string $kepala_bps_nip;

    public static function group(): string
    {
        return 'system';
    }
}
