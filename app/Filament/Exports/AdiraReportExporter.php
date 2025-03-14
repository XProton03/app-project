<?php

namespace App\Filament\Exports;

use App\Models\AdiraReport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AdiraReportExporter extends Exporter
{
    protected static ?string $model = AdiraReport::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employees.name'),
            ExportColumn::make('number'),
            ExportColumn::make('periode'),
            ExportColumn::make('status_tiket'),
            ExportColumn::make('category'),
            ExportColumn::make('service'),
            ExportColumn::make('subject'),
            ExportColumn::make('responses_duration'),
            ExportColumn::make('responses_breach'),
            ExportColumn::make('resolution_duration'),
            ExportColumn::make('resolution_breach'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your adira report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
