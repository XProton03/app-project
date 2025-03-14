<?php

namespace App\Filament\Resources\AdiraReportResource\Pages;

use App\Filament\Resources\AdiraReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdiraReports extends ListRecords
{
    protected static string $resource = AdiraReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
