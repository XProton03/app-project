<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employees';
    protected $fillable = [
        'departments_id',
        'provinces_id',
        'regencies_id',
        'districts_id',
        'villages_id',
        'employee_code',
        'name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'address',
        'contract_start_date',
        'contract_end_date',
        'employement_statuses_id',
        'job_positions_id',
        'office_locations_id',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public static function boot()
    {
        parent::boot();

        // Generate NIP saat karyawan baru diinput
        static::creating(function ($employee) {
            $employee->employee_code = self::generateEmployeeCode($employee);
        });

        // Periksa jika status karyawan berubah menjadi karyawan tetap
        static::updating(function ($employee) {
            // Jika status berubah menjadi karyawan tetap, regenerate employee_code
            if ($employee->isDirty('employement_statuses_id')) {
                if ($employee->employement_statuses_id == 1) {
                    // Jika menjadi karyawan tetap
                    $employee->employee_code = self::generateEmployeeCode($employee);
                } elseif ($employee->getOriginal('employement_statuses_id') == 1 && $employee->employement_statuses_id != 1) {
                    // Jika dari karyawan tetap menjadi tidak tetap
                    $employee->employee_code = self::generateEmployeeCode($employee);
                }
            }
        });
    }

    private static function generateEmployeeCode($employee)
    {
        if ($employee->employement_statuses_id == 1) {
            $jobposition = JobPosition::find($employee->job_positions_id);
            $officelocation = OfficeLocation::find($employee->office_locations_id);

            // Hitung jumlah karyawan tetap yang ada di lokasi kantor yang sama
            $employeeCount = Employee::where('office_locations_id', $employee->office_locations_id)
                ->where('employement_statuses_id', 1)
                ->count() + 1;  // Menambahkan 1 untuk urutan karyawan berikutnya

            $monthYear = \Carbon\Carbon::parse($employee->contract_start_date)->format('mY');

            // Format employee code untuk karyawan tetap
            return sprintf('%s%s%04d%s', $officelocation->code, $jobposition->code, $employeeCount, $monthYear);
        } else {
            // Jika status bukan karyawan tetap, hanya gunakan angka yang bertambah
            $employeeCount = Employee::where('employement_statuses_id', '!=', 1)->count() + 1;

            // Generate employee_code untuk karyawan tidak tetap
            return sprintf('%06d', $employeeCount);
        }
    }

    public function employement_statuses(): BelongsTo
    {
        return $this->belongsTo(EmployementStatus::class, 'employement_statuses_id');
    }
    public function job_positions(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_positions_id');
    }
    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departments_id');
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

    public function employement_files(): HasMany
    {
        return $this->hasMany(EmployementFile::class, 'employees_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function office_locations(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class, 'office_locations_id');
    }

    public function prospect_leads(): HasMany
    {
        return $this->hasMany(ProspectLead::class, 'employees_id');
    }
}
