<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VideotronPlaybackService
{
    /**
     * Mengembalikan tanggal hari ini dalam zona waktu operasional Asia/Jakarta (YYYY-MM-DD).
     */
    public function todayDate(): string
    {
        return now()->timezone('Asia/Jakarta')->toDateString();
    }

    /**
     * Batasan query untuk penayangan yang seharusnya aktif tayang pada hari ini (per pivot videotron).
     */
    public function applyActiveBroadcastConstraints(Builder $query, ?string $today = null): Builder
    {
        $date = $today ?? $this->todayDate();

        return $query
            ->where('submissions.status', 'disetujui')
            ->whereDate('submissions.start_date', '<=', $date)
            ->whereDate('submissions.end_date', '>=', $date)
            ->whereNull('submission_videotron.taken_down_at');
    }

    /**
     * Batasan query untuk pengajuan yang masa tayangnya telah berakhir.
     */
    public function applyExpiredBroadcastConstraints(Builder $query, ?string $today = null): Builder
    {
        $date = $today ?? $this->todayDate();

        return $query->where(function (Builder $expiredQuery) use ($date): void {
            $expiredQuery
                ->where('submissions.status', 'selesai')
                ->orWhere(function (Builder $approvedExpired) use ($date): void {
                    $approvedExpired
                        ->where('submissions.status', 'disetujui')
                        ->whereDate('submissions.end_date', '<', $date);
                });
        });
    }

    /**
     * Batasan query untuk daftar pengajuan yang masa tayangnya telah habis
     * dan masih memiliki minimal 1 layar videotron yang belum dikonfirmasi diturunkan.
     */
    public function applyPendingTakedownSubmissionConstraints(Builder $query, ?string $today = null): Builder
    {
        return $this->applyExpiredBroadcastConstraints($query, $today)
            ->whereHas('videotrons', function (Builder $videotronQuery): void {
                $videotronQuery->whereNull('submission_videotron.taken_down_at');
            });
    }

    /**
     * Jumlah pengajuan habis masa tayang yang masih memiliki minimal 1 layar belum diturunkan.
     */
    public function countPendingTakedownSubmissions(?string $today = null): int
    {
        return $this->applyPendingTakedownSubmissionConstraints(Submission::query(), $today)->count();
    }

    /**
     * Total slot (kombinasi pengajuan × videotron) yang habis masa tayang dan belum diceklis turun.
     */
    public function countPendingTakedownSlots(?string $today = null): int
    {
        $date = $today ?? $this->todayDate();

        return (int) DB::table('submission_videotron')
            ->join('submissions', 'submissions.id', '=', 'submission_videotron.submission_id')
            ->whereNull('submission_videotron.taken_down_at')
            ->where(function ($query) use ($date): void {
                $query
                    ->where('submissions.status', 'selesai')
                    ->orWhere(function ($approvedExpired) use ($date): void {
                        $approvedExpired
                            ->where('submissions.status', 'disetujui')
                            ->whereDate('submissions.end_date', '<', $date);
                    });
            })
            ->count();
    }

    /**
     * Daftar konten yang seharusnya sedang tayang hari ini pada 1 perangkat videotron.
     *
     * @return Collection<int, Submission>
     */
    public function getActiveBroadcastsForVideotron(Videotron $videotron, ?string $today = null): Collection
    {
        $query = $videotron->submissions()->with(['user:id,name,email']);

        $this->applyActiveBroadcastConstraints($query->getQuery(), $today);

        return $query
            ->orderBy('submissions.end_date', 'asc')
            ->orderBy('submissions.start_date', 'asc')
            ->orderBy('submissions.id', 'asc')
            ->get();
    }

    /**
     * Daftar konten yang masa tayangnya telah habis dan belum dikonfirmasi diturunkan pada 1 perangkat videotron.
     *
     * @return Collection<int, Submission>
     */
    public function getPendingTakedownsForVideotron(Videotron $videotron, ?string $today = null): Collection
    {
        $query = $videotron->submissions()
            ->with(['user:id,name,email'])
            ->whereNull('submission_videotron.taken_down_at');

        $this->applyExpiredBroadcastConstraints($query->getQuery(), $today);

        return $query
            ->orderBy('submissions.end_date', 'desc')
            ->orderBy('submissions.id', 'desc')
            ->get();
    }

    /**
     * Memetakan user_id -> nama admin untuk semua taken_down_by pada kumpulan submission (1 query, bebas N+1).
     *
     * @param  iterable<int, Submission>  $submissions
     * @return array<int, string>
     */
    public function resolveAdminNamesForSubmissions(iterable $submissions): array
    {
        $adminIds = [];

        foreach ($submissions as $submission) {
            if ($submission->relationLoaded('videotrons')) {
                foreach ($submission->videotrons as $videotron) {
                    $adminId = $videotron->pivot?->taken_down_by;
                    if ($adminId !== null) {
                        $adminIds[(int) $adminId] = (int) $adminId;
                    }
                }
            }

            if ($submission->relationLoaded('pivot')) {
                $pivotAdminId = $submission->pivot?->taken_down_by;
                if ($pivotAdminId !== null) {
                    $adminIds[(int) $pivotAdminId] = (int) $pivotAdminId;
                }
            }
        }

        if ($adminIds === []) {
            return [];
        }

        return User::query()
            ->whereIn('id', array_values($adminIds))
            ->pluck('name', 'id')
            ->mapWithKeys(fn (string $name, int|string $id): array => [(int) $id => $name])
            ->all();
    }

    /**
     * Menandai konten pada 1 titik videotron telah diturunkan secara fisik oleh admin.
     */
    public function markTakenDown(Submission $submission, Videotron $videotron, User $admin): bool
    {
        if (! $admin->isAdmin()) {
            throw new DomainException('Hanya admin yang dapat mengonfirmasi penurunan konten videotron.');
        }

        if (! in_array($submission->status, ['disetujui', 'selesai'], true)) {
            throw new DomainException('Status pengajuan ini tidak termasuk materi yang ditayangkan.');
        }

        if ($submission->display_status !== 'selesai') {
            throw new DomainException('Penurunan konten hanya dapat dikonfirmasi setelah masa tayang berakhir.');
        }

        $now = now();

        $affected = DB::table('submission_videotron')
            ->where('submission_id', $submission->id)
            ->where('videotron_id', $videotron->id)
            ->whereNull('taken_down_at')
            ->update([
                'playback_status' => 'selesai',
                'taken_down_at' => $now,
                'taken_down_by' => $admin->id,
                'updated_at' => $now,
            ]);

        return $affected > 0;
    }

    /**
     * Menandai seluruh layar videotron pada 1 pengajuan sekaligus sebagai sudah diturunkan.
     */
    public function markAllVideotronsTakenDown(Submission $submission, User $admin): int
    {
        if (! $admin->isAdmin()) {
            throw new DomainException('Hanya admin yang dapat mengonfirmasi penurunan konten videotron.');
        }

        if (! in_array($submission->status, ['disetujui', 'selesai'], true)) {
            throw new DomainException('Status pengajuan ini tidak termasuk materi yang ditayangkan.');
        }

        if ($submission->display_status !== 'selesai') {
            throw new DomainException('Penurunan konten hanya dapat dikonfirmasi setelah masa tayang berakhir.');
        }

        $now = now();

        return DB::table('submission_videotron')
            ->where('submission_id', $submission->id)
            ->whereNull('taken_down_at')
            ->update([
                'playback_status' => 'selesai',
                'taken_down_at' => $now,
                'taken_down_by' => $admin->id,
                'updated_at' => $now,
            ]);
    }
}
