<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Filament\Forms\Get as FormsGet;
use Filament\Forms\Set;
use Filament\Infolists\Infolist;
use App\Filament\Exports\EmployeeExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\CreateAction;

class EmployeeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Employees';
    protected static ?string $navigationLabel = 'Employee';
    protected static ?string $label = 'Employee';
    protected static ?string $slug = 'employee';
    protected static ?int $navigationSort = 21;


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
                Forms\Components\Section::make('Informasi Karyawan')
                    ->description('Isi form ini dengan informasi pribadi karyawan.')
                    ->schema([
                        Forms\Components\TextInput::make('employee_code')
                            ->label('NIP')
                            ->readOnly()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('phone')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->numeric()
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\Radio::make('gender')
                            ->options([
                                'Laki-Laki' => 'Laki-Laki',
                                'Perempuan' => 'Perempuan',
                            ])
                            ->required()
                            ->inline()
                            ->inlineLabel(false),
                        Forms\Components\TextInput::make('email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->columnSpan(2)
                            ->maxLength(255),
                    ])
                    ->columns('3'),
                Forms\Components\Section::make('Informasi Alamat Karyawan')
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
                Forms\Components\Section::make('Status Karyawan')
                    ->schema([
                        Forms\Components\Select::make('office_locations_id')
                            ->label('Office')
                            ->relationship(name: 'office_locations', titleAttribute: 'office_name')
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('employement_statuses_id')
                            ->label('Status')
                            ->relationship(name: 'employement_statuses', titleAttribute: 'status_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('status_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('departments_id')
                            ->label('Department')
                            ->relationship(name: 'departments', titleAttribute: 'department_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('job_positions_id')
                            ->label('Jabatan')
                            ->relationship(name: 'job_positions', titleAttribute: 'position_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('position_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\DatePicker::make('contract_start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('contract_end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                    ])
                    ->columns('3'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                //
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office_locations.provinces.name')
                    ->label('Office')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departments.department_name')
                    ->label('Department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_positions.position_name')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employement_statuses.status_name')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_end_date')
                    ->label('Status Kontrak')
                    ->badge()
                    ->icon('heroicon-o-clock')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            $endDate = \Carbon\Carbon::parse($state);
                            $remainingDays = now()->diffInDays($endDate, false);

                            if ($remainingDays > 0) {
                                return "Sisa " . round($remainingDays) . " hari";
                            } elseif ($remainingDays === 0) {
                                return "Kontrak Berakhir Hari Ini";
                            } else {
                                return "Kontrak Berakhir";
                            }
                        }
                        return "Tidak Ada Kontrak";
                    })
                    ->color(fn($state) => str_contains($state, 'Sisa ') ? 'success' : 'danger')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('Office')
                    ->relationship('office_locations', 'office_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('status')
                    ->relationship('employement_statuses', 'status_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('jabatan')
                    ->relationship('job_positions', 'position_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('department')
                    ->relationship('departments', 'department_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('set_user_id')
                        ->label('Set User ID')
                        ->icon('heroicon-o-user-circle')
                        ->color('primary')
                        ->form([
                            Select::make('user_id')
                                ->label('User')
                                ->options(User::query()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            if (!isset($data['user_id'])) {
                                Notification::make()
                                    ->title('User ID is required!')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $record->update(['user_id' => $data['user_id']]);

                            Notification::make()
                                ->title('User ID updated successfully!')
                                ->success()
                                ->send();
                        }),
                    Action::make('activities')
                        ->url(fn($record) => EmployeeResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                    // ExportBulkAction::make()
                    //     ->exporter(EmployeeExporter::class),
                    BulkAction::make('delete')
                        ->label('Delete Selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->action(function (Collection $records) {
                            foreach ($records as $employee) {
                                // 🗂 Hapus semua file terkait di quotation_files
                                $employee->employement_files()->each(function ($file) {
                                    if (Storage::disk('nas')->exists($file->file)) {
                                        Storage::disk('nas')->delete($file->file);
                                    }
                                    $file->delete();
                                });
                                $employee->delete();

                                Notification::make()
                                    ->title('Data deleted successfully!')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Karyawan')
                    ->schema([
                        Fieldset::make('Informasi Karyawan')
                            ->schema([
                                TextEntry::make('employee_code')
                                    ->columnSpan('full')
                                    ->badge()
                                    ->label('NIP'),
                                TextEntry::make('name')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary')
                                    ->label('Nama'),
                                TextEntry::make('birth_date')
                                    ->date()
                                    ->label('Tanggal Lahir'),
                                TextEntry::make('gender')
                                    ->badge()
                                    ->label('Jenis Kelamin'),
                                TextEntry::make('phone')
                                    ->label('No. Telepon'),
                                TextEntry::make('email')
                                    ->label('Email'),
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
                                    ->markdown(),
                            ])->columns(3),
                        Fieldset::make('Status Karyawan')
                            ->schema([
                                TextEntry::make('employement_statuses.status_name')
                                    ->badge()
                                    ->label('Status'),
                                TextEntry::make('departments.department_name')
                                    ->badge()
                                    ->label('Departemen'),
                                TextEntry::make('job_positions.position_name')
                                    ->badge()
                                    ->label('Jabatan'),
                                TextEntry::make('contract_start_date')
                                    ->label('Mulai Kontrak')
                                    ->badge()
                                    ->date(),
                                TextEntry::make('contract_end_date')
                                    ->label('Selesai Kontrak')
                                    ->badge()
                                    ->date(),
                            ])->columns(3),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EmployementFilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'activities' => Pages\ListEmployeeActivities::route('/{record}/activities'),
        ];
    }
}
