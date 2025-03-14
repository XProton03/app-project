<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficeLocation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'office_locations';
    protected $fillable = [
        'provinces_id',
        'regencies_id',
        'districts_id',
        'villages_id',
        'code',
        'office_name',
        'phone',
        'address',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'office_locations_id');
    }

    public function provinces(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'provinces_id');
    }
    public function regencies(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regencies_id');
    }
    public function districts(): BelongsTo
    {
        return $this->belongsTo(District::class, 'districts_id');
    }
    public function villages(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'villages_id');
    }

}
