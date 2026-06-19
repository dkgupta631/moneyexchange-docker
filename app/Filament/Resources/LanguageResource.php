<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Filament\Resources\LanguageResource\RelationManagers;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    public static function getModelLabel(): string{
        return __('message.Language');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('message.Language name'))
                    ->prefixIcon('heroicon-m-arrow-long-right')
                    ->rule(function ($record) {
                        return $record ? 'unique:languages,name,' . $record->id : 'unique:languages,name';
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(__('message.Language Code'))
                    ->prefixIcon('heroicon-m-flag')
                    ->rule(function ($record) {
                        return $record ? 'unique:languages,code,' . $record->id : 'unique:languages,code';
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->label(__('message.Order'))
                    ->integer()
                    ->prefixIcon('heroicon-m-list-bullet')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->label(__('message.Status'))
                            ->default('0')
                            ->onIcon('heroicon-m-bolt')
                            ->onColor('success')
                    ->required(),
                Forms\Components\FileUpload::make('icon')
                    ->label(__('message.Icon'))
                        ->required()
                        ->directory('images/flages')
                        ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Serial_number')
                    ->label(__('message.Serial number'))
                    ->badge()
                    ->state(fn($column) => $column->getRowLoop()->iteration),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.Language name'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('icon')
                    ->label(__('message.Icon'))
                    ->circular(false) // disable circle
                    ->extraImgAttributes([
                        'class' => 'object-cover rounded-md', // optional: cover + rounded rectangle
                    ]),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('message.Language Code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('message.Order'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(__('message.Status'))
                    ->boolean(),
            ])
            ->defaultSort('order', 'ASC')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('message.Edit'))->modalButton(__('message.Save changes')),
                Tables\Actions\DeleteAction::make()->label(__('message.Delete')),
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
            'index' => Pages\ListLanguages::route('/'),
            // 'create' => Pages\CreateLanguage::route('/create'),
            // 'view' => Pages\ViewLanguage::route('/{record}'),
            // 'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
