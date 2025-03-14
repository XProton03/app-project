<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Collection;

class EmployementFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'employement_files';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('file')
                    ->required()
                    ->columnSpan(2)
                    ->preserveFilenames()
                    ->maxSize(5120)
                    ->openable()
                    ->disk('nas')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->directory('employees')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->deleteUploadedFileUsing(function ($file, $record) {
                        if ($record && $record->file) {
                            // Hapus file lama
                            Storage::disk('nas')->delete($record->file);
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file')
            ->columns([
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('file')
                    ->label('File')
                    ->url(fn($record) => asset('storage/uploads/' . $record->file))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        try {
                            // Hapus file dengan disk storage
                            if ($record->file && Storage::disk('nas')->exists($record->file)) {
                                Storage::disk('nas')->delete($record->file);
                            }
                            Notification::make()
                                ->title('Files deleted successfully!')
                                ->success()
                                ->send();
                            // Hapus data dari database
                            $record->delete();
                        } catch (\Exception $e) {
                            // Kirim notifikasi jika terjadi error
                            Notification::make()
                                ->title('Error deleting file!')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('delete_files')
                        ->label('Delete Files')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                // Hapus file dari storage
                                Storage::disk('nas')->delete($record->file);

                                // Hapus record dari database
                                $record->delete();
                            }
                            Notification::make()
                                ->title('Files deleted successfully!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ]),
            ]);
    }
}
