<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionDecisionService;
use DomainException;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class ListSubmissions extends Page
{
    use WithPagination;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'filament.resources.submission-resource.pages.list-submissions';

    protected static ?string $title = 'Pengajuan';

    public function getHeading(): string
    {
        return 'Pengajuan';
    }

    public function getSubheading(): ?string
    {
        return 'Periksa tautan materi, ambil keputusan, dan tandai materi yang sudah diunduh.';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    // Filter & Search
    public string $search = '';

    public string $statusFilter = 'all';

    public string $dateFilter = 'all';

    public int $perPage = 10;

    // Selected Submission for Master-Detail Panel
    public ?int $selectedSubmissionId = null;

    /** True ketika halaman dibuka dari notifikasi (via ?selected= di URL). Tabel hanya menampilkan 1 baris. */
    public bool $fromNotification = false;

    public string $activeTab = 'informasi'; // 'informasi', 'riwayat', 'catatan'

    // Modals
    public bool $showApproveModal = false;

    public ?string $approveNotes = null;

    public bool $showRevisionModal = false;

    public string $revisionFeedback = '';

    public ?string $revisionError = null;

    public bool $showRejectModal = false;

    public string $rejectReason = '';

    public ?string $rejectError = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => 'all'],
        'selectedSubmissionId' => ['as' => 'drawer', 'except' => null],
    ];

    /**
     * @var array<int, string>
     */
    public const FILTERABLE_STATUSES = ['menunggu', 'revisi', 'terjadwal', 'tayang', 'disetujui'];

    public function mount(): void
    {
        // Kompatibilitas tautan lama: ?tableFilters[status][value]=menunggu
        $legacyStatus = request()->input('tableFilters.status.value');
        if (is_string($legacyStatus) && in_array($legacyStatus, self::FILTERABLE_STATUSES, true)) {
            $this->statusFilter = $legacyStatus;
        }

        if ($this->statusFilter !== 'all' && ! in_array($this->statusFilter, self::FILTERABLE_STATUSES, true)) {
            $this->statusFilter = 'all';
        }

        $selectedParam = request()->query('selected');
        $drawerParam = request()->query('drawer');

        if ($selectedParam && Submission::where('id', $selectedParam)->exists()) {
            // Dibuka dari notifikasi / tautan luar (?selected=ID) -> mode 1 baris
            $this->selectedSubmissionId = (int) $selectedParam;
            $this->fromNotification = true;
        } elseif ($drawerParam && Submission::where('id', $drawerParam)->exists()) {
            // Dibuka / di-reload saat browsing tabel biasa (?drawer=ID) -> mode tabel lengkap
            $this->selectedSubmissionId = (int) $drawerParam;
            $this->fromNotification = false;
        } else {
            $this->selectedSubmissionId = null;
            $this->fromNotification = false;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function setDateFilter(string $date): void
    {
        $this->dateFilter = $date;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->dateFilter = 'all';
        $this->resetPage();
    }

    /**
     * @return array<string, int>
     */
    public function getStatusCountsProperty(): array
    {
        $counts = Submission::getDisplayStatusCounts();
        $activeTotal = ($counts['menunggu'] ?? 0)
            + ($counts['revisi'] ?? 0)
            + ($counts['terjadwal'] ?? 0)
            + ($counts['tayang'] ?? 0);

        return array_merge([
            'all' => $activeTotal,
            'menunggu' => $counts['menunggu'] ?? 0,
            'revisi' => $counts['revisi'] ?? 0,
            'terjadwal' => $counts['terjadwal'] ?? 0,
            'tayang' => $counts['tayang'] ?? 0,
        ], $counts);
    }

    public function selectSubmission(int $id): void
    {
        $this->selectedSubmissionId = $id;
        $this->fromNotification = false;
        $this->activeTab = 'informasi';
    }

    public function closeDetail(): void
    {
        $this->selectedSubmissionId = null;
        $this->fromNotification = false;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // Modal Handlers
    public function openApproveModal(): void
    {
        $this->approveNotes = null;
        $this->showApproveModal = true;
    }

    public function confirmApprove(): void
    {
        if (! $this->selectedSubmission) {
            return;
        }

        try {
            app(SubmissionDecisionService::class)->approve($this->selectedSubmission, $this->approveNotes);

            $this->showApproveModal = false;
            $this->approveNotes = null;
            $this->fromNotification = false;

            Notification::make()
                ->title('Pengajuan Berhasil Disetujui!')
                ->body("Tiket {$this->selectedSubmission->ticket_code} disetujui untuk persiapan penayangan.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Persetujuan Gagal')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function openRevisionModal(): void
    {
        $this->revisionFeedback = '';
        $this->revisionError = null;
        $this->showRevisionModal = true;
    }

    public function closeRevisionModal(): void
    {
        $this->showRevisionModal = false;
        $this->revisionError = null;
    }

    public function updatedRevisionFeedback(): void
    {
        if (mb_strlen(trim($this->revisionFeedback)) >= 5) {
            $this->revisionError = null;
        }
    }

    public function confirmRevision(?string $feedback = null, ?string $type = null): void
    {
        if (! $this->selectedSubmission) {
            return;
        }

        if ($feedback !== null) {
            $this->revisionFeedback = $feedback;
        }

        if (mb_strlen(trim($this->revisionFeedback)) < 5) {
            $this->revisionError = 'Catatan revisi wajib diisi dengan jelas (minimal 5 karakter) agar pengaju memahami bagian yang harus diperbaiki.';

            return;
        }

        if (empty($type) || $type === 'unknown') {
            $text = strtolower(trim($this->revisionFeedback));
            $hasSurat = str_contains($text, 'surat') || str_contains($text, 'peminjaman') || str_contains($text, 'permohonan') || str_contains($text, 'stempel') || str_contains($text, 'tanda tangan') || str_contains($text, 'ttd') || str_contains($text, 'dokumen') || str_contains($text, 'pdf') || str_contains($text, 'berkas');
            $hasMateri = str_contains($text, 'drive') || str_contains($text, 'link') || str_contains($text, 'tautan') || str_contains($text, 'video') || str_contains($text, 'poster') || str_contains($text, 'durasi') || str_contains($text, 'resolusi') || str_contains($text, 'akses') || str_contains($text, 'materi') || str_contains($text, 'gdrive') || str_contains($text, 'konten');

            if (! $hasSurat && ! $hasMateri) {
                $this->revisionError = 'Revisi belum bisa dikirim! Mohon pilih bagian yang wajib diperbaiki (Surat atau Drive).';

                return;
            }

            if ($hasSurat && ! $hasMateri) {
                $type = 'surat';
            } elseif ($hasSurat && $hasMateri) {
                $type = 'keduanya';
            } else {
                $type = 'materi';
            }
        }

        $this->revisionError = null;

        try {
            app(SubmissionDecisionService::class)->requestRevision(
                $this->selectedSubmission,
                $this->revisionFeedback,
                auth()->id(),
                $type
            );

            $this->showRevisionModal = false;
            $this->revisionFeedback = '';
            $this->fromNotification = false;

            Notification::make()
                ->title('Permintaan Revisi Telah Dikirim')
                ->body("Catatan revisi untuk tiket {$this->selectedSubmission->ticket_code} berhasil dikirim ke pengaju.")
                ->warning()
                ->send();
        } catch (DomainException $e) {
            $this->revisionError = $e->getMessage();
        }
    }

    public function openRejectModal(): void
    {
        $this->rejectReason = '';
        $this->rejectError = null;
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectError = null;
    }

    public function updatedRejectReason(): void
    {
        if (mb_strlen(trim($this->rejectReason)) >= 5) {
            $this->rejectError = null;
        }
    }

    public function confirmReject(?string $reason = null): void
    {
        if (! $this->selectedSubmission) {
            return;
        }

        if ($reason !== null) {
            $this->rejectReason = $reason;
        }

        if (mb_strlen(trim($this->rejectReason)) < 5) {
            $this->rejectError = 'Alasan penolakan wajib diisi dengan jelas (minimal 5 karakter).';

            return;
        }

        $this->rejectError = null;

        try {
            app(SubmissionDecisionService::class)->reject($this->selectedSubmission, $this->rejectReason);

            $this->showRejectModal = false;
            $this->rejectReason = '';
            $this->fromNotification = false;

            Notification::make()
                ->title('Pengajuan Ditolak')
                ->body("Alasan penolakan tiket {$this->selectedSubmission->ticket_code} telah disimpan.")
                ->danger()
                ->send();
        } catch (DomainException $e) {
            $this->rejectError = $e->getMessage();
        }
    }

    public function markMaterialDownloaded(): void
    {
        if (! $this->selectedSubmission) {
            return;
        }

        try {
            app(SubmissionDecisionService::class)->markMaterialDownloaded($this->selectedSubmission);

            Notification::make()
                ->title('Materi Ditandai Sudah Diunduh')
                ->body("Materi tiket {$this->selectedSubmission->ticket_code} siap untuk penayangan.")
                ->success()
                ->send();
        } catch (DomainException $e) {
            Notification::make()
                ->title('Gagal Memperbarui Status Materi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getSelectedSubmissionProperty(): ?Submission
    {
        if (! $this->selectedSubmissionId) {
            return null;
        }

        return Submission::with(['videotrons', 'user', 'statusHistories.user', 'revisions', 'materialDownloader'])
            ->find($this->selectedSubmissionId);
    }

    public function getSubmissionsProperty(): LengthAwarePaginator
    {
        // Ketika dibuka dari notifikasi, tampilkan hanya 1 baris pengajuan yang dipilih
        if ($this->fromNotification && $this->selectedSubmissionId) {
            return Submission::query()
                ->with(['videotrons', 'user'])
                ->where('id', $this->selectedSubmissionId)
                ->paginate(1);
        }

        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $caseSql = Submission::getDisplayStatusCaseSql('submissions', $today);

        $query = Submission::query()
            ->with(['videotrons', 'user'])
            ->whereRaw("({$caseSql}) IN ('menunggu', 'revisi', 'terjadwal', 'tayang')");

        // Search
        if (! empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('ticket_code', 'like', "%{$term}%")
                    ->orWhere('title', 'like', "%{$term}%")
                    ->orWhere('pic_name', 'like', "%{$term}%")
                    ->orWhere('organization', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
            });
        }

        // Status Filter (menggunakan logika display_status yang sama dengan dashboard cards)
        if ($this->statusFilter !== 'all' && in_array($this->statusFilter, self::FILTERABLE_STATUSES, true)) {
            if ($this->statusFilter === 'disetujui') {
                $query->where('status', 'disetujui');
            } else {
                $query->whereRaw("({$caseSql}) = ?", [$this->statusFilter]);
            }
        }

        // Date Filter
        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->dateFilter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($this->dateFilter === 'active_schedule') {
            $query->where('end_date', '>=', today());
        }

        // Urutan stabil berdasarkan waktu pengajuan (terbaru di atas) agar baris tidak loncat saat status berubah
        return $query
            ->latest('created_at')
            ->latest('id')
            ->paginate($this->perPage);
    }

    public function getTotalCountProperty(): int
    {
        return Submission::count();
    }
}
