<?php

namespace App\Http\Middleware;

use App\Models\Submission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicantRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Administrator tidak dapat mengakses portal pemohon.',
                    'redirect' => url('/admin/submissions'),
                ], 403);
            }

            return $this->handleAdminRedirect($request);
        }

        if (Auth::check() && (Auth::user()->account_status ?? 'approved') !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda belum disetujui atau telah dinonaktifkan oleh Admin PUSTIKOM.');
        }

        return $next($request);
    }

    /**
     * Redirect administrator to the appropriate admin panel section based on the requested applicant URL.
     */
    protected function handleAdminRedirect(Request $request): Response
    {
        // 1. Jika Admin mengakses detail/cetak tiket pengaju: /pengajuan/{ticket_code}
        $ticketCode = $request->route('ticket_code');
        if (is_string($ticketCode) && ! empty(trim($ticketCode))) {
            $submission = Submission::where('ticket_code', trim($ticketCode))->first();

            if ($submission) {
                // Skenario cetak bukti pengajuan
                if ($request->is('*/cetak*')) {
                    return redirect()->route('admin.submissions.print-pdf', ['id' => $submission->id]);
                }

                // Cek apakah tiket sudah selesai/ditolak/batal (masuk Riwayat & Rekap)
                $isArchived = in_array($submission->status, ['selesai', 'ditolak', 'dibatalkan'], true)
                    || ($submission->status === 'disetujui' && $submission->end_date && $submission->end_date->isPast());

                if ($isArchived) {
                    return redirect('/admin/history-and-recap?drawer='.$submission->id);
                }

                return redirect('/admin/submissions?selected='.$submission->id);
            }

            return redirect('/admin/submissions');
        }

        // 2. Jika Admin mengakses form pengajuan baru (/pengajuan/baru) atau simpan
        if ($request->is('pengajuan/baru*') || $request->is('pengajuan/simpan*')) {
            return redirect('/admin/submissions');
        }

        // 3. Jika Admin mengakses daftar pengajuan dengan filter status atau pencarian
        $status = $request->query('status');
        if (is_string($status) && ! empty($status)) {
            $cleanStatus = strtolower(trim($status));

            if (in_array($cleanStatus, ['selesai', 'ditolak', 'dibatalkan'], true)) {
                return redirect('/admin/history-and-recap?status='.urlencode($cleanStatus));
            }

            if (in_array($cleanStatus, ['menunggu', 'revisi', 'disetujui', 'terjadwal', 'tayang'], true)) {
                return redirect('/admin/submissions?statusFilter='.urlencode($cleanStatus));
            }
        }

        $search = $request->query('search');
        if (is_string($search) && ! empty(trim($search))) {
            return redirect('/admin/submissions?search='.urlencode(trim($search)));
        }

        // 4. Jika Admin mengakses notifikasi pengaju (/notifikasi*)
        if ($request->is('notifikasi*')) {
            return redirect('/admin');
        }

        // 5. Default redirect untuk rute /pengajuan lainnya
        return redirect('/admin/submissions');
    }
}
