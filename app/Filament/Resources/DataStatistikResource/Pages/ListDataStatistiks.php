<?php

namespace App\Filament\Resources\DataStatistikResource\Pages;

use App\Filament\Resources\DataStatistikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataStatistiks extends ListRecords
{
    protected static string $resource = DataStatistikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
