<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;


class ProspectLead extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'prospect_leads';
    protected $fillable = [
        'company_name',
        'category_industries_id',
        'industry_type',
        'phone',
        'email',
        'address',
        'pic',
        'status_leads_id',
        'schedule',
        'employees_id',
        'user_id',
        'followup_by',
        'is_followup_needed',
        'notes',
    ];

    protected $casts = [
        'followup_by' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = Auth::id();
    }

    public function status_leads(): BelongsTo
    {
        return $this->belongsTo(StatusLead::class, 'status_leads_id');
    }
    public function category_industries(): BelongsTo
    {
        return $this->belongsTo(CategoryIndustry::class, 'category_industries_id');
    }
    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
