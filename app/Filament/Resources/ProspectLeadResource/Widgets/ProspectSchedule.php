<?php

namespace App\Filament\Resources\ProspectLeadResource\Widgets;

use App\Models\ProspectLead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProspectSchedule extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProspectLead::query()
                    ->where('schedule', '!=', null)
                    ->where('status_leads_id', 1)
            )
            ->defaultSort('schedule', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company'),
                Tables\Columns\TextColumn::make('status_leads.status')
                    ->label('State')
                    ->badge('primary'),
                Tables\Columns\TextColumn::make('schedule')
                    ->label('Schedule')
                    ->date(),
                Tables\Columns\TextColumn::make('followup_by')
                    ->label('Visit by'),
            ]);
    }
}
