<?php

namespace Tests\Feature;

use App\Filament\Resources\AttendanceResource;
use App\Models\User;
use Tests\TestCase;

class RoleConsistencyTest extends TestCase
{
    public function test_user_without_roles_does_not_crash_resource_query(): void
    {
        $user = new User(['id' => 99999, 'name' => 'No Role User', 'email' => 'norole@example.com']);
        $this->actingAs($user);

        // AttendanceResource query should execute without Undefined array key 0
        $query = AttendanceResource::getEloquentQuery();
        $this->assertNotNull($query);
    }

    public function test_sso_controller_assigns_pegawai_bps_role(): void
    {
        $controllerContent = file_get_contents(app_path('Http/Controllers/Auth/SsoController.php'));
        $this->assertStringContainsString("assignRole('Pegawai BPS')", $controllerContent);
        $this->assertStringNotContainsString("assignRole('pegawai')", $controllerContent);
    }
}
