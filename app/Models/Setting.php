<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public const CACHE_KEY = 'sipetron_settings_cache';

    public const DEFAULT_TEMPLATE_DOC_URL = '/downloads/Template-Surat-Permohonan-Videotron-UINSSC.docx';

    public const LEGACY_SAMPLE_TEMPLATE_URL = 'https://docs.google.com/document/d/sample-template-surat-uin-ssc';

    /**
     * In-memory runtime cache selama 1 siklus HTTP request
     * agar pemanggilan setting() berulang kali tidak memicu query tabel cache berulang.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $runtimeCache = null;

    protected static function booted(): void
    {
        static::saved(function (): void {
            self::clearCache();
        });

        static::deleted(function (): void {
            self::clearCache();
        });
    }

    /**
     * Ambil semua setelan yang sudah di-cache dalam bentuk array asosiatif [key => value].
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        if (self::$runtimeCache !== null) {
            return self::$runtimeCache;
        }

        return self::$runtimeCache = Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = self::all();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Ambil nilai satu setelan berdasarkan key dengan nilai default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::allCached();
        $value = $all[$key] ?? $default;

        if ($key === 'template_doc_url' && (blank($value) || $value === self::LEGACY_SAMPLE_TEMPLATE_URL)) {
            return self::DEFAULT_TEMPLATE_DOC_URL;
        }

        return $value;
    }

    /**
     * Simpan atau perbarui nilai setelan dan segarkan cache.
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $group = null): self
    {
        $setting = self::firstOrNew(['key' => $key]);

        if ($type !== null) {
            $setting->type = $type;
        } elseif (! $setting->exists) {
            $setting->type = self::detectType($value);
        }

        if ($group !== null) {
            $setting->group = $group;
        }

        if (is_bool($value)) {
            $setting->value = $value ? '1' : '0';
        } else {
            $setting->value = (string) $value;
        }

        $setting->save();

        self::clearCache();

        return $setting;
    }

    /**
     * Bersihkan cache setelan.
     */
    public static function clearCache(): void
    {
        self::$runtimeCache = null;
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Deteksi tipe data secara otomatis.
     */
    protected static function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            default => 'string',
        };
    }

    /**
     * Cast nilai dari database sesuai tipe yang tersimpan.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            default => $value,
        };
    }
}
