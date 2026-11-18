<?php

namespace App\Filament\Resources\VideotronResource\Pages;

use App\Filament\Resources\VideotronResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideotron extends EditRecord
{
    protected static string $resource = VideotronResource::class;

    public function getHeading(): string
    {
        return 'Ubah Data Videotron';
    }

    public function getSubheading(): ?string
    {
        return 'Perbarui spesifikasi, lokasi, atau kondisi teknis perangkat videotron.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
