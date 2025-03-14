<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'projects';
    protected $fillable = [
        'customers_id',
        'contract_no',
        'project_name',
        'description',
        'start_date',
        'end_date',
        'price',
        'status',
        'pic',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    public function project_files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'projects_id');
    }

    public function project_teams(): HasMany
    {
        return $this->hasMany(ProjectTeam::class, 'projects_id');
    }
}
