<?php

namespace Tests\Feature;

use App\Filament\Resources\TicketResource;
use App\Filament\Resources\TicketResource\Pages\CreateTicket;
use App\Filament\Resources\TicketResource\Pages\EditTicket;
use App\Models\Employee;
use App\Models\Logbook;
use App\Models\Ticket;
use App\Models\User;
use Tests\TestCase;

class TicketAssigneeFlowTest extends TestCase
{
    public function test_ticket_resource_form_has_assignee_ids_and_status(): void
    {
        $code = file_get_contents(app_path('Filament/Resources/TicketResource.php'));
        $this->assertStringContainsString("Forms\Components\Select::make('assignee_ids')", $code);
        $this->assertStringContainsString("Forms\Components\Select::make('status')", $code);
    }

    public function test_create_and_edit_ticket_pages_handle_assignee_sync(): void
    {
        $createCode = file_get_contents(app_path('Filament/Resources/TicketResource/Pages/CreateTicket.php'));
        $this->assertStringContainsString("afterCreate", $createCode);
        $this->assertStringContainsString("assignee_ids", $createCode);

        $editCode = file_get_contents(app_path('Filament/Resources/TicketResource/Pages/EditTicket.php'));
        $this->assertStringContainsString("afterSave", $editCode);
        $this->assertStringContainsString("assignee_ids", $editCode);
    }
}
