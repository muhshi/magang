<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketComment;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Components\ViewEntry;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected static ?string $title = 'Detail Penugasan';

    public ?int $editingCommentId = null;

    protected function getHeaderActions(): array
    {
        $canComment = auth()->user()->hasRole(['super_admin', 'pembimbing', 'Pegawai BPS', 'Magang BPS']);

        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing', 'Pegawai BPS'])),

            Actions\Action::make('addComment')
                ->label('Tambah Komentar')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->form([
                    RichEditor::make('comment')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $ticket = $this->getRecord();

                    $ticket->comments()->create([
                        'user_id' => auth()->id(),
                        'comment' => $data['comment'],
                    ]);

                    Notification::make()
                        ->title('Komentar berhasil ditambahkan')
                        ->success()
                        ->send();
                })
                ->visible($canComment),
        ];
    }

    public function handleEditComment($id)
    {
        $comment = TicketComment::find($id);

        if (! $comment) {
            Notification::make()
                ->title('Komentar tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        if (! auth()->user()->hasRole(['super_admin']) && $comment->user_id !== auth()->id()) {
            Notification::make()
                ->title('Anda tidak memiliki izin untuk mengedit komentar ini')
                ->danger()
                ->send();

            return;
        }

        $this->editingCommentId = $id;
        $this->mountAction('editComment', ['commentId' => $id]);
    }

    public function handleDeleteComment($id)
    {
        $comment = TicketComment::find($id);

        if (! $comment) {
            Notification::make()
                ->title('Komentar tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        if (! auth()->user()->hasRole(['super_admin']) && $comment->user_id !== auth()->id()) {
            Notification::make()
                ->title('Anda tidak memiliki izin untuk menghapus komentar ini')
                ->danger()
                ->send();

            return;
        }

        $comment->delete();

        Notification::make()
            ->title('Komentar berhasil dihapus')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
    }

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Kolom 1: Info Tugas
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('uuid')
                                        ->label('ID Tugas')
                                        ->copyable(),

                                    TextEntry::make('name')
                                        ->label('Deskripsi Tugas'),

                                    TextEntry::make('employee.name')
                                        ->label('Nama Pegawai')
                                        ->default('-'),

                                    TextEntry::make('priority')
                                        ->label('Prioritas')
                                        ->badge()
                                        ->formatStateUsing(fn (?string $state): string => match($state) {
                                            'urgent' => '🔴 Penting mendesak',
                                            'important' => '🟡 Penting tidak mendesak',
                                            'flexible' => '🟢 Fleksibel',
                                            default => $state ?? '-',
                                        })
                                        ->color(fn (?string $state): string => match($state) {
                                            'urgent' => 'danger',
                                            'important' => 'warning',
                                            'flexible' => 'success',
                                            default => 'gray',
                                        }),
                                ]),
                        ])->columnSpan(1),

                        // Kolom 2: Status & Assignment
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn (?string $state): string => match($state) {
                                            'belum' => 'Belum Dikerjakan',
                                            'proses' => 'Sedang Proses',
                                            'revisi' => 'Perlu Revisi',
                                            'selesai' => 'Selesai',
                                            default => $state ?? 'Belum Dikerjakan',
                                        })
                                        ->color(fn (?string $state): string => match($state) {
                                            'belum' => 'gray',
                                            'proses' => 'info',
                                            'revisi' => 'warning',
                                            'selesai' => 'success',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('assignees.name')
                                        ->label('Yang Ditugaskan')
                                        ->badge()
                                        ->separator(',')
                                        ->default('Belum diset'),

                                    TextEntry::make('approval_status')
                                        ->label('Persetujuan')
                                        ->badge()
                                        ->formatStateUsing(fn (?string $state): string => match($state) {
                                            'approved' => '✅ Approved',
                                            'pending' => '⏳ Pending',
                                            default => '⏳ Pending',
                                        })
                                        ->color(fn (?string $state): string => match($state) {
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            default => 'warning',
                                        }),
                                ]),
                        ])->columnSpan(1),

                        // Kolom 3: Tanggal
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('created_at')
                                        ->label('Tgl Pengisian')
                                        ->formatStateUsing(fn ($state) => $state && $state !== '-' ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-')
                                        ->placeholder('-'),

                                    TextEntry::make('start_date')
                                        ->label('Mulai')
                                        ->formatStateUsing(fn ($state) => $state && $state !== '-' ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-')
                                        ->placeholder('-'),

                                    TextEntry::make('due_date')
                                        ->label('Selesai')
                                        ->formatStateUsing(fn ($state) => $state && $state !== '-' ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-')
                                        ->placeholder('-'),

                                    TextEntry::make('updated_at')
                                        ->label('Terakhir Diperbarui')
                                        ->formatStateUsing(fn ($state) => $state && $state !== '-' ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                                        ->placeholder('-'),
                                ]),
                        ])->columnSpan(1),
                    ]),

                // Lampiran
                Section::make('Lampiran')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        ViewEntry::make('attachment')
                            ->view('filament.infolists.entries.attachment-detail')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Komentar
                Section::make('Komentar')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Diskusi mengenai tugas ini')
                    ->schema([
                        TextEntry::make('comments_list')
                            ->label('Komentar Terbaru')
                            ->state(function (Ticket $record) {
                                if (method_exists($record, 'comments')) {
                                    return $record->comments()->with('user')->latest()->get();
                                }

                                return collect();
                            })
                            ->view('filament.resources.ticket-resource.latest-comments'),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getActions(): array
    {
        return [
            Action::make('editComment')
                ->label('Edit Komentar')
                ->mountUsing(function (Schema $form, array $arguments) {
                    $commentId = $arguments['commentId'] ?? null;

                    if (! $commentId) {
                        return;
                    }

                    $comment = TicketComment::find($commentId);

                    if (! $comment) {
                        return;
                    }

                    $form->fill([
                        'commentId' => $comment->id,
                        'comment' => $comment->comment,
                    ]);
                })
                ->form([
                    Hidden::make('commentId')
                        ->required(),
                    RichEditor::make('comment')
                        ->label('Komentar')
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $comment = TicketComment::find($data['commentId']);

                    if (! $comment) {
                        Notification::make()
                            ->title('Komentar tidak ditemukan')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (! auth()->user()->hasRole(['super_admin']) && $comment->user_id !== auth()->id()) {
                        Notification::make()
                            ->title('Anda tidak memiliki izin untuk mengedit komentar ini')
                            ->danger()
                            ->send();

                        return;
                    }

                    $comment->update([
                        'comment' => $data['comment'],
                    ]);

                    Notification::make()
                        ->title('Komentar berhasil diperbarui')
                        ->success()
                        ->send();

                    $this->editingCommentId = null;

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
                })
                ->modalWidth('lg')
                ->modalHeading('Edit Komentar')
                ->modalSubmitActionLabel('Perbarui')
                ->color('success')
                ->icon('heroicon-o-pencil'),
        ];
    }
}