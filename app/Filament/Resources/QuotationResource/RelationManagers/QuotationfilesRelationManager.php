<?php

namespace App\Filament\Resources\QuotationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Storage;

class QuotationfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotation_files';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file')
                    ->columnSpanFull()
                    ->disk('nas')
                    ->directory('quotations')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->openable()
                    ->acceptedFileTypes(['application/pdf'])
                    ->url(fn($record) => $record && $record->file ? 'http://192.168.20.244/files/' . $record->file : null)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('file')
                        ->label('Open File')
                        ->url(fn($record) => 'http://192.168.20.244/files/' . $record->file)
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document')
                        ->color('primary'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->action(function ($record) {
                        // Hapus file dengan disk storage
                        if ($record->file && Storage::disk('public')->exists($record->file)) {
                            Storage::disk('public')->delete($record->file);
                        }
                        // Hapus data dari database
                        $record->delete();
                    }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
