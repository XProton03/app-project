<?php

namespace App\Filament\Resources\ProspectLeadResource\Pages;

use App\Filament\Imports\ProspectLeadImporter;
use App\Filament\Resources\ProspectLeadResource;
use App\Models\ProspectLead;
use App\Models\StatusLead;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProspectLeads extends ListRecords
{
    protected static string $resource = ProspectLeadResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //         Actions\ImportAction::make()
    //             ->label('Import Data')
    //             ->importer(ProspectLeadImporter::class)
    //             ->icon('heroicon-o-arrow-down-tray')
    //             ->color('primary'),
    //     ];
    // }
    protected function getHeaderWidgets(): array
    {
        return [
            ProspectLeadResource\Widgets\ProspectLeadOverview::class,
            ProspectLeadResource\Widgets\ProspectSchedule::class,
            ProspectLeadResource\Widgets\FollowupProspectTable::class,
        ];
    }
    public function getTabs(): array
    {
        $tabs = [];

        // Menambahkan tab 'All'
        $tabs['All'] = Tab::make()
            ->badge(ProspectLead::query()->count())
            ->label('All States');

        // Mengambil semua status
        $statusLeads = StatusLead::all();

        // Menambahkan tab untuk setiap status
        foreach ($statusLeads as $status) {
            $tabs[$status->status] = Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_leads_id', '=', $status->id))
                ->badge(ProspectLead::query()->where('status_leads_id', '=', $status->id)->count())
                ->label($status->status);
        }

        return $tabs;
    }
}
