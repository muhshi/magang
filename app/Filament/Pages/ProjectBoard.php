<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Project;
use App\Models\Ticket;
<<<<<<< HEAD
use App\Models\TicketStatus;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
=======
>>>>>>> upstream/main
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ProjectBoard extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static string $view = 'filament.pages.project-board';

    protected static ?string $title = 'Project Board';

    protected static ?string $navigationLabel = 'Project Board';

    protected static ?string $navigationGroup = 'Project Visualization';
<<<<<<< HEAD
    protected static ?int $navigationSort = 1;
=======

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'project-board/{project_id?}';
>>>>>>> upstream/main

    public ?Project $selectedProject = null;

    public Collection $projects;

    public Collection $ticketStatuses;

    public ?Ticket $selectedTicket = null;

    public ?int $selectedProjectId = null;

<<<<<<< HEAD

    public function mount(): void
=======
    public function mount($project_id = null): void
>>>>>>> upstream/main
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (Auth::user()->roles[0]->name === 'super_admin') {
            $this->projects = Project::all();
        } else {
            $this->projects = $user->projects;
        }

<<<<<<< HEAD
        if ($this->projects->isNotEmpty()) {
            $firstId = $this->projects->first()->id;
            $this->selectedProjectId = $firstId;
            $this->selectProject($firstId);
=======
        if ($project_id && $this->projects->contains('id', $project_id)) {
            $this->selectedProjectId = (int) $project_id;
            $this->selectedProject = Project::find($project_id);
            $this->loadTicketStatuses();
        } elseif ($this->projects->isNotEmpty() && ! is_null($project_id)) {
            Notification::make()
                ->title('Project Not Found')
                ->danger()
                ->send();
            $this->redirect(static::getUrl());
>>>>>>> upstream/main
        }
    }

    public function selectProject(int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        $this->selectedTicket = null;
        $this->ticketStatuses = collect();
<<<<<<< HEAD

        $this->selectedProject = Project::find($projectId);

        $this->loadTicketStatuses();
    }

    public function loadTicketStatuses(): void
    {

        if (!$this->selectedProject) {
=======
        $this->selectedProjectId = $projectId;
        $this->selectedProject = Project::find($projectId);

        if ($this->selectedProject) {
            $url = static::getUrl(['project_id' => $projectId]);
            $this->redirect($url);

            $this->loadTicketStatuses();
        }
    }

    public function updatedSelectedProjectId($value): void
    {
        if ($value) {
            $this->selectProject((int) $value);
        } else {
            $this->selectedProject = null;
            $this->ticketStatuses = collect();

            $this->redirect(static::getUrl());
        }
    }

    public function loadTicketStatuses(): void
    {
        if (! $this->selectedProject) {
>>>>>>> upstream/main
            $this->ticketStatuses = collect();

            return;
        }

        $this->ticketStatuses = $this->selectedProject->ticketStatuses()
<<<<<<< HEAD
            ->with([
                'tickets' => function ($query) {
                    $query->with(['assignee', 'status'])
                        ->when(!auth()->user()->hasRole(['super_admin']), function ($q) {
                            $q->where('user_id', auth()->id());
                        })
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->orderBy('id')
=======
            ->with(['tickets' => function ($query) {
                $query->with(['assignee', 'status'])
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('sort_order')
>>>>>>> upstream/main
            ->get();
    }

    #[On('ticket-moved')]
    public function moveTicket($ticketId, $newStatusId): void
    {
        $ticket = Ticket::find($ticketId);

        if ($ticket && $ticket->project_id === $this->selectedProject?->id) {
            $ticket->update([
                'ticket_status_id' => $newStatusId,
            ]);

            $this->loadTicketStatuses();

            $this->dispatch('ticket-updated');

            Notification::make()
                ->title('Ticket Updated')
                ->success()
                ->send();
        }
    }

    #[On('refresh-board')]
    public function refreshBoard(): void
    {
        $this->loadTicketStatuses();
        $this->dispatch('ticket-updated');
    }

    public function showTicketDetails(int $ticketId): void
    {
        $ticket = Ticket::with(['assignee', 'status', 'project'])->find($ticketId);

<<<<<<< HEAD
        if (!$ticket) {
=======
        if (! $ticket) {
>>>>>>> upstream/main
            Notification::make()
                ->title('Ticket Not Found')
                ->danger()
                ->send();

            return;
        }

<<<<<<< HEAD
        $this->redirect(TicketResource::getUrl('view', ['record' => $ticketId]));
=======
        
        $url = TicketResource::getUrl('view', ['record' => $ticketId]);
        $this->js("window.open('{$url}', '_blank')");
>>>>>>> upstream/main
    }

    public function closeTicketDetails(): void
    {
        $this->selectedTicket = null;
    }

    public function editTicket(int $ticketId): void
    {
        $ticket = Ticket::find($ticketId);

<<<<<<< HEAD
        if (!$this->canEditTicket($ticket)) {
=======
        if (! $this->canEditTicket($ticket)) {
>>>>>>> upstream/main
            Notification::make()
                ->title('Permission Denied')
                ->body('You do not have permission to edit this ticket.')
                ->danger()
                ->send();

            return;
        }

        $this->redirect(TicketResource::getUrl('edit', ['record' => $ticketId]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_ticket')
                ->label('New Ticket')
                ->icon('heroicon-m-plus')
                ->visible(fn() => $this->selectedProject !== null && auth()->user()->hasRole(['super_admin']))
                ->url(fn(): string => TicketResource::getUrl('create', [
                    'project_id' => $this->selectedProject?->id,
                    'ticket_status_id' => $this->selectedProject?->ticketStatuses->first()?->id,
                ])),

            Action::make('refresh_board')
                ->label('Refresh Board')
                ->icon('heroicon-m-arrow-path')
                ->action('refreshBoard'),
        ];
    }

    private function canViewTicket(?Ticket $ticket): bool
    {
<<<<<<< HEAD
        if (!$ticket)
            return false;
=======
        if (! $ticket) {
            return false;
        }
>>>>>>> upstream/main

        return auth()->user()->hasRole(['super_admin'])
            || $ticket->user_id === auth()->id();
    }

    private function canEditTicket(?Ticket $ticket): bool
    {
<<<<<<< HEAD
        if (!$ticket)
            return false;
=======
        if (! $ticket) {
            return false;
        }
>>>>>>> upstream/main

        return auth()->user()->hasRole(['super_admin'])
            || $ticket->user_id === auth()->id();
    }

    private function canManageTicket(?Ticket $ticket): bool
    {
<<<<<<< HEAD
        if (!$ticket)
            return false;
=======
        if (! $ticket) {
            return false;
        }
>>>>>>> upstream/main

        return auth()->user()->hasRole(['super_admin'])
            || $ticket->user_id === auth()->id();
    }
<<<<<<< HEAD

    public static function canAccess(): bool
    {
        return !auth()->user()->hasRole(['Calon Magang']);
    }
=======
>>>>>>> upstream/main
}
