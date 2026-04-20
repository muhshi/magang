<?php

namespace App\Console\Commands;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoApproveLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:auto-approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-approve pending leave requests that have not been processed after 5 working days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingLeaves = Leave::where('status', 'pending')->get();
        $now = Carbon::now();
        $approvedCount = 0;

        foreach ($pendingLeaves as $leave) {
            $createdAt = Carbon::parse($leave->created_at);
            
            // Hitung hari kerja (Senen-Jumat) antara tanggal pembuatan dan sekarang
            $workingDays = $this->countWorkingDays($createdAt, $now);

            if ($workingDays >= 5) {
                $leave->update([
                    'status' => 'approved',
                    'note' => ($leave->note ? $leave->note . "\n" : "") . '[Auto-approved by System after 5 working days]'
                ]);
                $approvedCount++;
                
                $this->info("Approved leave for User ID: {$leave->user_id} (Created at: {$leave->created_at})");
                Log::info("Leave auto-approved for user_id: {$leave->user_id}, leave_id: {$leave->id}");
            }
        }

        $this->info("Total leaves auto-approved: {$approvedCount}");
    }

    /**
     * Helper to count working days (Mon-Fri) between two dates.
     */
    private function countWorkingDays(Carbon $start, Carbon $end): int
    {
        if ($start->greaterThan($end)) {
            return 0;
        }

        $count = 0;
        // Start from the day after creation
        $current = $start->copy()->addDay()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($current->lessThanOrEqualTo($endDay)) {
            if (!$current->isWeekend()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }
}
