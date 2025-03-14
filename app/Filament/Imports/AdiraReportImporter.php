<?php

namespace App\Filament\Imports;

use App\Models\AdiraReport;
use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AdiraReportImporter extends Importer
{
    protected static ?string $model = AdiraReport::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employees_id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('number')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('periode')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('status_tiket')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('category')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('service')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('subject')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('responses_duration')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('responses_breach')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('resolution_duration')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('resolution_breach')
                ->requiredMapping()
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?AdiraReport
    {
        // return AdiraReport::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new AdiraReport();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your adira report import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeValidate(): void
    {
        $employees_id = Employee::query()->where('name', $this->data['employees_id'])->first()?->id;
        $this->data['employees_id'] = $employees_id;
    }
}
