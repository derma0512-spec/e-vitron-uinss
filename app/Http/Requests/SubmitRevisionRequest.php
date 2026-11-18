<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitRevisionRequest extends FormRequest
{
    /**
     * Instance pengajuan yang sedang direvisi.
     */
    protected ?Submission $targetSubmission = null;

    /**
     * Ambil pengajuan milik pengguna berdasarkan parameter route ticket_code.
     */
    public function targetSubmission(): ?Submission
    {
        if ($this->targetSubmission === null && $this->user()) {
            $ticketCode = (string) $this->route('ticket_code');
            $this->targetSubmission = Submission::where('ticket_code', $ticketCode)
                ->where('user_id', $this->user()->id)
                ->first();
        }

        return $this->targetSubmission;
    }

    /**
     * Tentukan apakah pengguna yang sedang login diizinkan mengirim revisi.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Sanitasi input balasan revisi dari tag HTML berbahaya sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $submission = $this->targetSubmission();
        if (! $submission || $submission->status !== 'revisi') {
            return;
        }

        $this->merge([
            'user_reply' => StoreSubmissionRequest::sanitizePlainText($this->input('user_reply')),
        ]);
    }

    /**
     * Aturan validasi pengiriman perbaikan revisi berdasarkan jenis revisi (materi / surat / keduanya).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $submission = $this->targetSubmission();
        if (! $submission || $submission->status !== 'revisi') {
            return [];
        }

        $revType = $submission->effective_revision_type;

        $rules = [
            'user_reply' => 'required|string',
        ];

        if (in_array($revType, ['materi', 'keduanya'], true)) {
            $rules['gdrive_link'] = [
                'required',
                'url',
                'regex:/^https?:\/\/(drive|docs)\.google\.com\/[^\s]+$/i',
            ];
        }

        if (in_array($revType, ['surat', 'keduanya'], true)) {
            $rules['surat_peminjaman'] = [
                $revType === 'surat' ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'extensions:pdf',
                'max:2048',
            ];
        }

        return $rules;
    }

    /**
     * Pesan kesalahan validasi berbahasa Indonesia untuk form perbaikan revisi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_reply.required' => 'Penjelasan perbaikan wajib diisi.',
            'gdrive_link.required' => 'Link Google Drive bahan tayang revisi wajib diisi.',
            'gdrive_link.url' => 'Format link Google Drive harus berupa URL yang valid.',
            'gdrive_link.regex' => 'Hanya menerima tautan berkas/folder resmi dari Google Drive (misal: https://drive.google.com/...).',
            'surat_peminjaman.required' => 'Berkas surat permohonan resmi baru (PDF) wajib diunggah.',
            'surat_peminjaman.mimes' => 'Berkas surat permohonan harus berformat PDF.',
            'surat_peminjaman.extensions' => 'Berkas surat permohonan harus berformat PDF.',
            'surat_peminjaman.max' => 'Ukuran berkas surat permohonan maksimal 2 MB.',
        ];
    }
}
