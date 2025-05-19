<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
    protected ?bool $hasDatabaseTransactions = true;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Successfully')
            ->body('The department has been created successfully.');
    }
}
