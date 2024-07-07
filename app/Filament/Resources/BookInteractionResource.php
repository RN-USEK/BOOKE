<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookInteractionResource\Pages;
use App\Filament\Resources\BookInteractionResource\RelationManagers;
use App\Models\BookInteraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookInteractionResource extends Resource
{
    protected static ?string $model = BookInteraction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Forms\Components\Select::make('interaction_type')
                    ->options([
                        'view' => 'View',
                        'purchase' => 'Purchase',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('book.title'),
                Tables\Columns\TextColumn::make('interaction_type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBookInteractions::route('/'),
            'create' => Pages\CreateBookInteraction::route('/create'),
            'edit' => Pages\EditBookInteraction::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return false;
    }
}
