<?php

namespace Tests\Feature;

use App\Filament\Resources\TicketResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Internship;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class PaketATest extends TestCase
{
    public function test_interns_update_status_command_exists_and_registered(): void
    {
        $consoleRoutes = file_get_contents(base_path('routes/console.php'));
        $this->assertStringContainsString('interns:update-status', $consoleRoutes);
    }

    public function test_ticket_resource_has_auto_select_employee_default(): void
    {
        $code = file_get_contents(app_path('Filament/Resources/TicketResource.php'));
        $this->assertStringContainsString("Employee::where('name'", $code);
    }

    public function test_ticket_resource_navigation_badge_is_role_aware(): void
    {
        $code = file_get_contents(app_path('Filament/Resources/TicketResource.php'));
        $this->assertStringContainsString("hasRole('Magang BPS')", $code);
    }

    public function test_attendance_model_has_checked_out_method(): void
    {
        $attendance = new Attendance([
            'start_time' => '08:00:00',
            'end_time'   => '08:00:00',
        ]);
        $this->assertFalse($attendance->hasCheckedOut());

        $attendance->end_time = '16:00:00';
        $this->assertTrue($attendance->hasCheckedOut());
    }
}
