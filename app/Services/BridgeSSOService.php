<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BridgeSSOService
{
    protected bool $enabled;

    protected string $baseUrl;

    protected int $timeout;

    protected bool $simulate;

    public function __construct()
    {
        $this->enabled = (bool) config('services.bridge_sso.enabled', true);
        $this->baseUrl = rtrim((string) config('services.bridge_sso.base_url', 'https://bridge.uinssc.ac.id'), '/');
        $this->timeout = (int) config('services.bridge_sso.timeout', 8);
        $this->simulate = (bool) config('services.bridge_sso.simulate', false);
    }

    /**
     * Cek apakah integrasi Bridge SSO sedang diaktifkan.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Cek apakah integrasi berjalan dalam mode simulasi/mock lokal.
     */
    public function isSimulated(): bool
    {
        return $this->simulate;
    }

    /**
     * Dapatkan Base URL yang sedang dikonfigurasi.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Melakukan autentikasi kredensial pengguna ke endpoint /api/v2/portal/login.
     *
     * @return array{success: bool, status: string, message: string, token: ?string, raw?: array}
     */
    public function login(string $username, string $password): array
    {
        if (! $this->enabled) {
            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'Integrasi SSO kampus dinonaktifkan pada konfigurasi sistem.',
                'token' => null,
            ];
        }

        // Mode Simulasi untuk pengujian lokal saat server kampus belum bisa dijangkau
        if ($this->simulate) {
            return $this->simulateLogin($username, $password);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->asForm()
                ->post("{$this->baseUrl}/api/v2/portal/login", [
                    'username' => $username,
                    'password' => $password,
                ]);

            if ($response->successful()) {
                $payload = $response->json();
                $token = $payload['data']['token'] ?? $payload['token'] ?? null;

                if (! empty($token)) {
                    return [
                        'success' => true,
                        'status' => 'authenticated',
                        'message' => 'Autentikasi akun kampus berhasil.',
                        'token' => (string) $token,
                        'raw' => is_array($payload) ? $payload : [],
                    ];
                }

                return [
                    'success' => false,
                    'status' => 'token_missing',
                    'message' => 'Respon server portal tidak memuat token sesi yang valid.',
                    'token' => null,
                ];
            }

            if (in_array($response->status(), [401, 422], true)) {
                $errMessage = $response->json('message') ?? 'NIM/NIP atau password portal salah.';

                return [
                    'success' => false,
                    'status' => 'invalid_credentials',
                    'message' => (string) $errMessage,
                    'token' => null,
                ];
            }

            Log::warning('Bridge SSO Login returned non-success HTTP status', [
                'status' => $response->status(),
                'username' => $username,
            ]);

            return [
                'success' => false,
                'status' => 'server_error',
                'message' => 'Server portal kampus mengembalikan respon status: '.$response->status(),
                'token' => null,
            ];
        } catch (ConnectionException $e) {
            Log::warning('Bridge SSO Connection Timeout/Refused', [
                'username' => $username,
                'url' => $this->baseUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'connection_error',
                'message' => 'Tidak dapat terhubung ke server portal akademik kampus (Koneksi Terputus / Waktu Habis).',
                'token' => null,
            ];
        } catch (Throwable $e) {
            Log::error('Bridge SSO Login Exception', [
                'username' => $username,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'exception',
                'message' => 'Terjadi kendala teknis saat menghubungi server portal kampus: '.$e->getMessage(),
                'token' => null,
            ];
        }
    }

    /**
     * Mengambil biodata/profil pengguna dari endpoint /api/v2/profil dengan Bearer token.
     *
     * @return array{success: bool, data: array, message: string}
     */
    public function getProfile(string $token, ?string $username = null): array
    {
        if ($this->simulate) {
            return [
                'success' => true,
                'data' => $this->simulateProfile($username ?? '210101001'),
                'message' => 'Data profil simulasi berhasil dimuat.',
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->withToken($token)
                ->get("{$this->baseUrl}/api/v2/profil");

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();

                return [
                    'success' => true,
                    'data' => is_array($data) ? $data : [],
                    'message' => 'Data profil berhasil diambil dari server kampus.',
                ];
            }

            Log::warning('Bridge SSO getProfile failed', [
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Gagal mengambil data profil dari server portal kampus.',
            ];
        } catch (Throwable $e) {
            Log::error('Bridge SSO getProfile Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Terjadi gangguan saat mengambil data profil dari server kampus.',
            ];
        }
    }

    /**
     * Memetakan struktur profil dari Bridge API eCampuz v2 ke kolom tabel users E-VITRON.
     *
     * @param  array<string, mixed>  $profilePayload
     * @return array{name: string, identifier: string, email: string, role: string, unit_or_prodi: string, phone: ?string}
     */
    public function parseProfileData(array $profilePayload, string $username): array
    {
        // 1. Ekstraksi Nama
        $name = $profilePayload['nama']
            ?? $profilePayload['nama_lengkap']
            ?? $profilePayload['name']
            ?? $profilePayload['user']['nama']
            ?? ('Mahasiswa '.$username);

        // 2. Ekstraksi Identifier (NIM / NIP)
        $identifier = $profilePayload['nim']
            ?? $profilePayload['nip']
            ?? $profilePayload['username']
            ?? $username;

        // 3. Deteksi Role (Dosen vs Mahasiswa)
        $isDosen = ! empty($profilePayload['nip'])
            || str_contains(strtolower((string) ($profilePayload['status_aktivitas'] ?? '')), 'dosen')
            || str_contains(strtolower((string) ($profilePayload['jenis_pengguna'] ?? '')), 'dosen');
        $role = $isDosen ? 'dosen' : 'mahasiswa';

        // 4. Ekstraksi Email
        $fallbackDomain = '@uinssc.ac.id';
        $email = $profilePayload['email']
            ?? $profilePayload['email_kampus']
            ?? $profilePayload['user']['email']
            ?? (strtolower(preg_replace('/[^a-zA-Z0-9_\-\.]/', '', (string) $identifier)).$fallbackDomain);

        // 5. Ekstraksi Prodi / Unit Kerja (Mendukung format eCampuz v2 teks maupun nested array)
        $rawProdi = $profilePayload['program_studi_nama']
            ?? $profilePayload['prodi']
            ?? $profilePayload['program_studi']
            ?? $profilePayload['nama_prodi']
            ?? $profilePayload['prodi_nama']
            ?? $profilePayload['jurusan']
            ?? $profilePayload['nama_jurusan']
            ?? ($profilePayload['mahasiswa']['program_studi_nama'] ?? null)
            ?? ($profilePayload['mahasiswa']['prodi'] ?? null)
            ?? ($profilePayload['mahasiswa']['program_studi'] ?? null)
            ?? ($profilePayload['mahasiswa']['nama_prodi'] ?? null)
            ?? ($profilePayload['mahasiswa']['jurusan'] ?? null)
            ?? ($profilePayload['user']['program_studi_nama'] ?? null)
            ?? ($profilePayload['user']['prodi'] ?? null)
            ?? $profilePayload['fakultas_nama']
            ?? $profilePayload['fakultas']
            ?? $profilePayload['nama_fakultas']
            ?? $profilePayload['unit']
            ?? $profilePayload['unit_kerja']
            ?? null;

        if (is_array($rawProdi)) {
            $rawProdi = $rawProdi['program_studi_nama']
                ?? $rawProdi['nama']
                ?? $rawProdi['nama_prodi']
                ?? $rawProdi['name']
                ?? $rawProdi['prodi']
                ?? $rawProdi['nama_resmi']
                ?? null;
        }

        if (! empty($rawProdi)) {
            $unitOrProdi = ucwords(strtolower(trim((string) $rawProdi)));
        } else {
            $unitOrProdi = 'Sivitas Akademika UIN SSC';
        }

        // 6. Ekstraksi Nomor Telepon / WhatsApp
        $phone = $profilePayload['no_hp']
            ?? $profilePayload['phone']
            ?? $profilePayload['telepon']
            ?? $profilePayload['nomor_hp']
            ?? null;

        // 7. Ekstraksi Foto / Avatar
        $fotoRaw = $profilePayload['foto']
            ?? $profilePayload['avatar']
            ?? $profilePayload['foto_url']
            ?? $profilePayload['avatar_url']
            ?? null;

        $avatarUrl = null;
        if (! empty($fotoRaw)) {
            if (filter_var($fotoRaw, FILTER_VALIDATE_URL)) {
                $avatarUrl = (string) $fotoRaw;
            } elseif (file_exists(public_path('storage/avatars/'.$fotoRaw))) {
                $avatarUrl = '/storage/avatars/'.$fotoRaw;
            } elseif (file_exists(public_path('storage/avatars/'.$identifier.'.png'))) {
                $avatarUrl = '/storage/avatars/'.$identifier.'.png';
            } elseif (file_exists(public_path('storage/avatars/'.$identifier.'.jpg'))) {
                $avatarUrl = '/storage/avatars/'.$identifier.'.jpg';
            } else {
                $avatarUrl = '/storage/avatars/'.$fotoRaw;
            }
        } elseif (file_exists(public_path('storage/avatars/'.$identifier.'.png'))) {
            $avatarUrl = '/storage/avatars/'.$identifier.'.png';
        }

        return [
            'name' => trim((string) $name),
            'identifier' => trim((string) $identifier),
            'email' => strtolower(trim((string) $email)),
            'role' => $role,
            'unit_or_prodi' => trim((string) $unitOrProdi),
            'phone' => $phone ? trim((string) $phone) : null,
            'avatar_url' => $avatarUrl,
        ];
    }

    /**
     * Memanggil logout pada server Bridge API untuk mencabut Bearer token.
     */
    public function logout(?string $token): bool
    {
        if (empty($token) || $this->simulate) {
            return true;
        }

        try {
            Http::timeout(3)
                ->acceptJson()
                ->withToken($token)
                ->post("{$this->baseUrl}/api/v2/logout");

            return true;
        } catch (Throwable) {
            return true; // Jangan blokir proses logout lokal jika API kampus gagal
        }
    }

    /**
     * Menghasilkan data simulasi untuk mode offline/testing lokal.
     *
     * @return array{success: bool, status: string, message: string, token: string, raw: array}
     */
    protected function simulateLogin(string $username, string $password): array
    {
        // Izinkan login simulasi jika password bukan 'wrong'
        if (in_array(strtolower($password), ['salah', 'wrong', 'invalid'], true)) {
            return [
                'success' => false,
                'status' => 'invalid_credentials',
                'message' => 'Password portal akademik salah (Simulasi).',
                'token' => null,
            ];
        }

        return [
            'success' => true,
            'status' => 'authenticated',
            'message' => 'Login akun kampus berhasil (Mode Simulasi Bridge SSO).',
            'token' => 'simulated_bearer_token_'.hash('sha256', $username.time()),
            'raw' => [
                'status' => true,
                'message' => 'Simulated login success',
                'data' => [
                    'token' => 'simulated_token',
                ],
            ],
        ];
    }

    /**
     * Struktur profil realistis untuk mode simulasi.
     *
     * @return array<string, mixed>
     */
    protected function simulateProfile(string $username): array
    {
        $isNip = strlen($username) >= 12;

        return [
            'nama' => $isNip ? 'Dosen '.$username.', M.Kom.' : 'Mahasiswa '.$username,
            'nim' => $isNip ? null : $username,
            'nip' => $isNip ? $username : null,
            'email' => strtolower($username).'@uinssc.ac.id',
            'prodi' => 'Teknologi Informasi (S1)',
            'fakultas' => 'Fakultas Sains dan Teknologi Siber',
            'no_hp' => '0812'.substr(hash('crc32', $username), 0, 8),
        ];
    }
}
