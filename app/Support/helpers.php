<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Ambil atau set nilai pengaturan aplikasi.
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return Setting::allCached();
        }

        return Setting::get($key, $default);
    }
}
