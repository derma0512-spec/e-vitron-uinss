<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna yang sedang login diizinkan mengirim pengajuan.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Sanitasi input teks dari tag HTML/script berbahaya (Anti-XSS & Anti-Spam Injection) sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        if (! setting('service_active', true)) {
            return;
        }

        $this->merge([
            'title' => $this->sanitizePlainText($this->input('title')),
            'description' => $this->sanitizePlainText($this->input('description')),
            'pic_name' => $this->sanitizePlainText($this->input('pic_name')),
            'organization' => $this->filled('organization')
                ? $this->sanitizePlainText($this->input('organization'))
                : null,
        ]);
    }

    /**
     * Aturan validasi pengiriman pengajuan baru.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (! setting('service_active', true)) {
            return [];
        }

        $maxVideoDuration = (int) setting('max_video_duration', 180);
        if ($maxVideoDuration < 5 || $maxVideoDuration > 180) {
            $maxVideoDuration = 180;
        }

        return [
            'videotrons' => 'required|array|min:1',
            'videotrons.*' => 'exists:videotrons,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:poster,video',
            'description' => 'required|string',
            'pic_name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'whatsapp' => ['required', 'string', 'max:30', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'gdrive_link' => [
                'required',
                'url',
                'regex:/^https?:\/\/(drive|docs)\.google\.com\/[^\s]+$/i',
            ],
            'video_duration_seconds' => "nullable|required_if:content_type,video|integer|min:5|max:{$maxVideoDuration}",
            'surat_peminjaman' => 'required|file|mimes:pdf|extensions:pdf|max:2048',
        ];
    }

    /**
     * Pesan kesalahan validasi berbahasa Indonesia yang ramah pengguna.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'videotrons.required' => 'Silakan pilih minimal 1 perangkat videotron yang tersedia.',
            'videotrons.min' => 'Silakan pilih minimal 1 perangkat videotron.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh berada di masa lalu.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'title.required' => 'Judul konten kegiatan wajib diisi.',
            'description.required' => 'Deskripsi konten wajib diisi.',
            'pic_name.required' => 'Nama penanggung jawab (PIC) wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
            'gdrive_link.required' => 'Link Google Drive bahan tayang wajib diisi.',
            'gdrive_link.url' => 'Format link Google Drive harus berupa URL yang valid.',
            'gdrive_link.regex' => 'Hanya menerima tautan berkas/folder resmi dari Google Drive (misal: https://drive.google.com/...).',
            'video_duration_seconds.required_if' => 'Durasi video wajib diisi untuk jenis konten Video.',
            'video_duration_seconds.min' => 'Durasi video minimal 5 detik.',
            'video_duration_seconds.max' => 'Durasi video maksimal 3 menit (180 detik).',
            'surat_peminjaman.required' => 'Surat permohonan resmi (PDF) wajib dilampirkan.',
            'surat_peminjaman.mimes' => 'Berkas surat permohonan harus berformat PDF.',
            'surat_peminjaman.extensions' => 'Berkas surat permohonan harus berformat PDF.',
            'surat_peminjaman.max' => 'Ukuran berkas surat permohonan maksimal 2 MB.',
        ];
    }

    /**
     * Membersihkan input teks dari tag HTML/script berbahaya (Anti-XSS & Anti-Spam Injection).
     */
    public static function sanitizePlainText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $cleaned = preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $value) ?? $value;
        $cleaned = strip_tags($cleaned);
        $cleaned = preg_replace('/javascript\s*:/i', '', $cleaned) ?? $cleaned;
        $cleaned = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $cleaned) ?? $cleaned);

        return $cleaned;
    }
}
