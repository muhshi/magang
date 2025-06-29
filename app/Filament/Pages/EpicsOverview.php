<?php

namespace App\Filament\Pages;

use App\Models\Epic;
use App\Models\Project;
use App\Models\Ticket;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class EpicsOverview extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static string $view = 'filament.pages.epics-overview';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?string $navigationLabel = 'Epics';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?int $navigationSort = 3;

    // UPDATED: Add URL parameter support
    protected static ?string $slug = 'epics-overview/{project_id?}';

    public Collection $epics;

    public array $expandedEpics = [];

    public ?int $selectedProjectId = null;

    public Collection $availableProjects;

    // UPDATED: Handle project_id from URL
    public function mount($project_id = null): void
    {
        $this->loadAvailableProjects();

        // Handle project_id from URL
        if ($project_id && $this->availableProjects->contains('id', $project_id)) {
            $this->selectedProjectId = (int) $project_id;
        } elseif ($project_id && !$this->availableProjects->contains('id', $project_id)) {
            // Project not found or user doesn't have access
            Notification::make()
                ->title('Project Not Found')
                ->body('The selected project was not found or you do not have access to it.')
                ->danger()
                ->send();
            $this->redirect(static::getUrl());
        }


        $this->loadEpics();
        $this->expandedEpics = $this->epics->pluck('id')->toArray();
    }

    public function loadAvailableProjects(): void
    {
        $user = Auth::user();

        if (Auth::user()->roles[0]->name !== 'super_admin') {
            $this->availableProjects = Project::orderBy('name')->get();
        } else {
            $this->availableProjects = Project::orderBy('name')->get();
        }
    }

    public function loadEpics(): void
    {
        $query = Epic::with([
            'project',
            'project',
            'tickets' => function ($query) {
                $query->with(['status', 'assignees', 'creator']);
            },
        ])
            ->orderBy('start_date', 'asc');



        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }


        $this->epics = $query->get();
    }

    // UPDATED: Handle project selection and update URL
    public function updatedSelectedProjectId($value): void
    {
        $this->selectedProjectId = $value ? (int) $value : null;

        if ($this->selectedProjectId) {
            // Redirect to URL with project_id
            $url = static::getUrl(['project_id' => $this->selectedProjectId]);
            $this->redirect($url);
        } else {
            // Redirect to URL without project_id
            $this->redirect(static::getUrl());
        }

        $this->loadEpics();
        $this->expandedEpics = $this->epics->pluck('id')->toArray();
    }

    public function toggleEpic(int $epicId): void
    {
        if (in_array($epicId, $this->expandedEpics)) {
            $this->expandedEpics = array_diff($this->expandedEpics, [$epicId]);
        } else {
            $this->expandedEpics[] = $epicId;
        }
    }

    public function isExpanded(int $epicId): bool
    {
        return in_array($epicId, $this->expandedEpics);
    }


    // Helper method to get ticket statistics for an epic
    public function getEpicStats(Epic $epic): array
    {
        $tickets = $epic->tickets;
        $totalTickets = $tickets->count();

        if ($totalTickets === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'todo' => 0,
                'progress_percentage' => 0,
            ];
        }

        $completed = $tickets->filter(function ($ticket) {
            return in_array($ticket->status?->name, ['Done', 'Completed', 'Closed']);
        })->count();

        $inProgress = $tickets->filter(function ($ticket) {
            return in_array($ticket->status?->name, ['In Progress', 'Review']);
        })->count();

        $todo = $tickets->filter(function ($ticket) {
            return in_array($ticket->status?->name, ['To Do', 'Open', 'New']);
        })->count();

        return [
            'total' => $totalTickets,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'todo' => $todo,
            'progress_percentage' => $totalTickets > 0 ? round(($completed / $totalTickets) * 100) : 0,
        ];
    }

    // Helper method to get assignees display for a ticket
    public function getTicketAssigneesDisplay($ticket): string
    {
        if ($ticket->assignees->isEmpty()) {
            return 'Unassigned';
        }

        $names = $ticket->assignees->pluck('name')->toArray();

        if (count($names) <= 2) {
            return implode(', ', $names);
        }

        return $names[0] . ', ' . $names[1] . ' +' . (count($names) - 2) . ' more';
    }


    #[On('epic-created')]
    #[On('epic-updated')]
    #[On('epic-deleted')]
    #[On('ticket-created')]
    #[On('ticket-updated')]
    #[On('ticket-deleted')]
    public function refreshEpics(): void
    {
        $this->loadEpics();


        $currentEpicIds = $this->epics->pluck('id')->toArray();
        $this->expandedEpics = array_intersect($this->expandedEpics, $currentEpicIds);


        Notification::make()
            ->title('Data refreshed')
            ->success()
            ->send();
    }
}
