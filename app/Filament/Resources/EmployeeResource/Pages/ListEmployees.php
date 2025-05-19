<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\EmployeeExporter;
use Filament\Actions\ExportAction;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;
    protected ?bool $hasDatabaseTransactions = true;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus-circle')
                ->databaseTransaction()
                ->label('Add Data'),
            ExportAction::make()
                ->exporter(EmployeeExporter::class)
                ->color('primary')
                ->icon('heroicon-o-arrow-up-circle')
                ->label('Export Data')
            
        ];
    }
}
