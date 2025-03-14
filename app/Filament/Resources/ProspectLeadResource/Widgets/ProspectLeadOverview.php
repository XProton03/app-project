<?php

namespace App\Filament\Resources\ProspectLeadResource\Widgets;

use App\Models\ProspectLead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class ProspectLeadOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // stat::make('Total Leads', ProspectLead::query()->count()),
            Stat::make('Prospect', ProspectLead::query()
                ->count())
                ->description('Total Leads')
                ->descriptionIcon('heroicon-m-clipboard')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Prospect', ProspectLead::where('is_followup_needed', True)
                ->count())
                ->description('Need Followup')
                ->descriptionIcon('heroicon-m-clipboard')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
        ];
    }
}
