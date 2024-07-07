<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistResource\Pages;
use App\Models\Wishlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->disabled(fn () => Auth::user()->hasRole('user')), // Disable for user role
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->visible(fn () => Auth::user()->hasAnyRole(['admin', 'manager'])),
                Tables\Columns\ImageColumn::make('book.cover_image')
                ->label('Cover')
                ->url(fn ($record) => $record->book->cover_image),
                Tables\Columns\TextColumn::make('book.title')
                ->label('Title'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user')
                ->relationship('user', 'name')
                ->searchable()
                ->multiple()
                ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'manager']))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => Auth::user()->hasAnyRole(['admin']) || (Auth::user()->hasRole('user') && $record->user_id === Auth::id())),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => Auth::user()->hasAnyRole(['admin']) || $record->user_id === Auth::id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->hasAnyRole(['admin'])),
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
            'index' => Pages\ListWishlists::route('/'),
            'edit' => Pages\EditWishlist::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('user')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false; // Disable manual creation of wishlist items
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['admin', 'manager']) || $record->user_id === Auth::id();
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['admin']) || (Auth::user()->hasRole('user') && $record->user_id === Auth::id());
    }
}
