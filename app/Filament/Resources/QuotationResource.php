<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Quotation;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuotationResource\RelationManagers\QuotationfilesRelationManager;
use App\Filament\Resources\QuotationResource\RelationManagers\TasksRelationManager;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Project Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Quotation')
                    ->description()
                    ->schema([
                        Forms\Components\TextInput::make('quotation_number')
                            ->label('Quotation Number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn() => \App\Models\Quotation::generateQuotationNumber())
                            ->readOnly(),
                        Forms\Components\DatePicker::make('request_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('quotation_price')
                            ->label('Harga')
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('customers_id')
                            ->label('Customer')
                            ->relationship(name: 'customers', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->name . ' - ' . ($record->companies->company_name ?? 'N/A');
                            })
                            ->createOptionForm([
                                Forms\Components\Section::make('Form Customer')
                                    ->description('please fill the column')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->required(),
                                        Forms\Components\Select::make('companies_id')
                                            ->relationship('companies', 'company_name')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->getOptionLabelFromRecordUsing(function ($record) {
                                                return $record->company_name . ' - Location: ' . ($record->villages->name ?? 'N/A');
                                            }),
                                        Forms\Components\Select::make('customer_type')
                                            ->options([
                                                'Perusahaan' => 'Perusahaan',
                                                'Perorangan' => 'Perorangan',
                                            ])
                                            ->required()
                                            ->searchable()
                                            ->live(),
                                    ])
                            ]),
                        Forms\Components\TextInput::make('project_name')
                            ->columnSpan(2)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'Project' => 'Project',
                                'Mandays' => 'Mandays',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date', true),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Open' => 'Open',
                                'Payment Process' => 'Payment Process',
                                'Completed' => 'Completed',
                            ])
                            ->searchable()
                            ->required()
                            ->default('Open'),
                        Forms\Components\Select::make('employees_id')
                            ->label('PIC')
                            ->options(
                                Employee::whereNotNull('user_id')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\RichEditor::make('notes')
                            ->maxLength(65535)
                            ->columnSpan(3),
                    ])
                    ->columns('3'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quotation_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quotation_payment.payment_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.companies.company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_tasks')
                    ->label('Total Tasks')
                    ->getStateUsing(fn(Quotation $quotation) => $quotation->tasks()->count()),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Task Progress'),
                Tables\Columns\TextColumn::make('quotation_price')
                    ->label('Global Price')
                    ->searchable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('tasks.price')
                    ->label('Price Tasks')
                    ->searchable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => [
                        'Open'              => 'primary',
                        'Payment Process'   => 'warning',
                        'Completed'         => 'success',
                        'Cancel'            => 'danger',
                    ][$state] ?? 'secondary')
                    ->icon(fn($state) => [
                        'Open'              => 'heroicon-o-clock',
                        'Payment Process'   => 'heroicon-o-credit-card',
                        'Completed'         => 'heroicon-o-check-circle',
                        'Cancel'             => 'heroicon-o-x-circle',
                    ][$state] ?? 'secondary'),
                Tables\Columns\TextColumn::make('employees.name')
                    ->icon('heroicon-o-user-circle')
                    ->badge()
                    ->label('PIC')
                    ->searchable(),
            ])
            ->filters([
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
                    Tables\Actions\DeleteAction::make(),
                    Action::make('cancel')
                        ->label('Set to Cancel')
                        ->color('danger')
                        ->visible(fn($record) => $record->status !== 'Cancel')
                        ->form([
                            Forms\Components\RichEditor::make('notes')
                                ->label('Catatan')
                                ->placeholder('Masukkan catatan untuk status cancel...')
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            // Simpan data ke database
                            \App\Models\Quotation::where('id', $record->id)->update([
                                'status'        => 'Cancel',
                                'notes'         => $data['notes'],
                            ]);

                            // Tampilkan notifikasi sukses
                            Notification::make()
                                ->title('Set to Cancel successfully!')
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-o-x-circle')
                        ->slideOver(),
                    Action::make('activities')
                        ->url(fn($record) => QuotationResource::getUrl('activities', ['record' => $record]))
                        ->icon('heroicon-o-clock')
                        ->color('secondary')
                        ->label('Logs'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('payment')
                        ->label('Payment Process')
                        ->color('primary')
                        ->form([
                            Forms\Components\DatePicker::make('payment_date')
                                ->required()
                                ->native(false),
                            Forms\Components\TextInput::make('payment_number')
                                ->required(),
                            Forms\Components\RichEditor::make('notes')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            try {
                                foreach ($records as $record) {
                                    // Validasi data dan simpan pembayaran
                                    \App\Models\QuotationPayment::create([
                                        'quotations_id'  => $record->id,
                                        'payment_date'   => $data['payment_date'],
                                        'payment_number' => $data['payment_number'],
                                        'notes'          => $data['notes'],
                                        'users_id'       => auth::user()->id,
                                    ]);
                                }

                                // Notifikasi sukses
                                Notification::make()
                                    ->title('Payment success!')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error processing payment')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        })
                        ->icon('heroicon-o-credit-card')
                        ->slideOver(),
                    //Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('delete')
                        ->label('Delete Selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->action(function (Collection $records) {
                            foreach ($records as $quotation) {
                                // 🗂 Hapus semua file terkait di quotation_files
                                $quotation->quotation_files()->each(function ($file) {
                                    if (Storage::disk('public')->exists($file->file)) {
                                        Storage::disk('public')->delete($file->file);
                                    }
                                    $file->delete();
                                });

                                // 📋 Hapus semua tasks terkait
                                $quotation->tasks()->each(function ($task) {
                                    // 🗂 Hapus semua file terkait di task_files
                                    $task->task_files()->each(function ($file) {
                                        if (Storage::disk('public')->exists($file->file)) {
                                            Storage::disk('public')->delete($file->file);
                                        }
                                        $file->delete();
                                    });

                                    // Hapus task
                                    $task->delete();
                                });

                                // 🧾 Hapus quotation
                                $quotation->delete();
                                Notification::make()
                                    ->title('Files deleted successfully!')
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
                Section::make('Detail Quotation')
                    ->schema([
                        Fieldset::make('Informasi Quotation')
                            ->schema([
                                TextEntry::make('quotation_number')
                                    ->columnSpan('full')
                                    ->badge(),
                                TextEntry::make('customers.name')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary')
                                    ->label('Nama'),
                                TextEntry::make('customers.companies.company_name')
                                    ->label('Perusahaan'),
                                TextEntry::make('request_date')
                                    ->icon('heroicon-o-calendar')
                                    ->iconColor('primary')
                                    ->date(),
                                TextEntry::make('category')
                                    ->badge(),
                                TextEntry::make('project_name'),
                                TextEntry::make('quotation_price')
                                    ->label('Quotation Price')
                                    ->badge()
                                    ->money('IDR'),
                                TextEntry::make('tasks_price')
                                    ->label('Price Tasks')
                                    ->badge()
                                    ->money('IDR'),
                                TextEntry::make('start_date')
                                    ->icon('heroicon-o-calendar')
                                    ->iconColor('primary')
                                    ->date(),
                                TextEntry::make('end_date')
                                    ->icon('heroicon-o-calendar')
                                    ->iconColor('primary')
                                    ->date(),
                                TextEntry::make('employees.name')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary')
                                    ->label('PIC'),
                                TextEntry::make('completion_percentage')
                                    ->label('Progress'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->label('Status'),
                                TextEntry::make('notes')
                                    ->label('Note')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ])->columns(3),
                        Fieldset::make('Informasi Payment')
                            ->schema([
                                TextEntry::make('quotation_payment.payment_number')
                                    ->label('Nomor Penagihan')
                                    ->badge(),
                                TextEntry::make('quotation_payment.payment_date')
                                    ->label('Tanggal Penagihan')
                                    ->date(),
                                TextEntry::make('quotation_payment.user.name')
                                    ->label('Proses oleh'),
                                TextEntry::make('quotation_payment.notes')
                                    ->columnSpanFull()
                                    ->markdown()
                                    ->label('Notes'),
                            ])->columns(3),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuotationfilesRelationManager::class,
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'view' => Pages\ViewQuotation::route('/{record}'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
            'activities' => Pages\ListQuotationActivities::route('/{record}/activities'),
        ];
    }
}
