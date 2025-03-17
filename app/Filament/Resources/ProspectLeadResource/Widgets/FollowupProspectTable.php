<?php

namespace App\Filament\Resources\ProspectLeadResource\Widgets;

use App\Models\ProspectLead;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class FollowupProspectTable extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProspectLead::query()
                    ->join('users', 'prospect_leads.user_id', '=', 'users.id')
                    ->where('prospect_leads.is_followup_needed', true)
                    ->selectRaw('users.name, prospect_leads.user_id AS id, COUNT(prospect_leads.id) as total_followup_needed')
                    ->groupBy('prospect_leads.user_id', 'users.name')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Followup By')
                    ->sortable(),
                TextColumn::make('total_followup_needed')
                    ->label('Need Followup')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state)),
            ]);
    }
}
