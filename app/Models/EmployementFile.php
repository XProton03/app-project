<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployementFile extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employement_files';
    protected $fillable = [
        'employees_id',
        'file_name',
        'file',
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
