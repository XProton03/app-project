<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\RelationManagers\ProjectFilesRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\ProjectTeamsRelationManager;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ProjectResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Project';
    protected static ?string $label = 'Project';
    protected static ?string $slug = 'projects';
    protected static ?int $navigationSort = 11;

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
                Forms\Components\Section::make('Form Quotation')
                    ->description()
                    ->schema([
                        Forms\Components\TextInput::make('contract_no')
                            ->label('No. Kontrak')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('customers_id')
                            ->label('Customer')
                            ->relationship(name: 'customers', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->name . ' - ' . ($record->companies->company_name ?? 'N/A');
                            }),
                        Forms\Components\TextInput::make('project_name')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->maxLength(65535)
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date', true),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric(),
                        Forms\Components\Select::make('pic')
                            ->label('PIC')
                            ->options(
                                Employee::whereNotNull('user_id')
                                    ->pluck('name', 'name')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Open' => 'Open',
                                'Running' => 'Running',
                                'Stoped' => 'Stoped',
                            ])
                            ->searchable()
                            ->required()
                            ->default('Open'),
                        Forms\Components\RichEditor::make('notes')
                            ->maxLength(65535)
                            ->columnSpan(2),
                    ])
                    ->columns('2')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->searchable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => [
                        'Open'      => 'primary',
                        'Running'   => 'success',
                        'Stoped'    => 'danger',
                    ][$state] ?? 'secondary')
                    ->icon(fn($state) => [
                        'Open'      => 'heroicon-o-clock',
                        'Running'   => 'heroicon-o-credit-card',
                        'Stoped'    => 'heroicon-o-check-circle',
                    ][$state] ?? 'secondary'),
                Tables\Columns\TextColumn::make('pic')
                    ->icon('heroicon-o-user-circle')
                    ->badge()
                    ->label('PIC')
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

    public static function getRelations(): array
    {
        return [
            ProjectFilesRelationManager::class,
            ProjectTeamsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
