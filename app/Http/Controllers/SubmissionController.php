<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\SubmitRevisionRequest;
use App\Models\Submission;
use App\Models\User;
use App\Models\Videotron;
use Dompdf\Dompdf;
use Dompdf\Options;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

class SubmissionController extends Controller
{
    /**
     * Menampilkan daftar pengajuan milik pengguna yang sedang login beserta filter status dan pencarian.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $currentStatus = $request->query('status');
        $search = $request->query('search');

        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $caseSql = Submission::getDisplayStatusCaseSql('submissions', $today);

        $query = Submission::where('user_id', $user->id)
            ->with('videotrons')
            ->latest();

        if ($currentStatus) {
            if ($currentStatus === 'tayang') {
                $query->whereRaw("({$caseSql}) = 'tayang'");
            } elseif ($currentStatus === 'disetujui' || $currentStatus === 'terjadwal') {
                $query->whereRaw("({$caseSql}) IN ('terjadwal', 'disetujui')");
            } elseif (in_array($currentStatus, ['menunggu', 'revisi', 'selesai', 'ditolak', 'dibatalkan'], true)) {
                $query->whereRaw("({$caseSql}) = ?", [$currentStatus]);
            }
        }

        if (! empty(trim((string) $search))) {
            $term = trim((string) $search);
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('ticket_code', 'like', "%{$term}%")
                    ->orWhere('organization', 'like', "%{$term}%")
                    ->orWhereHas('videotrons', function ($vq) use ($term) {
                        $vq->where('name', 'like', "%{$term}%")
                            ->orWhere('location', 'like', "%{$term}%");
                    });
            });
        }

        $openTicket = $request->query('open');
        $fromNotification = false;

        $perPage = (int) $request->query('per_page', 9);
        if (! in_array($perPage, [6, 9, 12, 18, 24], true)) {
            $perPage = 9;
        }

        $submissions = $query->paginate($perPage)->withQueryString();

        $displayCounts = Submission::getDisplayStatusCounts(null, null, $user->id);
        $statusCounts = array_merge([
            'all' => array_sum($displayCounts),
        ], $displayCounts);
        $statusCounts['disetujui'] = $displayCounts['terjadwal'] ?? 0;

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('pengajuan.partials.submissions-grid', compact('submissions', 'statusCounts', 'currentStatus', 'search', 'perPage', 'fromNotification', 'openTicket'));
        }

        return view('pengajuan.index', compact('submissions', 'statusCounts', 'currentStatus', 'search', 'perPage', 'fromNotification', 'openTicket'));
    }

    /**
     * Menampilkan formulir pengajuan bertahap (Wizard) jika layanan sedang dibuka.
     */
    public function create(): View|RedirectResponse
    {
        if (! setting('service_active', true)) {
            return redirect()->route('pengajuan.index');
        }

        $videotrons = Videotron::orderBy('id')->get();

        return view('pengajuan.wizard', compact('videotrons'));
    }

    /**
     * Memproses penyimpanan pengajuan baru ke database dan membuat kode tiket unik.
     */
    public function store(StoreSubmissionRequest $request): RedirectResponse
    {
        if (! setting('service_active', true)) {
            return redirect()->route('pengajuan.index');
        }

        // Cek batasan maksimal hari tayang jika kebijakan pembatasan aktif (> 0)
        $maxDisplayDays = (int) setting('max_display_days', 0);
        if ($maxDisplayDays > 0) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            if ($startDate->diffInDays($endDate) + 1 > $maxDisplayDays) {
                return back()->withInput()->withErrors([
                    'end_date' => "Maksimal durasi penayangan per pengajuan adalah {$maxDisplayDays} hari.",
                ]);
            }
        }

