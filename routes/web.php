<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecapPrintController;
use App\Http\Controllers\SubmissionController;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureApplicantRole;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Support\Facades\Route;

// Autentikasi Sistem Terpadu (Admin & Pengaju)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin/login', function () {
    return redirect()->route('login');
});

// Beranda & Informasi (Wajib Login Terlebih Dahulu)
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/informasi', [HomeController::class, 'panduan'])->name('panduan');
    Route::get('/pengajuan/{ticket_code}/surat', [SubmissionController::class, 'downloadSurat'])->name('pengajuan.surat');
});

// Rute Pengajuan (Membutuhkan Login Pengaju)
Route::middleware(['auth', EnsureApplicantRole::class, PreventBackHistory::class])->prefix('pengajuan')->name('pengajuan.')->group(function () {
    Route::get('/', [SubmissionController::class, 'index'])->name('index');
    Route::get('/baru', [SubmissionController::class, 'create'])->name('create');
    Route::post('/simpan', [SubmissionController::class, 'store'])->name('store')->middleware('throttle:10,1');
    Route::get('/{ticket_code}/cetak', [SubmissionController::class, 'printProof'])->name('print')->middleware('throttle:20,1');
    Route::get('/{ticket_code}', [SubmissionController::class, 'show'])->name('show');
    Route::post('/{ticket_code}/batal', [SubmissionController::class, 'cancel'])->name('cancel');
    Route::post('/{ticket_code}/revisi', [SubmissionController::class, 'submitRevision'])->name('revision')->middleware('throttle:10,1');
});

// Rute Notifikasi Pengaju
Route::middleware(['auth', EnsureApplicantRole::class, PreventBackHistory::class])->prefix('notifikasi')->name('notifikasi.')->group(function () {
    Route::get('/cek-terbaru', [NotificationController::class, 'checkUnread'])->name('checkUnread')->middleware('throttle:60,1');
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{id}/buka', [NotificationController::class, 'readAndRedirect'])->name('read');
    Route::post('/{id}/tandai', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/tandai-semua', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::delete('/hapus-semua', [NotificationController::class, 'clearAll'])->name('clearAll');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

// Rute Panel Admin
Route::middleware(['auth', EnsureAdminRole::class, PreventBackHistory::class])->prefix('admin')->group(function () {
    // Rute Notifikasi Admin (AJAX / REST)
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clearAll'])->name('admin.notifications.clearAll');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.markAllAsRead');

    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('admin.change-password');
    Route::get('/history-and-recap/print', [RecapPrintController::class, 'print'])->name('admin.history-and-recap.print');
    Route::get('/history-and-recap/export-pdf', [RecapPrintController::class, 'exportPdf'])->name('admin.history-and-recap.export-pdf')->middleware('throttle:20,1');
    Route::get('/submissions/{id}/print-pdf', [RecapPrintController::class, 'printSinglePdf'])->name('admin.submissions.print-pdf')->middleware('throttle:20,1');
    Route::get('/schedule-preparation', function () {
        return redirect('/admin/submissions?statusFilter=disetujui');
    });
    Route::get('/submissions/{id}/edit', function ($id) {
        return redirect('/admin/submissions?selected='.(int) $id);
    });
    Route::get('/submissions/{id}', function ($id) {
        return redirect('/admin/submissions?selected='.(int) $id);
    });
});
