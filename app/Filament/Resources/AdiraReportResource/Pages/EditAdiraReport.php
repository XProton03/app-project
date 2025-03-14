<?php

namespace App\Filament\Resources\AdiraReportResource\Pages;

use App\Filament\Resources\AdiraReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdiraReport extends EditRecord
{
    protected static string $resource = AdiraReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
