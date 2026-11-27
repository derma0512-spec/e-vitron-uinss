<?php

$trustedProxies = trim((string) env('TRUSTED_PROXIES', '127.0.0.1,::1'));

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Reverse Proxies
    |--------------------------------------------------------------------------
    |
    | Daftar IP reverse proxy yang dipercaya untuk header X-Forwarded-*.
    | Gunakan "127.0.0.1,::1" untuk VPS dengan Nginx reverse proxy lokal,
    | atau "*" hanya bila aplikasi berada di belakang cloud proxy tepercaya.
    |
    */

    'proxies' => $trustedProxies === '' ? null : $trustedProxies,

];
