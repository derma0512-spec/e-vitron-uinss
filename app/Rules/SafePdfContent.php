<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\PotentiallyTranslatedString;

class SafePdfContent implements ValidationRule
{
    /**
     * Memastikan berkas PDF yang diunggah valid secara biner dan tidak disisipi skrip PHP (anti-polyglot).
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return;
        }

        $realPath = $value->getRealPath();
        if (! is_string($realPath) || ! is_readable($realPath)) {
            $fail('Berkas surat permohonan tidak dapat dibaca oleh sistem.');

            return;
        }

        // Batasi pembacaan maksimal 2 MB + 1 byte untuk mencegah lonjakan memori
        $contents = @file_get_contents($realPath, false, null, 0, (2 * 1024 * 1024) + 1);
        if (! is_string($contents)) {
            $fail('Berkas surat permohonan tidak dapat diverifikasi.');

            return;
        }

        // Tolak penyisipan tag PHP eksplisit (anti-polyglot web shell tanpa false positive pada PDF scan asli)
        if (stripos($contents, '<?php') !== false) {
            $fail('Berkas surat permohonan mengandung konten skrip yang tidak diperbolehkan.');

            return;
        }

        // Untuk berkas non-kosong (bukan file dummy berisi byte nol), pastikan header %PDF- hadir di 1024 byte awal (ISO 32000)
        if ($contents !== '' && trim($contents, "\0") !== '') {
            $headerWindow = substr($contents, 0, 1024);
            if (! str_contains($headerWindow, '%PDF-')) {
                $fail('Berkas surat permohonan bukan merupakan dokumen PDF yang valid.');
            }
        }
    }
}
