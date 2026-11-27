<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman daftar notifikasi lengkap milik pengaju.
     * Dialihkan langsung ke slide-over drawer notifikasi pada halaman Pengajuan Saya.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('pengajuan.index', ['notif_drawer' => 1]);
    }

    /**
     * Buka notifikasi, tandai sudah dibaca, dan arahkan ke detail pengajuan terkait.
     */
    public function readAndRedirect(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $ticketCode = $notification->data['ticket_code'] ?? null;

        if ($ticketCode) {
            $submission = Submission::where('ticket_code', $ticketCode)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($submission) {
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
        }

        if (! empty($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return redirect()->route('notifikasi.index');
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi telah ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi milik pengaju sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Hapus satu notifikasi milik pengaju.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->where('data->event', '!=', 'submitted')->count(),
                'total_count' => $request->user()->notifications()->where('data->event', '!=', 'submitted')->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Hapus semua notifikasi milik pengaju.
     */
    public function clearAll(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->notifications()->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'total_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }

    /**
     * Polling endpoint untuk mengecek jumlah unread dan notifikasi terbaru milik pengaju.
     */
    public function checkUnread(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'unread_count' => 0,
                'total_count' => 0,
                'notifications' => [],
                'drawer_notifications' => [],
                'latest' => null,
            ]);
        }

        $unreadCount = $user->unreadNotifications()
            ->where('data->event', '!=', 'submitted')
            ->count();

        $totalCount = $user->notifications()
            ->where('data->event', '!=', 'submitted')
            ->count();

        // Ambil notifikasi terbaru (unread & read) untuk sinkronisasi laci lonceng real-time
        $recentAll = $user->notifications()
            ->where('data->event', '!=', 'submitted')
            ->latest()
            ->take(50)
            ->get();

        $notificationsList = [];
        $drawerNotifications = [];

        foreach ($recentAll as $notif) {
            $data = $notif->data ?? [];
            $event = $data['event'] ?? 'status';
            $title = $data['title'] ?? 'Pemberitahuan';
            $title = preg_replace('/^(?:Pemberitahuan\s+)?Kendala\s+(?:Perangkat:\s*)?/ui', 'Kendala: ', $title);
            $title = preg_replace('/^Kabar\s+Baik:\s*Perangkat\s*(.*?)\s*Selesai\s*Diperbaiki$/ui', 'Kabar Baik: $1 Pulih', $title);
            $title = preg_replace('/^Kabar\s+Baik:\s*(.*?)\s*Normal\s*Kembali$/ui', 'Kabar Baik: $1 Pulih', $title);
            $ticketCode = $data['ticket_code'] ?? null;
            $submissionTitle = $data['submission_title'] ?? null;
            $message = ! empty($data['notes'])
                ? ('Catatan Admin: "'.strip_tags($data['notes']).'"')
                : ($data['message'] ?? 'Status pengajuan Anda telah diperbarui.');

            $isSuccess = in_array($event, ['disetujui', 'selesai', 'videotron_recovered']) || str_contains($title, 'Disetujui') || str_contains($title, 'Selesai') || str_contains($title, 'Selesai Diperbaiki');
            $isDanger = in_array($event, ['ditolak', 'videotron_disruption']) || str_contains($title, 'Ditolak') || str_contains($title, 'Batal') || str_contains($title, 'Kendala') || str_contains($title, 'Gangguan');

            $type = match (true) {
                $isSuccess => 'success',
                $isDanger => 'error',
                default => 'info',
            };

            $typeClass = match (true) {
                $isDanger => 'is-danger',
                $isSuccess => 'is-success',
                default => 'is-info',
            };

            $isUnread = is_null($notif->read_at);

            $item = [
                'id' => (string) $notif->id,
                'title' => $title,
                'message' => $message,
                'ticket_code' => $ticketCode,
                'submission_title' => $submissionTitle,
                'event' => $event,
                'type' => $type,
                'type_class' => $typeClass,
                'is_unread' => $isUnread,
                'url' => route('notifikasi.read', $notif->id),
                'delete_url' => route('notifikasi.destroy', $notif->id),
                'batch_id' => $data['batch_id'] ?? null,
                'videotron_name' => $data['videotron_name'] ?? null,
                'age_seconds' => $notif->created_at ? (int) max(0, $notif->created_at->diffInSeconds(now(), true)) : null,
                'created_at' => $notif->created_at?->toISOString(),
                'time_ago' => $notif->created_at ? $notif->created_at->locale('id')->diffForHumans() : 'Baru saja',
            ];

            $drawerNotifications[] = $item;

            if ($isUnread && count($notificationsList) < 10) {
                $notificationsList[] = $item;
            }
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
            'notifications' => $notificationsList,
            'drawer_notifications' => $drawerNotifications,
            'latest' => $notificationsList[0] ?? null,
            'notifications_url' => route('pengajuan.index', ['notif_drawer' => 1]),
            'server_time' => now()->toISOString(),
        ]);
    }
}
