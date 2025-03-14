<?php

namespace App\Observers;

use App\Models\ProspectLead;
use Carbon\Carbon;

class ProspectLeadObserver
{
    /**
     * Handle the ProspectLead "created" event.
     */
    public function created(ProspectLead $prospectLead): void
    {
        //
    }

    /**
     * Handle the ProspectLead "updated" event.
     */
    public function updated(ProspectLead $prospectLead): void
    {
        // Abaikan jika status_leads_id adalah 10, 11, atau 1
        if (in_array($prospectLead->status_leads_id, [10, 11, 1])) {
            $prospectLead->updateQuietly(['is_followup_needed' => false]);
            return;
        }

        // Cek apakah sudah 2 hari sejak terakhir di-update
        $needsFollowUp = $prospectLead->updated_at->diffInDays(Carbon::now()) >= 2;

        // Update status follow-up
        $prospectLead->updateQuietly(['is_followup_needed' => $needsFollowUp]);
    }

    /**
     * Handle the ProspectLead "deleted" event.
     */
    public function deleted(ProspectLead $prospectLead): void
    {
        //
    }

    /**
     * Handle the ProspectLead "restored" event.
     */
    public function restored(ProspectLead $prospectLead): void
    {
        //
    }

    /**
     * Handle the ProspectLead "force deleted" event.
     */
    public function forceDeleted(ProspectLead $prospectLead): void
    {
        //
    }
}
