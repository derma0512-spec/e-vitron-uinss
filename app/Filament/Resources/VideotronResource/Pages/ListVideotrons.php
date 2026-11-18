<?php

namespace App\Filament\Resources\VideotronResource\Pages;

use App\Filament\Resources\VideotronResource;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ListVideotrons extends ListRecords
{
    protected static string $resource = VideotronResource::class;

    protected function applyGlobalSearchToTableQuery(Builder $query): Builder
    {
        $search = $this->getTableSearch();

        if (blank($search)) {
            return $query;
        }

        $term = trim($search);

        if (mb_strlen($term) === 1) {
            return $query->where('name', 'like', "%{$term}%");
        }

        $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });

        $query->orderByRaw('CASE WHEN name LIKE ? THEN 1 ELSE 2 END', ["%{$term}%"]);

        return $query;
    }

    public function getHeading(): string
    {
        return 'Kelola Videotron';
    }

    public function getSubheading(): ?string
    {
        return 'Pantau status operasional, lokasi, resolusi, dan kondisi fisik layar videotron.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Videotron')
                ->icon('heroicon-o-plus')
                ->slideOver()
                ->createAnother(false)
                ->modalIcon('heroicon-o-plus-circle')
                ->modalIconColor('primary')
                ->modalAlignment(Alignment::Left)
                ->modalHeading('Tambah Perangkat Videotron')
                ->modalDescription('Daftarkan layar videotron baru ke sistem inventaris E-VITRON.')
                ->modalSubmitAction(fn (StaticAction $action) => $action
                    ->label(new HtmlString('<span wire:loading.remove wire:target="callMountedAction">Simpan Perangkat</span><span wire:loading wire:target="callMountedAction">Menyimpan...</span>'))
                    ->color('primary')
                )
                ->modalCancelAction(fn (StaticAction $action) => $action
                    ->label('Batal')
                    ->color('gray')
                )
                ->modalAutofocus(false)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->modalFooterActionsAlignment(Alignment::End)
                ->extraModalWindowAttributes([
                    'class' => 'modal-ubah-data-videotron modal-tambah-videotron',
                    'tabindex' => '-1',
                ]),
        ];
    }
}
