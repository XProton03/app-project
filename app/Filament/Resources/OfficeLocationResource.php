<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeLocationResource\Pages;
use App\Filament\Resources\OfficeLocationResource\RelationManagers;
use App\Models\OfficeLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Get as FormsGet;
use Filament\Forms\Set;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;

class OfficeLocationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = OfficeLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Employees';
    protected static ?string $navigationLabel = 'Offices';
    protected static ?string $label = 'Offices';
    protected static ?string $slug = 'office-locations';
    protected static ?int $navigationSort = 25;

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
                Forms\Components\Section::make('Informasi Office')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->label('Kode'),
                        Forms\Components\TextInput::make('office_name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->numeric()
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Head Office' => 'Head Office',
                                'Representatif' => 'Representatif',
                                'Cabang' => 'Cabang',
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns('3'),
                Forms\Components\Section::make('Informasi Alamat Office')
                    ->schema([
                        Forms\Components\Select::make('provinces_id')
                            ->label('Province')
                            ->relationship(name: 'provinces', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('regencies_id', null);
                                $set('districts_id', null);
                            }),
                        Forms\Components\Select::make('regencies_id')
                            ->label('Regency')
                            ->options(function (FormsGet $get) {
                                return \App\Models\Regency::where('provinces_id', $get('provinces_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('districts_id', null);
                            }),
                        Forms\Components\Select::make('districts_id')
                            ->label('District')
                            ->options(function (FormsGet $get) {
                                return \App\Models\District::where('regencies_id', $get('regencies_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('villages_id')
                            ->label('Villages')
                            ->options(function (FormsGet $get) {
                                return \App\Models\Village::where('districts_id', $get('districts_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\RichEditor::make('address')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpan(3),
                    ])
                    ->columns('3'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinces.name')
                    ->label('Location')
                    ->searchable(),
            ])
            ->filters([
                //
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Office')
                    ->schema([
                        Fieldset::make('Office')
                            ->schema([
                                TextEntry::make('code')
                                    ->columnSpan('full')
                                    ->badge(),
                                TextEntry::make('office_name')
                                    ->icon('heroicon-o-building-office')
                                    ->iconColor('primary'),
                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone')
                                    ->iconColor('primary'),
                                TextEntry::make('status')
                                    ->badge(),
                            ])->columns(3),
                        Fieldset::make('Address')
                            ->schema([
                                TextEntry::make('provinces.name')
                                    ->label('Provinsi'),
                                TextEntry::make('regencies.name')
                                    ->label('Kabupaten/Kota'),
                                TextEntry::make('districts.name')
                                    ->label('Kecamatan'),
                                TextEntry::make('villages.name')
                                    ->label('Kelurahan'),
                                TextEntry::make('address')
                                    ->label('Alamat')
                                    ->columnSpanFull()
                                    ->markdown()
                                    ->icon('heroicon-o-map-pin')
                                    ->iconColor('primary'),
                            ])->columns(3),
                    ])
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
            'index' => Pages\ListOfficeLocations::route('/'),
            'create' => Pages\CreateOfficeLocation::route('/create'),
            'view' => Pages\ViewOfficeLocation::route('/{record}'),
            'edit' => Pages\EditOfficeLocation::route('/{record}/edit'),
        ];
    }
}
