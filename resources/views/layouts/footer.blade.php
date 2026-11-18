{{-- ====================================================
     FOOTER RESMI SISTEM VIDEOTRON UIN SSC
     UIN Siber Syekh Nurjati Cirebon (Sederhana & Rapi)
==================================================== --}}
<footer class="relative bg-[#082E24] text-slate-200 border-t border-[#134D3C] overflow-hidden mt-auto">
    {{-- Islamic Ornament Watermark Halus di Sudut --}}
    <div class="ornament-pattern absolute -left-10 -bottom-10 w-52 h-52 opacity-10 pointer-events-none" aria-hidden="true"></div>
    <div class="ornament-pattern absolute -right-10 -top-10 w-52 h-52 opacity-10 pointer-events-none" aria-hidden="true"></div>

    {{-- Garis Aksen Emas Halus di Bagian Atas --}}
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#D9A93B]/60 to-transparent" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-[1400px] px-5 sm:px-10 lg:px-14 py-6 sm:py-8">
        {{-- Grid 3 Kolom yang Bersih & Seimbang --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-8 pb-5 sm:pb-6 border-b border-[#134D3C]/70">
            
            {{-- Kolom 1: Identitas & Deskripsi Singkat (5 kolom di desktop) --}}
            <div class="md:col-span-5 space-y-2.5 sm:space-y-3">
                <div class="flex items-center gap-3 select-none">
                    <img src="{{ asset('images/sipetron-icon.png') }}" alt="Logo E-VITRON" class="h-9 w-9 sm:h-10 sm:w-10 object-contain" />
                    <div class="flex flex-col justify-center leading-none">
                        <span class="font-montserrat text-lg sm:text-[21px] font-extrabold tracking-tight text-white leading-none">E-VITRON</span>
                        <span class="text-[9px] sm:text-[9.5px] font-semibold text-[#D9A93B] tracking-tight mt-1 leading-none">Layanan Pengajuan Penayangan Videotron</span>
                    </div>
                </div>

                <p class="text-[11.5px] sm:text-xs text-[#9BBBAE] leading-relaxed max-w-md">
                    Media resmi penayangan materi publikasi kegiatan, pengumuman informasi akademik, dan kelembagaan sivitas akademika UIN Siber Syekh Nurjati Cirebon.
                </p>
            </div>

            {{-- Kolom 2: Navigasi Pengguna (3 kolom di desktop) --}}
            <div class="md:col-span-3 space-y-2 sm:space-y-2.5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="h-2.5 w-1 rounded-full bg-[#D9A93B]"></span>
                    Menu Navigasi
                </h4>
                <ul class="space-y-1 sm:space-y-1.5 text-xs">
                    <li>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-[#B2CEC2] hover:text-[#D9A93B] transition-colors py-0.5">
                            <span class="text-[#D9A93B] text-[10px]">›</span>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengajuan.create') }}" class="inline-flex items-center gap-1.5 text-[#B2CEC2] hover:text-[#D9A93B] transition-colors py-0.5">
                            <span class="text-[#D9A93B] text-[10px]">›</span>
                            <span>Ajukan Penayangan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center gap-1.5 text-[#B2CEC2] hover:text-[#D9A93B] transition-colors py-0.5">
                            <span class="text-[#D9A93B] text-[10px]">›</span>
                            <span>Pengajuan Saya</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('panduan') }}" class="inline-flex items-center gap-1.5 text-[#B2CEC2] hover:text-[#D9A93B] transition-colors py-0.5">
                            <span class="text-[#D9A93B] text-[10px]">›</span>
                            <span>Panduan & Informasi</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Kolom 3: Kontak Kami (Terkoneksi ke Pengaturan Sistem) --}}
            <div class="md:col-span-4 space-y-2 sm:space-y-2.5">
                <h4 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="h-2.5 w-1 rounded-full bg-[#D9A93B]"></span>
                    Kontak Layanan
                </h4>
                <div class="space-y-1.5 sm:space-y-2 text-xs text-[#9BBBAE]">
                    <div class="leading-tight">
                        <span class="text-slate-200 font-semibold block text-[11px]">Lokasi Pelayanan</span>
                        <span class="text-[#B2CEC2] text-[11.5px] sm:text-xs">{{ setting('contact_office_location', 'Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon') }}</span>
                    </div>
                    <div class="leading-tight">
                        <span class="text-slate-200 font-semibold block text-[11px]">Helpdesk & Email</span>
                        <div class="flex flex-col gap-0.5 sm:gap-1 mt-0.5 text-[11.5px] sm:text-xs">
                            @php
                                $wa = (string) setting('helpdesk_whatsapp', setting('contact_whatsapp', '081234567890'));
                                $waDigits = preg_replace('/[^0-9]/', '', $wa);
                                if (str_starts_with($waDigits, '0')) {
                                    $waLink = '62' . substr($waDigits, 1);
                                } elseif (str_starts_with($waDigits, '62')) {
                                    $waLink = $waDigits;
                                } else {
                                    $waLink = '62' . $waDigits;
                                }
                            @endphp
                            <a href="https://wa.me/{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="text-emerald-300 hover:text-[#D9A93B] transition-colors inline-flex items-center gap-1.5">
                                <span>WhatsApp: {{ $wa }}</span>
                            </a>
                            <a href="mailto:{{ setting('contact_email', 'info@uinssc.ac.id') }}" class="text-[#B2CEC2] hover:text-[#D9A93B] transition-colors">
                                {{ setting('contact_email', 'info@uinssc.ac.id') }}
                            </a>
                        </div>
                    </div>
                    <div class="leading-tight">
                        <span class="text-slate-200 font-semibold block text-[11px]">Jam Pelayanan</span>
                        <span class="text-[#B2CEC2] text-[11.5px] sm:text-xs">{{ setting('contact_service_hours', 'Senin - Jumat: 08.00 - 16.00 WIB') }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bar Bawah / Bottom Bar yang Rapi & di Tengah --}}
        <div class="pt-4 sm:pt-5 text-center text-[11px] sm:text-xs text-[#7EA494]">
            <p>
                &copy; {{ date('Y') }} <span class="text-slate-200 font-medium">E-VITRON UIN Siber Syekh Nurjati Cirebon</span>. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>
