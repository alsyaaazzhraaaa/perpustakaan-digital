<?php

namespace App\Filament\Admin\Resources\AlatResource\Pages;

use App\Filament\Admin\Resources\AlatResource;
use Filament\Actions; // v3 menggunakan Actions
use Filament\Resources\Pages\ListRecords;

class ListAlats extends ListRecords
{
    protected static string $resource = AlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}