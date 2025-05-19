<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\TaskResource\RelationManagers\TaskFilesRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\TaskExpensesRelationManager;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Project Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Task')
                    ->description('please fill the column')
                    ->schema([
                        Forms\Components\TextInput::make('task_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('quotations_id')
                            ->columnSpan(2)
                            ->relationship('quotations', 'quotation_number', function ($query) {
                                $query->where('status', '!=', 'Selesai'); // Filter status yang belum selesai
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $customerName = $record->customers->name ?? 'N/A';
                                $companyName = $record->customers->companies->company_name ?? 'N/A'; // Akses relasi company melalui customers
                                return $record->quotation_number . ' - ' . $record->project_name . ' - ' . $customerName . ' - ' . $companyName;
                            }),
                        Forms\Components\Select::make('companies_id')
                            ->relationship('companies', 'company_name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->company_name . ' - ' . ($record->villages->districts->regencies->name ?? 'N/A');
                            }),
                        Forms\Components\TextInput::make('pic')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required(),
                        Forms\Components\RichEditor::make('short_description')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('job_description')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('schedule')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('duration')
                            ->numeric(),
                        Forms\Components\Select::make('employees_id')
                            ->relationship('employees', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Planing' => 'Planing',
                                'In Progress' => 'In Progress',
                                'Document Progress' => 'Document Progress',
                                'Completed' => 'Completed',
                                'Cancel' => 'Cancel',
                            ])
                            ->required()
                            ->searchable()
                            ->default('Planing'),
                        Forms\Components\RichEditor::make('notes')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quotations.quotation_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('task_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.villages.districts.regencies.name')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('employees.name')
                    ->label('Engineer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => [
                        'Planing' => 'primary',
                        'In Progress' => 'warning',
                        'Document Progress' => 'info',
                        'Completed' => 'success',
                        'Cancel' => 'danger',
                    ][$state] ?? 'secondary'),
            ])
            ->filters([
                SelectFilter::make('quotations_id')
                    ->relationship('quotations', 'quotation_number')
                    ->label('Quotation Number')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from ' . Carbon::parse($data['created_from'])->format('F d, Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until ' . Carbon::parse($data['created_until'])->format('F d, Y');
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('in_progress')
                        ->label('In Progress')
                        ->color('info')
                        ->visible(fn($record): bool => $record->status === 'Planing')
                        ->form([
                            Forms\Components\DatePicker::make('start_date')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Select::make('employees_id')
                                ->relationship('employees', 'name')
                                ->preload()
                                ->searchable(),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status inprogress...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Task::where('id', $record->id)->update([
                                'status'        => 'In Progress',
                                'start_date'    => $data['start_date'],
                                'employees_id'  => $data['employees_id'],
                                'notes'         => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to In Progress successfully!')
                                ->success()
                                ->send();
                        })
                        ->slideOver()
                        ->icon('heroicon-o-cog-8-tooth'),
                    Action::make('document_progress')
                        ->label('Document Progress')
                        ->color('warning')
                        ->visible(fn($record): bool => $record->status === 'In Progress')
                        ->form([
                            Forms\Components\DatePicker::make('end_date')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('duration')
                                ->required()
                                ->numeric(),
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Task::where('id', $record->id)->update([
                                'status'    => 'Document Progress',
                                'end_date'  => $data['end_date'],
                                'duration'  => $data['duration'],
                                'notes'     => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to Document Progress successfully!')
                                ->success()
                                ->send();
                        })
                        ->slideOver()
                        ->icon('heroicon-o-currency-dollar'),
                    Action::make('completed')
                        ->label('Completed')
                        ->visible(fn($record): bool => $record->status === 'Document Progress')
                        ->color('success')
                        ->steps([
                            Step::make('Upload file BAST')
                                ->description('Pastikan file diupload lengkap dan sesuai dengan data yang ada.')
                                ->schema([
                                    Forms\Components\TextInput::make('file_name')
                                        ->required()
                                        ->columnSpanFull()
                                        ->maxLength(255),
                                    Forms\Components\FileUpload::make('file')
                                        ->columnSpanFull()
                                        ->disk('nas')
                                        ->directory('tasks')
                                        ->preserveFilenames()
                                        ->maxSize(2048)
                                        ->openable()
                                        ->acceptedFileTypes(['application/pdf']),
                                ]),
                            Step::make('Harga')
                                ->description('Masukan harga sesuai dengan pricelist dan durasi pekerjaan.')
                                ->schema([
                                    Forms\Components\TextInput::make('mandays')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('transports')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('accomodations')
                                        ->required()
                                        ->numeric(),
                                ])
                                ->columns(3),
                        ])
                        ->action(function (array $data, $record) {
                            $record->update(['status' => 'Completed']);
                            // Simpan data ke database
                            \App\Models\JobCost::create([
                                'tasks_id'      => $record->id,
                                'mandays'       => $data['mandays'],
                                'transports'    => $data['transports'],
                                'accomodations' => $data['accomodations'],
                            ]);
                            \App\Models\TaskFile::create([
                                'tasks_id'  => $record->id,
                                'file_name' => $data['file_name'],
                                'file'      => $data['file'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Job Cost and file saved successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-check-circle'),
                    Action::make('cancel')
                        ->label('Set to Cancel')
                        ->visible(fn($record): bool => $record->status === 'Planing')
                        ->color('danger')
                        ->form([
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status cancel...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Task::where('id', $record->id)->update([
                                'status'        => 'Cancel',
                                'notes'         => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to Cancel successfully!')
                                ->success()
                                ->send();
                        })
                        ->slideOver()
                        ->icon('heroicon-o-x-circle'),
                    Action::make('files')
                        ->label('Expenses')
                        ->color('primary')
                        ->form([
                            Forms\Components\TextInput::make('ammount')
                                ->required()
                                ->numeric(),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'MCU' => 'MCU',
                                    'Surat Sehat' => 'Surat Sehat',
                                    'Jasa' => 'Jasa',
                                    'Transport' => 'Transport',
                                    'Penginapan' => 'Penginapan',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->searchable(),
                            Forms\Components\FileUpload::make('file')
                                ->columnSpanFull()
                                ->disk('nas')
                                ->directory('expenses')
                                ->preserveFilenames()
                                ->maxSize(2048)
                                ->openable()
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\TaskExpense::create([
                                'tasks_id'      => $record->id,
                                'ammount'       => $data['ammount'],
                                'type'          => $data['type'],
                                'file'          => $data['file'],
                                'description'   => $data['description'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Data saved successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-paper-clip'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Cancel')
                        ->label('Cancel')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->form([
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status Cancel...')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'Cancel',
                                    'schedule' => null,
                                    'notes' => $data['notes'], // Update kolom note dari form modal
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make()
                        ->exporter(TaskExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TaskFilesRelationManager::class,
            TaskExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
