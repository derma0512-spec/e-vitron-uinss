<?php

namespace App\Filament\Resources\VideotronResource\Pages;

use App\Filament\Resources\VideotronResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVideotron extends CreateRecord
{
    protected static string $resource = VideotronResource::class;

    public function getHeading(): string
    {
        return 'Tambah Videotron';
    }

    public function getSubheading(): ?string
    {
        return 'Daftarkan perangkat videotron baru ke sistem monitoring kampus.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
