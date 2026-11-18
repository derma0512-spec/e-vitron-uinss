<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public string $statusFilter = 'all';

    public function setStatusFilter(string $status): void
    {
        $allowed = ['all', 'pending', 'approved', 'inactive', 'rejected'];
        $this->statusFilter = in_array($status, $allowed, true) ? $status : 'all';
        $this->resetPage();
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if ($query && $this->statusFilter !== 'all') {
            if ($this->statusFilter === 'approved') {
                $query->where(function (Builder $q): void {
                    $q->where('account_status', 'approved')
                        ->orWhereNull('account_status');
                });
            } else {
                $query->where('account_status', $this->statusFilter);
            }
        }

        return $query;
    }

    public function getHeading(): string
    {
        return 'Persetujuan Akun Pendaftar';
    }

    public function getSubheading(): ?string
    {
        return 'Verifikasi dan setujui pendaftaran akun baru Tendik maupun Mitra Luar agar dapat masuk ke portal E-VITRON.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

