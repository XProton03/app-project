<?php

namespace App\Filament\Resources\ProspectLeadResource\Pages;

use App\Filament\Resources\ProspectLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProspectLead extends ViewRecord
{
    protected static string $resource = ProspectLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
