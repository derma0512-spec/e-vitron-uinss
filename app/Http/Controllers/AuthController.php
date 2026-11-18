<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BridgeSSOService;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman masuk (login) portal pengajuan & admin.
     */
    public function showLogin(): Response|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }

            return redirect()->route('home');
        }

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Menampilkan halaman pendaftaran akun lokal bagi Tendik & Mitra Luar.
     */
    public function showRegister(): Response|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }

            return redirect()->route('home');
        }

        return response()
            ->view('auth.register')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Memproses pendaftaran akun baru bagi Tendik & Mitra Luar.
     */
    public function register(Request $request): RedirectResponse
    {
        // Rate limiting atau proteksi honeypot
        if (! empty($request->input('website'))) {
            return back()->withInput();
        }

        // Sanitasi input teks pendaftaran dari tag HTML/script berbahaya (Anti-XSS)
        $cleanName = is_string($request->input('name'))
            ? trim(strip_tags(preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $request->input('name')) ?? ''))
            : $request->input('name');
        $cleanUnit = is_string($request->input('unit_or_prodi'))
            ? trim(strip_tags(preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $request->input('unit_or_prodi')) ?? ''))
            : $request->input('unit_or_prodi');

        $request->merge([
            'name' => $cleanName,
            'unit_or_prodi' => $cleanUnit,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role_type' => 'nullable|in:tendik,mitra',
            'unit_or_prodi' => 'required|string|max:150',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => ['required', 'string', 'min:9', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'unit_or_prodi.required' => 'Nama instansi atau unit kerja wajib diisi.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 9 digit.',
            'phone.regex' => 'Format nomor WhatsApp hanya boleh berisi angka.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $role = $validated['role_type'] ?? User::detectApplicantRole(
            (string) $validated['unit_or_prodi'],
            (string) $validated['email']
        );

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'identifier' => null,
            'phone' => trim($validated['phone']),
            'unit_or_prodi' => trim($validated['unit_or_prodi']),
            'auth_type' => 'local',
            'account_status' => 'pending',
        ]);

        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $notif = FilamentNotification::make()
                    ->title('Pendaftaran Akun Baru')
                    ->icon('heroicon-o-user-plus')
                    ->iconColor('warning')
                    ->body("Akun {$user->name} ({$user->unit_or_prodi}) menunggu persetujuan.")
                    ->actions([
                        NotificationAction::make('view')
                            ->label('Verifikasi Akun')
                            ->button()
                            ->size('xs')
                            ->url(url('/admin/users'))
                            ->markAsRead(),
                    ]);

                $admin->notifyNow($notif->toDatabase());
            }
        } catch (Throwable) {
            // Abaikan error pengiriman notifikasi jika rute admin belum ter-cache
        }

        return redirect()->route('login')->with(
            'success',
            'Pendaftaran berhasil dikirim! Akun Anda sedang menunggu persetujuan (approval) dari Admin PUSTIKOM. Notifikasi akan dikirimkan ke email '.$user->email.' setelah akun disetujui.'
        );
    }

    /**
     * Memproses autentikasi pengguna (via Email untuk Admin/Tendik/Mitra atau NIM/NIP via Bridge SSO).
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $input = trim($request->input('identifier'));
        $password = $request->input('password');

        // 1. Skenario EMAIL: Bisa ADMIN atau PENGAJU LOKAL (Tendik / Mitra Luar)
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $userByEmail = User::where('email', $input)->first();

            if (! $userByEmail || ! Hash::check($password, $userByEmail->password)) {
                return back()
                    ->withInput($request->only('identifier'))
                    ->with('error', 'Username atau password salah.');
            }

            if ($userByEmail->role !== 'admin') {
                if (($userByEmail->account_status ?? 'approved') === 'pending') {
                    return back()
                        ->withInput($request->only('identifier'))
                        ->with('error', 'Akun Anda masih menunggu persetujuan (approval) dari Admin PUSTIKOM. Silakan periksa email Anda secara berkala.');
                }

                if (($userByEmail->account_status ?? 'approved') === 'rejected') {
                    return back()
                        ->withInput($request->only('identifier'))
                        ->with('error', 'Mohon maaf, pendaftaran akun Anda belum dapat disetujui oleh Admin PUSTIKOM.');
                }

                if (($userByEmail->account_status ?? 'approved') !== 'approved') {
                    return back()
                        ->withInput($request->only('identifier'))
                        ->with('error', 'Akun Anda sedang dinonaktifkan oleh Admin PUSTIKOM. Silakan hubungi tim layanan untuk informasi lebih lanjut.');
                }
            }

            Auth::login($userByEmail, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($userByEmail->role === 'admin') {
                return redirect('/admin');
            }

            return redirect()->route('home')->with('success', 'Selamat datang, '.$userByEmail->name.'!');
        }

        // 2. Skenario PENGAJU: Wajib menggunakan nomor identitas (angka)
        if (ctype_digit($input)) {
            $bridgeSSO = app(BridgeSSOService::class);

            if ($bridgeSSO->isEnabled()) {
                $ssoResult = $bridgeSSO->login($input, $password);

                if ($ssoResult['success'] && ! empty($ssoResult['token'])) {
                    $token = $ssoResult['token'];
                    $profileResult = $bridgeSSO->getProfile($token, $input);
                    $profileData = $bridgeSSO->parseProfileData($profileResult['data'] ?? [], $input);

                    $user = User::where('identifier', $input)->first();

                    if ($user && $user->role === 'admin') {
                        return back()
                            ->withInput($request->only('identifier'))
                            ->with('error', 'Username atau password salah.');
                    }

                    if (! $user) {
                        $email = $profileData['email'];
                        if (User::where('email', $email)->exists()) {
                            $email = $input.'@syekhnurjati.ac.id';
                        }

                        $user = User::create([
                            'name' => $profileData['name'],
                            'identifier' => $input,
                            'email' => $email,
                            'role' => $profileData['role'],
                            'unit_or_prodi' => $profileData['unit_or_prodi'],
                            'avatar_url' => $profileData['avatar_url'] ?? null,
                            'phone' => $profileData['phone'],
                            'auth_type' => 'campus_sso',
                            'password' => Hash::make($password),
                        ]);
                    } else {
                        $user->name = $profileData['name'] ?: $user->name;
                        $user->unit_or_prodi = $profileData['unit_or_prodi'] ?: $user->unit_or_prodi;
                        if (! empty($profileData['avatar_url'])) {
                            $user->avatar_url = $profileData['avatar_url'];
                        }
                        if (! empty($profileData['phone']) && empty($user->phone)) {
                            $user->phone = $profileData['phone'];
                        }
                        $user->auth_type = 'campus_sso';
                        $user->password = Hash::make($password);
                        $user->save();
                    }

                    session(['bridge_sso_token' => $token]);

                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();

                    return redirect()->route('home')->with('success', 'Selamat datang, '.$user->name.'!');
                }

                // Fallback ke akun lokal jika SSO mengembalikan kredensial salah atau koneksi error
                $localUser = User::where('identifier', $input)
                    ->whereIn('role', ['mahasiswa', 'dosen'])
                    ->first();

                if ($localUser && Hash::check($password, $localUser->password)) {
                    Auth::login($localUser, $request->boolean('remember'));
                    $request->session()->regenerate();

                    return redirect()->route('home')->with('success', 'Selamat datang, '.$localUser->name.'!');
                }

                $errorMessage = ($ssoResult['status'] === 'invalid_credentials')
                    ? ($ssoResult['message'] ?? 'NIM/NIP atau password portal salah.')
                    : ($ssoResult['message'] ?? 'Username atau password salah.');

                return back()
                    ->withInput($request->only('identifier'))
                    ->with('error', $errorMessage);
            }

            // Fallback autentikasi lokal jika SSO dinonaktifkan
            $pengaju = User::where('identifier', $input)
                ->whereIn('role', ['mahasiswa', 'dosen'])
                ->first();

            if (! $pengaju || ! Hash::check($password, $pengaju->password)) {
                return back()
                    ->withInput($request->only('identifier'))
                    ->with('error', 'Username atau password salah.');
            }

            Auth::login($pengaju, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'Selamat datang, '.$pengaju->name.'!');
        }

        // 3. Skenario SALAH FORMAT: Bukan angka dan bukan email valid
        return back()
            ->withInput($request->only('identifier'))
            ->with('error', 'Username atau password salah.');
    }

    /**
     * Memproses keluar (logout) pengguna, mencabut token SSO jika ada, dan mengakhiri sesi.
     */
    public function logout(Request $request): RedirectResponse
    {
        $token = session('bridge_sso_token');
        if (! empty($token)) {
            app(BridgeSSOService::class)->logout($token);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Memperbarui kata sandi pengguna melalui modal Ubah Password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini yang Anda masukkan salah.',
                'errors' => [
                    'current_password' => ['Password saat ini yang Anda masukkan salah.'],
                ],
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui! Silakan gunakan kata sandi baru untuk login berikutnya.',
        ]);
    }
}
