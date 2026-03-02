<?php

namespace App\Filament\Resources\LogbookResource\Pages;

use App\Filament\Resources\LogbookResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewLogbook extends ViewRecord
{
    protected static string $resource = LogbookResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Detail Logbook')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('tanggal_pengisian')
                            ->label('Tanggal Pengisian')
                            ->date('d M Y'),

                        TextEntry::make('nama_pegawai')
                            ->label('Nama Pegawai (Pemberi Tugas)'),
                    ]),

                    TextEntry::make('deskripsi_tugas')
                        ->label('Deskripsi Tugas')
                        ->columnSpanFull(),

                    TextEntry::make('assignees.name')
                        ->label('Yang Ditugaskan')
                        ->badge()
                        ->separator(',')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'belum'   => 'Belum Dikerjakan',
                                'proses'  => 'Sedang Proses',
                                'revisi'  => 'Perlu Revisi',
                                'selesai' => 'Selesai',
                                default   => '-',
                            })
                            ->color(fn (?string $state) => match ($state) {
                                'belum'   => 'gray',
                                'proses'  => 'info',
                                'revisi'  => 'warning',
                                'selesai' => 'success',
                                default   => 'gray',
                            }),

                        TextEntry::make('source')
                            ->label('Sumber')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'system' => 'Sistem',
                                'manual' => 'Manual',
                                default  => '-',
                            })
                            ->color(fn (?string $state) => match ($state) {
                                'system' => 'primary',
                                'manual' => 'gray',
                                default  => 'gray',
                            }),
                    ]),

                    ViewEntry::make('lampiran')
                        ->label('Lampiran')
                        ->view('filament.infolists.entries.logbook-attachment-detail')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
