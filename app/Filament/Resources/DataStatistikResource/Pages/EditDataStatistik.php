<?php

namespace App\Filament\Resources\DataStatistikResource\Pages;

use App\Filament\Resources\DataStatistikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataStatistik extends EditRecord
{
    protected static string $resource = DataStatistikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
