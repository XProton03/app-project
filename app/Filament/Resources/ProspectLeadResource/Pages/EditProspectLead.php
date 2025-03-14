<?php

namespace App\Filament\Resources\ProspectLeadResource\Pages;

use App\Filament\Resources\ProspectLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProspectLead extends EditRecord
{
    protected static string $resource = ProspectLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::id(); // Pastikan selalu pakai user yang login
        return $data;
    }
}
