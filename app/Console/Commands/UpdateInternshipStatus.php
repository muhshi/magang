<?php

namespace App\Console\Commands;

use App\Models\Internship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateInternshipStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interns:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengubah role peserta magang yang telah menyelesaikan masa magang menjadi Alumni Magang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Cari peserta magang aktif (Magang BPS) yang end_date-nya sudah lewat
        $expiredInternships = Internship::where('status', 'accepted')
            ->where('end_date', '<', $today)
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('name', 'Magang BPS'));
            })
            ->with('user')
            ->get();

        $updatedCount = 0;

        foreach ($expiredInternships as $internship) {
            $user = $internship->user;
            if ($user && $user->hasRole('Magang BPS')) {
                $user->removeRole('Magang BPS');
                $user->assignRole('Alumni Magang');
                $updatedCount++;

                $this->info("User '{$user->name}' (ID: {$user->id}) masa magang berakhir {$internship->end_date} dialihkan ke Alumni Magang.");
                Log::info("Intern status updated to Alumni Magang for user: {$user->name} (ID: {$user->id})");
            }
        }

        $this->info("Total peserta magang dialihkan ke Alumni: {$updatedCount}");
        return Command::SUCCESS;
    }
}
