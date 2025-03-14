<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusLead extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'status_leads';
    protected $fillable = [
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function prospect_leads()
    {
        return $this->hasMany(ProspectLead::class, 'status_leads_id');
    }
}
