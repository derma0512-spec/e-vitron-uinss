<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda portal pengaju beserta 3 pengajuan terbaru.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user?->isActiveAdmin()) {
            return redirect('/admin');
        }

        if ($user?->isAdmin()) {
            Auth::logout();

            return redirect()->route('login')
                ->with('error', 'Akun admin Anda sedang dinonaktifkan. Silakan hubungi Admin Utama (PUSTIKOM).');
        }

        $recentSubmissions = Submission::where('user_id', Auth::id())
            ->with('videotrons')
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('recentSubmissions'));
    }

    /**
     * Menampilkan halaman panduan dan ketentuan penggunaan layanan videotron.
     */
    public function panduan(): View
    {
        return view('panduan');
    }
}
