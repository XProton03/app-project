<?php

namespace App\Filament\Resources\AdiraReportResource\Pages;

use App\Filament\Resources\AdiraReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdiraReport extends ViewRecord
{
    protected static string $resource = AdiraReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
