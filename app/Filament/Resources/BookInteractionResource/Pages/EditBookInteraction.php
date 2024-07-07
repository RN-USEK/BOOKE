<?php

namespace App\Filament\Resources\BookInteractionResource\Pages;

use App\Filament\Resources\BookInteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookInteraction extends EditRecord
{
    protected static string $resource = BookInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
