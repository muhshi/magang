<?php

namespace Tests\Feature;

use App\Filament\Pages\RekapPresensi;
use App\Filament\Resources\LogbookResource;
use App\Filament\Resources\TicketResource;
use App\Filament\Resources\UserResource;
use Tests\TestCase;

class PerformanceQueryTest extends TestCase
{
    public function test_rekap_presensi_uses_batch_fetching(): void
    {
        $code = file_get_contents(app_path('Filament/Pages/RekapPresensi.php'));
        $this->assertStringContainsString("whereIn('user_id', \$userIds)", $code);
        $this->assertStringContainsString("groupBy('user_id')", $code);
    }

    public function test_ticket_resource_has_eager_loading(): void
    {
        $code = file_get_contents(app_path('Filament/Resources/TicketResource.php'));
        $this->assertStringContainsString("with(['employee', 'assignees'])", $code);
    }

    public function test_logbook_resource_has_eager_loading(): void
    {
        $code = file_get_contents(app_path('Filament/Resources/LogbookResource.php'));
        $this->assertStringContainsString("with(['assignees'])", $code);
    }

    public function test_certificate_controller_uses_dry_helper(): void
    {
        $code = file_get_contents(app_path('Http/Controllers/CertificateController.php'));
        $this->assertStringContainsString("buildCertificatePdfResponse", $code);
    }
}
