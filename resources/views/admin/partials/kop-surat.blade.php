@php
    if (empty($logoBase64)) {
        $croppedPath = public_path('images/uin-ssc-logo-official.png');
        $fallbackPath = public_path('images/uin-ssc-logo-transparent.png');
        $targetLogoPath = is_file($croppedPath) ? $croppedPath : $fallbackPath;
        $logoBase64 = is_file($targetLogoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($targetLogoPath))
            : asset('images/uin-ssc-logo-official.png');
    }
@endphp

<style>
    .kop-shared-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .kop-shared-side-left {
        width: 68px;
        vertical-align: middle;
        text-align: left;
        padding: 0;
    }

    .kop-shared-side-left img {
        height: 82px;
        width: auto;
        object-fit: contain;
        display: block;
        margin: 0;
    }

    .kop-shared-center {
        text-align: center;
        vertical-align: middle;
        font-family: "Times New Roman", Times, serif;
        line-height: 1.25;
        padding: 0 2px;
    }

    .kop-shared-side-right {
        width: 58px;
        vertical-align: middle;
        padding: 0;
    }

    .kop-shared-line-1 {
        font-size: 11pt;
        font-weight: bold;
        letter-spacing: 0.3px;
        margin: 0 0 2px 0;
        color: #111827;
        text-transform: uppercase;
    }

    .kop-shared-line-2 {
        font-size: 12pt;
        font-weight: bold;
        color: #111827;
        margin: 0 0 2px 0;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .kop-shared-line-3 {
        font-size: 10pt;
        font-weight: bold;
        margin: 0 0 3px 0;
        color: #111827;
        text-transform: uppercase;
    }

    .kop-shared-address {
        font-size: 8.5pt;
        margin: 0 0 1.5px 0;
        color: #374151;
    }

    .kop-shared-web {
        font-size: 8pt;
        font-style: italic;
        margin: 0;
        color: #4B5563;
    }

    .kop-shared-divider-thick {
        border-top: 2.5px solid #111827;
        margin-top: 4px;
    }

    .kop-shared-divider-thin {
        border-top: 1px solid #111827;
        margin-top: 2px;
        margin-bottom: 14px;
    }
</style>

<table class="kop-shared-table">
    <tr>
        <td class="kop-shared-side-left">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="Logo UIN SSC">
            @endif
        </td>
        <td class="kop-shared-center">
            <p class="kop-shared-line-1">{{ setting('institution_line_1', 'KEMENTERIAN AGAMA REPUBLIK INDONESIA') }}</p>
            <p class="kop-shared-line-2">{{ setting('institution_line_2', 'UNIVERSITAS ISLAM NEGERI SIBER SYEKH NURJATI CIREBON') }}</p>
            <p class="kop-shared-line-3">{{ setting('institution_line_3', 'LAYANAN PENGAJUAN PENAYANGAN VIDEOTRON (E-VITRON)') }}</p>
            <p class="kop-shared-address">{{ setting('institution_address', 'Jalan Perjuangan By Pass Sunyaragi, Kota Cirebon, Jawa Barat 45132') }}</p>
            <p class="kop-shared-web">Laman: {{ setting('institution_web', 'https://uinssc.ac.id') }} &bull; Pos-el: {{ setting('institution_email', 'info@uinssc.ac.id') }}</p>
        </td>
        <td class="kop-shared-side-right"></td>
    </tr>
</table>
<div class="kop-shared-divider-thick"></div>
<div class="kop-shared-divider-thin"></div>
