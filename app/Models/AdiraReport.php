<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdiraReport extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'adira_reports';
    protected $fillable = [
        'employees_id',
        'number',
        'periode',
        'status_tiket',
        'category',
        'service',
        'subject',
        'responses_duration',
        'responses_breach',
        'resolution_duration',
        'resolution_breach',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employees():BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
}
