<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Employee;
use Filament\Forms\Form;
use App\Models\StatusLead;
use Filament\Tables\Table;
use App\Models\ProspectLead;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Imports\ProspectLeadImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProspectLeadResource\Pages;
use App\Filament\Resources\ProspectLeadResource\RelationManagers;

class ProspectLeadResource extends Resource
{
    protected static ?string $model = ProspectLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Prospect';
    protected static ?string $navigationLabel = 'Prospect';
    protected static ?string $label = 'Prospect';
    protected static ?string $slug = 'prospects';

    protected function getHeaderWidgets(): array
    {
        return [
            ProspectLeadResource\Widgets\ProspectLeadOverview::class,
            ProspectLeadResource\Widgets\ProspectSchedule::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Company')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category_industries_id')
                            ->label('Category Industry')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->relationship('category_industries', 'category')
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('category')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('industry_type')
                            ->label('Description Industry')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pic')
                            ->label('PIC')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('address')
                            ->columnSpan(2)
                            ->required(),
                    ])->columns('2'),
                Forms\Components\Section::make('Status prospect')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\Select::make('status_leads_id')
                            ->label('Status')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->relationship('status_leads', 'status')
                            ->default('1')
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('status')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\DatePicker::make('schedule')
                            ->label('Visit Schedule')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('user_id')
                            ->label('Followup by')
                            ->relationship('user', 'name')
                            ->default(fn() => auth::user()->id) // Set default ke user login
                            ->formatStateUsing(fn() => auth::user()->id) // Paksa tampilkan user login
                            ->dehydrated(),
                        Forms\Components\Select::make('followup_by')
                            ->label('Visit by')
                            ->multiple()
                            ->options(Employee::where('office_locations_id', '02')->get()->pluck('name', 'name'))
                            ->searchable()
                            ->live(),
                        Forms\Components\RichEditor::make('notes')
                            ->columnSpan(2),
                    ])->columns('2')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary'),
                ImportAction::make()
                    ->label('Import Data')
                    ->importer(ProspectLeadImporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->searchable(),
                IconColumn::make('is_followup_needed')
                    ->label('Need Followup')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-check-circle'),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_industries.category')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pic')
                    ->label('PIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_leads.status')
                    ->label('State')
                    ->badge('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Followup by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->searchable()
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Followup By')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('industry_type')
                    ->label('Industry')
                    ->options(fn(): array => ProspectLead::pluck('industry_type')->unique()->mapWithKeys(fn($value) => [$value => strval($value)])->toArray())
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_industries_id')
                    ->label('Category Industry')
                    ->relationship('category_industries', 'category')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_followup_needed')
                    ->label('Need Followup')
                    ->options(fn(): array => ProspectLead::pluck('is_followup_needed')->unique()->mapWithKeys(fn($value) => [$value => strval($value)])->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('followupDone')
                        ->label('Followup Done')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn($record) => $record->is_followup_needed)
                        ->form([
                            Forms\Components\Select::make('status_leads_id')
                                ->label('State')
                                ->relationship('status_leads', 'status')
                                ->default(function ($record) {
                                    // Menampilkan status yang sudah ada pada record sebelumnya
                                    return $record->status_leads_id;
                                })
                                ->live()
                                ->preload()
                                ->required()
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('status')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Notes')
                                ->placeholder('Masukkan catatan ...'),
                        ])
                        ->action(function (Table $table, ProspectLead $record, array $data) {
                            $record->update([
                                'status_leads_id'       => $data['status_leads_id'],
                                'user_id'               => auth::user()->id,
                                'is_followup_needed'    => false,
                                'notes'                 => $data['notes'],
                            ]);
                            Notification::make()
                                ->success()
                                ->title('Data Berhasil Diubah')
                                ->send();
                        }),
                    // Action::make('status')
                    //     ->label('Update Status')
                    //     ->color('warning')
                    //     ->form([
                    //         Forms\Components\Select::make('status_leads_id')
                    //             ->label('State')
                    //             ->relationship('status_leads', 'status')
                    //             ->default(function ($record) {
                    //                 // Menampilkan status yang sudah ada pada record sebelumnya
                    //                 return $record->status_leads_id;
                    //             })
                    //             ->live()
                    //             ->preload()
                    //             ->required()
                    //             ->searchable()
                    //             ->createOptionForm([
                    //                 Forms\Components\TextInput::make('status')
                    //                     ->required()
                    //                     ->maxLength(255),
                    //             ]),
                    //         // Forms\Components\Select::make('employees_id')
                    //         //     ->label('Followup by')
                    //         //     ->options(Employee::where('office_locations_id', '02')->get()->pluck('name', 'id'))
                    //         //     ->default(function ($record) {
                    //         //         // Menampilkan status yang sudah ada pada record sebelumnya
                    //         //         return $record->employees_id;
                    //         //     })
                    //         //     ->searchable()
                    //         //     ->live(),
                    //         Forms\Components\DatePicker::make('schedule')
                    //             ->label('Visit Schedule')
                    //             ->native(false)
                    //             ->displayFormat('d/m/Y')
                    //             ->default(function ($record) {
                    //                 // Menampilkan status yang sudah ada pada record sebelumnya
                    //                 return $record->schedule;
                    //             }),
                    //         Forms\Components\Select::make('followup_by')
                    //             ->label('Visit by')
                    //             ->multiple()
                    //             ->options(Employee::where('office_locations_id', '02')->get()->pluck('name', 'name'))
                    //             ->default(function ($record) {
                    //                 // Menampilkan status yang sudah ada pada record sebelumnya
                    //                 return $record->followup_by;
                    //             })
                    //             ->searchable()
                    //             ->live(),
                    //         Forms\Components\RichEditor::make('notes')
                    //             ->label('Notes')
                    //             ->placeholder('Masukkan catatan ...'),
                    //     ])
                    //     ->action(function (Table $table, ProspectLead $record, array $data) {
                    //         $record->update([
                    //             'status_leads_id'   => $data['status_leads_id'],
                    //             'user_id'           => auth::user()->id,
                    //             'schedule'          => $data['schedule'],
                    //             'followup_by'       => $data['followup_by'],
                    //             'notes'             => $data['notes'],
                    //         ]);
                    //         Notification::make()
                    //             ->success()
                    //             ->title('Status Prospect Berhasil Diubah')
                    //             ->send();
                    //     })
                    //     ->icon('heroicon-o-cog-6-tooth')
                    //     ->slideOver(),
                    Action::make('activities')
                        ->url(fn($record) => ProspectLeadResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Category Industry')
                        ->label('Category Industry')
                        ->color('primary')
                        ->icon('heroicon-o-bars-3')
                        ->form([
                            Forms\Components\Select::make('category_industries_id')
                                ->label('Category Industry')
                                ->relationship('category_industries', 'category')
                                ->live()
                                ->preload()
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'category_industries_id' => $data['category_industries_id'],
                                    'user_id' => auth::user()->id, // Update kolom note dari form modal
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Prospect')
                    ->schema([
                        Fieldset::make('Detail Company')
                            ->schema([
                                TextEntry::make('company_name')
                                    ->badge('primary')
                                    ->label('Perusahaan'),
                                TextEntry::make('industry_type')
                                    ->badge('primary')
                                    ->label('Industry'),
                                TextEntry::make('pic')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary')
                                    ->label('PIC'),
                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone')
                                    ->iconColor('primary')
                                    ->label('No. Telepon'),
                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->iconColor('primary')
                                    ->label('Email'),
                                TextEntry::make('address')
                                    ->icon('heroicon-o-map-pin')
                                    ->iconColor('primary')
                                    ->label('Alamat')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ])->columns(3),
                        Fieldset::make('Status Prospect')
                            ->schema([
                                TextEntry::make('status_leads.status')
                                    ->badge()
                                    ->label('Status'),
                                TextEntry::make('schedule')
                                    ->label('Schedule')
                                    ->badge()
                                    ->date(),
                                TextEntry::make('user.name')
                                    ->icon('heroicon-o-user-circle')
                                    ->badge()
                                    ->label('Followup by'),
                                TextEntry::make('followup_by')
                                    ->badge()
                                    ->label('Visit by'),
                                TextEntry::make('notes')
                                    ->icon('heroicon-o-bookmark')
                                    ->iconColor('primary')
                                    ->label('Notes')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ])->columns(3),
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspectLeads::route('/'),
            'create' => Pages\CreateProspectLead::route('/create'),
            'view' => Pages\ViewProspectLead::route('/{record}'),
            'edit' => Pages\EditProspectLead::route('/{record}/edit'),
            'activities' => Pages\ListProspectLeadActivities::route('/{record}/activities'),
        ];
    }
}