        // Verifikasi bahwa videotron yang dipilih tidak dalam kondisi gangguan atau nonaktif
        $invalidVideotrons = Videotron::whereIn('id', $request->videotrons)
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhereIn('condition', ['gangguan', 'dalam_perbaikan']);
            })
            ->count();

        if ($invalidVideotrons > 0) {
            return back()->withInput()->withErrors([
                'videotrons' => 'Satu atau lebih perangkat videotron yang Anda pilih sedang dalam perbaikan atau tidak aktif.',
            ]);
        }

        $submission = DB::transaction(function () use ($request) {
            $suratPath = null;
            if ($request->hasFile('surat_peminjaman')) {
                $suratPath = $request->file('surat_peminjaman')->store('surat_peminjaman', 'local');
            }
            // Buat kode tiket unik: VTR-YYYYMM-XXXX (memanfaatkan indeks created_at)
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            $count = Submission::whereBetween('created_at', [$monthStart, $monthEnd])
                ->lockForUpdate()
                ->count() + 1;
            $ticketCode = 'VTR-'.now()->format('Ym').'-'.str_pad($count, 4, '0', STR_PAD_LEFT);

            // Double check uniqueness
            while (Submission::where('ticket_code', $ticketCode)->exists()) {
                $count++;
                $ticketCode = 'VTR-'.now()->format('Ym').'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $submission = Submission::create([
                'ticket_code' => $ticketCode,
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content_type' => $request->content_type,
                'description' => $request->description,
                'pic_name' => $request->pic_name,
                'organization' => $request->organization,
                'whatsapp' => $request->whatsapp,
                'gdrive_link' => $request->gdrive_link,
                'surat_peminjaman' => $suratPath,
                'video_duration_seconds' => $request->content_type === 'video' ? $request->video_duration_seconds : null,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'menunggu',
            ]);

            // Hubungkan ke perangkat videotron yang dipilih
            $submission->videotrons()->attach($request->videotrons);

            // Catat riwayat status awal
            $submission->statusHistories()->create([
                'user_id' => Auth::id(),
                'from_status' => null,
                'to_status' => 'menunggu',
                'notes' => 'Pengajuan baru berhasil dikirimkan oleh pengaju.',
            ]);

            return $submission;
        });

        $startDate = $submission->start_date?->locale('id');
        $endDate = $submission->end_date?->locale('id');
        if ($startDate && $endDate) {
            if ($startDate->isSameDay($endDate)) {
                $periodFormatted = $startDate->translatedFormat('d M Y');
            } elseif ($startDate->isSameMonth($endDate) && $startDate->isSameYear($endDate)) {
                $periodFormatted = $startDate->translatedFormat('d').' - '.$endDate->translatedFormat('d M Y');
            } elseif ($startDate->isSameYear($endDate)) {
                $periodFormatted = $startDate->translatedFormat('d M').' - '.$endDate->translatedFormat('d M Y');
            } else {
                $periodFormatted = $startDate->translatedFormat('d M Y').' - '.$endDate->translatedFormat('d M Y');
            }
        } else {
            $periodFormatted = '-';
        }

        $submission->load('videotrons:id,name');

        return redirect()->route('pengajuan.create')
            ->with('submission_success', [
                'ticket_code' => $submission->ticket_code,
                'title' => $submission->title,
                'videotrons' => $submission->videotrons->pluck('name')->toArray(),
                'period' => $periodFormatted,
                'status' => 'Menunggu',
            ])
            ->with('success', 'Pengajuan berhasil dikirim! Kode Tiket: '.$submission->ticket_code);
    }

    /**
     * Menampilkan detail pengajuan (sebagai partial laci slide-over atau redirect ke halaman daftar).
     */
    public function show(Request $request, string $ticket_code): View|RedirectResponse
    {
        $submission = Submission::where('ticket_code', $ticket_code)
            ->where('user_id', Auth::id())
            ->with(['videotrons', 'revisions', 'statusHistories.user'])
            ->firstOrFail();

        if ($request->query('drawer') || $request->ajax() || $request->wantsJson()) {
            return view('pengajuan.partials.detail-drawer', compact('submission'));
        }

        $statusTab = match ($submission->display_status) {
            'terjadwal', 'disetujui' => 'disetujui',
            'revisi' => 'revisi',
            'tayang' => 'tayang',
            'ditolak' => 'ditolak',
            'selesai' => 'selesai',
            default => 'menunggu',
        };

        return redirect()->route('pengajuan.index', [
            'open' => $submission->ticket_code,
            'status' => $statusTab,
        ]);
    }

    /**
     * Mencetak bukti pengajuan penayangan dalam format PDF A4 Portrait menggunakan DomPDF.
     */
    public function printProof(string $ticket_code): Response
    {
        $submission = Submission::where('ticket_code', $ticket_code)
            ->where('user_id', Auth::id())
            ->with(['videotrons', 'user'])
            ->firstOrFail();

        $logoPath = public_path('images/uin-ssc-logo-official.png');
        if (! is_file($logoPath)) {
            $logoPath = public_path('images/uin-ssc-logo-transparent.png');
        }
        $logoBase64 = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $printedAt = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i').' WIB';

        $html = view('pengajuan.bukti-pengajuan-pdf', [
            'submission' => $submission,
            'logoBase64' => $logoBase64,
            'printedAt' => $printedAt,
        ])->render();

        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'bukti-pengajuan-'.strtolower($submission->ticket_code).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Membatalkan pengajuan penayangan yang masih berstatus menunggu oleh pengaju.
     */
    public function cancel(Request $request, string $ticket_code): JsonResponse|RedirectResponse
    {
        $submission = Submission::where('ticket_code', $ticket_code)
            ->where('user_id', Auth::id())
            ->first();

        if (! $submission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengajuan tidak ditemukan.',
                ], 404);
            }

            abort(404);
        }

        if ($submission->status !== 'menunggu') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan dengan status Menunggu yang dapat dibatalkan.',
                ], 422);
            }

            return back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat dibatalkan.');
        }

        $submission->update(['status' => 'dibatalkan']);

        $submission->statusHistories()->create([
            'user_id' => Auth::id(),
            'from_status' => 'menunggu',
            'to_status' => 'dibatalkan',
            'notes' => 'Dibatalkan oleh pengaju.',
        ]);

        // Notifikasi ke seluruh Admin aktif
        $admins = User::activeAdmins()->get();
        foreach ($admins as $admin) {
            $notif = Notification::make()
                ->title('Pengajuan Dibatalkan')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->body("Tiket {$submission->ticket_code} ({$submission->title}) telah dibatalkan oleh pihak pengaju.")
                ->actions([
                    Action::make('view')
                        ->label('Lihat Riwayat')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.pages.history-and-recap', ['selected' => $submission->id]))
                        ->markAsRead(),
                ]);

            $admin->notifyNow($notif->toDatabase());
        }

        session()->flash('success', 'Pengajuan berhasil dibatalkan.');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dibatalkan.',
                'ticket_code' => $submission->ticket_code,
            ]);
        }

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * Memproses pengiriman perbaikan revisi berkas surat atau tautan Google Drive oleh pengaju.
     */
    public function submitRevision(SubmitRevisionRequest $request, string $ticket_code): RedirectResponse
    {
        $submission = $request->targetSubmission() ?? Submission::where('ticket_code', $ticket_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! Gate::allows('submitRevision', $submission)) {
            return back()->with('error', 'Pengajuan ini tidak sedang dalam status revisi.');
        }

        $latestRevision = $submission->latestRevision;

        $newSuratPath = null;
        if ($request->hasFile('surat_peminjaman')) {
            $newSuratPath = $request->file('surat_peminjaman')->store('surat_peminjaman', 'local');
        }

        if ($latestRevision) {
            $latestRevision->update([
                'user_reply' => $request->user_reply,
                'updated_gdrive_link' => $request->filled('gdrive_link') ? $request->gdrive_link : null,
                'updated_surat_peminjaman' => $newSuratPath,
                'resolved_at' => now(),
            ]);
        }

        $updateData = [
            'status' => 'menunggu',
        ];
        if ($request->filled('gdrive_link')) {
            $updateData['gdrive_link'] = $request->gdrive_link;
        }
        if ($newSuratPath) {
            $updateData['surat_peminjaman'] = $newSuratPath;
        }

        $submission->update($updateData);

        $submission->statusHistories()->create([
            'user_id' => Auth::id(),
            'from_status' => 'revisi',
            'to_status' => 'menunggu',
            'notes' => 'Pengaju telah mengirimkan revisi: '.$request->user_reply,
        ]);

        // Notifikasi ke seluruh Admin aktif
        $admins = User::activeAdmins()->get();
        foreach ($admins as $admin) {
            $notif = Notification::make()
                ->title('Perbaikan Revisi Dikirim')
                ->icon('heroicon-o-arrow-path')
                ->iconColor('info')
                ->body("Pengaju telah mengirim perbaikan untuk tiket {$submission->ticket_code} ({$submission->title}).")
                ->actions([
                    Action::make('view')
                        ->label('Periksa Berkas')
                        ->button()
                        ->size('xs')
                        ->url(route('filament.admin.resources.submissions.index', ['selected' => $submission->id]))
                        ->markAsRead(),
                ]);

            $admin->notifyNow($notif->toDatabase());
        }

        return back()->with('success', 'Perbaikan berhasil dikirim kembali ke admin!');
    }

    /**
     * Mengalirkan berkas PDF surat permohonan secara aman (hanya pemilik pengajuan & Admin).
     */
    public function downloadSurat(Request $request, string $ticket_code): BinaryFileResponse
    {
        $submission = Submission::where('ticket_code', $ticket_code)->firstOrFail();

        Gate::authorize('downloadSurat', $submission);

        if (! $submission->surat_peminjaman) {
            abort(404);
        }

        $relativePath = ltrim((string) $submission->surat_peminjaman, '/');
        if (str_contains($relativePath, '..') || str_contains($relativePath, '\\') || str_contains($relativePath, "\0")) {
            abort(404);
        }

        if (Storage::disk('local')->exists($relativePath)) {
            $fullPath = Storage::disk('local')->path($relativePath);
        } elseif (Storage::disk('public')->exists($relativePath)) {
            $fullPath = Storage::disk('public')->path($relativePath);
        } else {
            abort(404);
        }

        $safeFilename = $this->sanitizePdfFilename($submission->surat_peminjaman_filename, 'Surat-Permohonan.pdf');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $safeFilename,
            'Surat-Permohonan.pdf'
        );

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-transform, max-age=0',
        ]);
    }

    /**
     * Membersihkan nama berkas PDF agar aman pada header HTTP Content-Disposition.
     */
    private function sanitizePdfFilename(?string $filename, string $fallback = 'Surat-Permohonan.pdf'): string
    {
        $clean = basename(str_replace('\\', '/', (string) $filename));
        $clean = preg_replace('/[\x00-\x1F\x7F"\';\\\\\/]+/', '', $clean) ?? '';
        $clean = trim($clean, ". \t\n\r\0\x0B");

        if ($clean === '') {
            return $fallback;
        }

        if (! str_ends_with(strtolower($clean), '.pdf')) {
            $clean .= '.pdf';
        }

        return $clean;
    }
}
