<?php

namespace App\Filament\Resources\OfficeLocationResource\Pages;

use App\Filament\Resources\OfficeLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOfficeLocation extends ViewRecord
{
    protected static string $resource = OfficeLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
