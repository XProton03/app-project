<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'companies';
    protected $fillable = [
        'provinces_id',
        'regencies_id',
        'districts_id',
        'villages_id',
        'company_name',
        'company_address',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
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
