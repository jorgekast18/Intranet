<?php

namespace App\Filament\Personal\Resources\TimeSheetResource\Pages;

use App\Filament\Personal\Resources\TimeSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeSheet extends EditRecord
{
    protected static string $resource = TimeSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
