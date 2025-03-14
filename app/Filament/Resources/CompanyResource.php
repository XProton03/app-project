<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use App\Filament\Exports\CompanyExporter;
use Filament\Tables\Actions\ExportBulkAction;

class CompanyResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?string $navigationLabel = 'Company';
    protected static ?string $label = 'Company';
    protected static ?string $slug = 'companies';
    protected static ?int $navigationSort = 32;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Company')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\Select::make('villages_id')
                            ->label('Village')
                            ->relationship('villages', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('company_address')
                            ->columnSpan(2)
                            ->required(),
                    ])->columns('2')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('villages.districts.regencies.provinces.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('villages.districts.regencies.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('villages.districts.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('villages.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('activities')
                        ->url(fn($record) => CompanyResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(CompanyExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Company')
                    ->schema([
                        TextEntry::make('villages.name')
                            ->badge('primary')
                            ->label('Kecamatan'),
                        TextEntry::make('company_name')
                            ->badge('primary')
                            ->label('Perusahaan'),
                        TextEntry::make('company_address')
                            ->label('Alamat')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary')
                            ->columnSpanFull()
                            ->markdown(),
                    ])->columns(2),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
            'activities' => Pages\ListCompanyActivities::route('/{record}/activities'),
        ];
    }
}
