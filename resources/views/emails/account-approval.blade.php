<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectTitle ?? 'Notifikasi Status Akun E-VITRON' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F5F8F5; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; color: #1A332C; -webkit-font-smoothing: antialiased; line-height: 1.6;">

    <!-- Wrapper Table -->
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F5F8F5; padding: 30px 15px;">
        <tr>
            <td align="center">

                <!-- Main Container Card (Max 600px) -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #FFFFFF; border: 1px solid #DCE7DF; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(15, 61, 46, 0.05);">
                    
                    <!-- HEADER RESMI E-VITRON & UIN SSC -->
                    <tr>
                        <td style="background-color: #0F3D2E; padding: 26px 30px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="font-size: 20px; font-weight: 800; letter-spacing: 0.5px; color: #FFFFFF; margin-bottom: 2px;">
                                            E-VITRON
                                        </div>
                                        <div style="font-size: 13px; font-weight: 600; color: #D9A441; letter-spacing: 0.3px; text-transform: uppercase;">
                                            UIN Siber Syekh Nurjati Cirebon
                                        </div>
                                        <div style="font-size: 11px; color: #A3C9B8; margin-top: 4px;">
                                            Layanan Pengajuan Penayangan Materi Videotron
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- STATUS BADGE BANNER -->
                    <tr>
                        <td style="padding: 24px 30px 0 30px;">
                            @php
                                $badgeBg = match($status) {
                                    'approved' => '#E8F5E9',
                                    'rejected' => '#FFEBEE',
                                    default => '#FFF8E1',
                                };
                                $badgeColor = match($status) {
                                    'approved' => '#1B5E20',
                                    'rejected' => '#C62828',
                                    default => '#B78103',
                                };
                                $badgeBorder = match($status) {
                                    'approved' => '#C8E6C9',
                                    'rejected' => '#FFCDD2',
                                    default => '#FFE082',
                                };
                            @endphp

                            <div style="background-color: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }}; border-radius: 12px; padding: 14px 18px; text-align: center;">
                                <div style="display: inline-block; font-size: 11px; font-weight: 800; letter-spacing: 0.8px; text-transform: uppercase; color: {{ $badgeColor }}; margin-bottom: 4px;">
                                    STATUS PENDAFTARAN AKUN
                                </div>
                                <div style="font-size: 17px; font-weight: 700; color: {{ $badgeColor }};">
                                    {{ $title }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- BODY / CONTENT SURAT -->
                    <tr>
                        <td style="padding: 22px 30px 10px 30px;">
                            
                            <!-- Sapaan Resmi -->
                            <p style="margin: 0 0 14px 0; font-size: 14px; color: #1A332C;">
                                Yth. <strong>{{ $user->name }}</strong>,
                                @if(!empty($user->unit_or_prodi))
                                    <br><span style="color: #647B71; font-size: 13px;">{{ $user->unit_or_prodi }}</span>
                                @endif
                            </p>

                            <!-- Pesan Pengantar -->
                            <p style="margin: 0 0 18px 0; font-size: 13.5px; color: #405B50; line-height: 1.6;">
                                {{ $messageText }}
                            </p>

                            <!-- KOTAK CATATAN ADMIN (JIKA ADA) -->
                            @if(!empty($notes))
                                <div style="background-color: #FAFBF9; border-left: 4px solid {{ $badgeColor }}; border-top: 1px solid #E6EEE8; border-right: 1px solid #E6EEE8; border-bottom: 1px solid #E6EEE8; border-radius: 0 10px 10px 0; padding: 14px 16px; margin: 0 0 20px 0;">
                                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $badgeColor }}; margin-bottom: 5px;">
                                        Catatan dari Admin PUSTIKOM:
                                    </div>
                                    <div style="font-size: 13px; color: #1A332C; font-style: italic; line-height: 1.5;">
                                        "{!! nl2br(e($notes)) !!}"
                                    </div>
                                </div>
                            @endif

                            <!-- KARTU RINCIAN AKUN -->
                            <div style="background-color: #F8FAF8; border: 1px solid #E2ECE5; border-radius: 12px; padding: 16px 18px; margin-bottom: 24px;">
                                <div style="font-size: 12px; font-weight: 700; color: #0F3D2E; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; border-bottom: 1px dashed #DCE7DF; padding-bottom: 8px;">
                                    Informasi Akun Terdaftar
                                </div>

                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 12.5px;">
                                    <tr>
                                        <td width="36%" style="padding: 4px 0; color: #647B71;">Nama Lengkap</td>
                                        <td width="4%" style="padding: 4px 0; color: #647B71;">:</td>
                                        <td width="60%" style="padding: 4px 0; font-weight: 600; color: #1A332C;">
                                            {{ $user->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0; color: #647B71;">Email Login</td>
                                        <td style="padding: 4px 0; color: #647B71;">:</td>
                                        <td style="padding: 4px 0; font-weight: 700; color: #0F3D2E;">
                                            {{ $user->email }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0; color: #647B71;">Kategori</td>
                                        <td style="padding: 4px 0; color: #647B71;">:</td>
                                        <td style="padding: 4px 0; color: #1A332C; text-transform: capitalize;">
                                            {{ $user->role === 'tendik' ? 'Tendik / Staf Kampus' : 'Mitra / Instansi Luar' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0; color: #647B71;">Instansi / Unit</td>
                                        <td style="padding: 4px 0; color: #647B71;">:</td>
                                        <td style="padding: 4px 0; color: #1A332C;">
                                            {{ $user->unit_or_prodi ?? '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @if($status === 'approved')
                                <!-- TOMBOL CTA (MASUK KE E-VITRON) -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="center" style="padding-bottom: 12px;">
                                            <a href="{{ $loginUrl }}" 
                                               target="_blank" 
                                               style="display: inline-block; background-color: #0F3D2E; color: #FFFFFF; font-weight: 700; font-size: 13.5px; text-decoration: none; padding: 13px 28px; border-radius: 10px; box-shadow: 0 4px 12px rgba(15, 61, 46, 0.25);">
                                                Masuk ke Portal E-VITRON Sekarang &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #166534; text-align: center; line-height: 1.5;">
                                    Gunakan alamat email dan password yang telah Anda daftarkan untuk masuk ke sistem.
                                </p>
                            @endif

                        </td>
                    </tr>

                    <!-- FOOTER INFORMASI & KONTAK RESMI -->
                    <tr>
                        <td style="background-color: #FAFBF9; border-top: 1px solid #EAEFEA; padding: 22px 30px; text-align: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #0F3D2E; margin-bottom: 4px;">
                                PUSTIKOM &amp; Unit Layanan Terpadu UIN Siber Syekh Nurjati Cirebon
                            </div>
                            <div style="font-size: 11.5px; color: #647B71; line-height: 1.5; margin-bottom: 10px;">
                                Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131<br>
                                Surel Bantuan: <a href="mailto:info@uinssc.ac.id" style="color: #0F3D2E; font-weight: 600; text-decoration: underline;">info@uinssc.ac.id</a>
                            </div>
                            <div style="font-size: 10.5px; color: #94A3B8; border-top: 1px solid #E2E8F0; padding-top: 10px; margin-top: 8px;">
                                Email ini dikirimkan secara otomatis oleh sistem E-VITRON. Mohon tidak membalas langsung ke alamat email ini.
                            </div>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

