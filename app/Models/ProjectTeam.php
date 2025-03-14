<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectTeam extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'project_teams';
    protected $fillable = [
        'projects_id',
        'employees_id',
        'start_date',
        'end_date',
        'area',
        'location',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function projects(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }
    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
}
