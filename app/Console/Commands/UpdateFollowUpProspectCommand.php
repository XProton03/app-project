<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UpdateFollowUpProspectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followup:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update is_followup_needed menjadi true jika updated_at lebih dari 2 hari
        DB::table('prospect_leads')
            ->whereNotIn('status_leads_id', [10, 11, 1]) // Kecuali status tertentu
            ->where('last_followup', '>=', Carbon::now()->subDays(2))
            ->where('is_followup_needed', false) // Hanya update jika masih false
            ->update(['is_followup_needed' => true]);

        // Update is_followup_needed menjadi false jika updated_at adalah hari ini
        // DB::table('prospect_leads')
        //     ->whereDate('updated_at', Carbon::today())
        //     ->where('is_followup_needed', true) // Hanya update jika masih true
        //     ->update(['is_followup_needed' => false]);

        $this->info('Follow-up status updated successfully.');
    }
}
