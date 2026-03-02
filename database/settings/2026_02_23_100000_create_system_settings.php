<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('system.default_office_name', 'BPS Kabupaten Demak');
        $this->migrator->add('system.default_office_lat', -6.894561);
        $this->migrator->add('system.default_office_lng', 110.637492);
        $this->migrator->add('system.default_geofence_radius_m', 100);
        $this->migrator->add('system.default_work_start', '08:00');
        $this->migrator->add('system.default_work_end', '16:00');
        $this->migrator->add('system.default_workdays', [1, 2, 3, 4, 5]);
    }
};
