<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdiraReportResource\Pages;
use App\Filament\Resources\AdiraReportResource\RelationManagers;
use App\Models\AdiraReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\AdiraReportImporter;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Exports\AdiraReportExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;

class AdiraReportResource extends Resource
{
    protected static ?string $model = AdiraReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Adira';
    protected static ?string $label = 'Adira';
    protected static ?string $slug = 'adira-report';
    protected static ?int $navigationSort = 51;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->label('Import Data')
                    ->importer(AdiraReportImporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),
                ExportAction::make()
                    ->label('Export Data')
                    ->exporter(AdiraReportExporter::class)
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary'),
                Action::make('report')
                    ->label('Show Report')
                    ->url(fn() => url('http://192.168.20.24/adira'))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->color('primary')
            ])
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employees.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->badge('primary'),
                Tables\Columns\TextColumn::make('periode')
                    ->searchable()
                    ->date(),
                Tables\Columns\TextColumn::make('status_tiket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('periode', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('periode', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators[] = 'Start date ' . Carbon::parse($data['start_date'])->format('F d, Y');
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators[] = 'End date ' . Carbon::parse($data['end_date'])->format('F d, Y');
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdiraReports::route('/'),
            'create' => Pages\CreateAdiraReport::route('/create'),
            'view' => Pages\ViewAdiraReport::route('/{record}'),
            'edit' => Pages\EditAdiraReport::route('/{record}/edit'),
        ];
    }
}
