<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SertifikatSaya extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Sertifikat';
    protected static ?string $title = 'Sertifikat Saya';
    protected static ?string $slug = 'sertifikat-saya';
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Sertifikat';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sertifikat-saya';

    public $certificate = null;
    public $internship = null;
    public ?string $pdfUrl = null;
    public ?string $downloadUrl = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->internship = $user->internship;

        if ($this->internship && $this->internship->certificate) {
            $this->certificate = $this->internship->certificate;
            $this->pdfUrl = route('certificate.generate', $this->certificate->id);
            $this->downloadUrl = route('certificate.download', $this->certificate->uuid);
        }
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return $user->hasRole(['Magang BPS', 'Alumni Magang']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
