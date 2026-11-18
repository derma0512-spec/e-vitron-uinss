    <!-- FOOTER HALAMAN (100% Identik dengan Login) -->
    <footer class="relative z-10 w-full py-3 px-6 select-none">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1.5 text-xs text-[#647B71]">
            <p class="font-medium text-[11.5px] text-center sm:text-left">
                E-VITRON &bull; UIN Siber Syekh Nurjati Cirebon
            </p>
            
            <div class="flex items-center gap-1.5 text-[11.5px]">
                <span class="text-[#647B71] font-medium">Butuh bantuan?</span>
                <button 
                    type="button" 
                    @click="showHelpModal = true"
                    class="font-bold text-[#0F3D2E] hover:text-[#D9A441] transition-colors cursor-pointer underline underline-offset-3 decoration-[#0F3D2E]/40 hover:decoration-[#D9A441]">
                    Bantuan Layanan
                </button>
            </div>
        </div>
    </footer>

    <!-- =========================================================
         MODAL BANTUAN & KONTAK LAYANAN E-VITRON (100% IDENTIK)
         ========================================================= -->
    <div 
        x-show="showHelpModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
        role="dialog" 
        aria-modal="true">
        
        <!-- Backdrop -->
        <div 
            x-show="showHelpModal"
            class="fixed inset-0 bg-black/60 cursor-pointer"
            style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;"></div>

        <!-- Modal Panel -->
        <div 
            x-show="showHelpModal"
            class="relative w-full max-w-lg bg-white rounded-[22px] sm:rounded-[24px] p-5 sm:p-7 shadow-2xl border border-[#DCE7DF] z-10 overflow-hidden">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-[#DCE7DF]">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#E8F3EC] text-[#0F3D2E]">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-[#0F3D2E] leading-tight">
                            Pusat Bantuan Layanan E-VITRON
                        </h3>
                        <p class="text-xs text-[#647B71] font-medium mt-0.5">
                            Layanan Pengelola E-VITRON • UIN Siber Syekh Nurjati Cirebon
                        </p>
                    </div>
                </div>
                <button 
                    type="button" 
                    @click="showHelpModal = false"
                    class="text-[#647B71] hover:text-[#0F3D2E] p-1.5 rounded-lg hover:bg-[#F5F8F5] transition-colors focus:outline-none cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="py-3.5 space-y-3.5">
                
                <!-- Petunjuk Format Akun -->
                <div class="bg-[#F5F8F5] p-3.5 rounded-[14px] border border-[#DCE7DF]/80 space-y-2 text-xs">
                    <div class="font-bold text-[#0F3D2E] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#D9A441]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Panduan Akun Masuk E-VITRON:</span>
                    </div>
                    <ul class="space-y-1.5 text-[#1A332C] pl-5 list-disc font-medium leading-relaxed">
                        <li><strong class="text-[#0F3D2E]">Mahasiswa &amp; Dosen:</strong> Masuk langsung menggunakan <strong>NIM / NIP</strong> dan password Portal Akademik (tidak perlu mendaftar akun baru).</li>
                        <li><strong class="text-[#0F3D2E]">Tendik &amp; Mitra Luar:</strong> Mendaftar terlebih dahulu melalui <a href="{{ route('register') }}" @click="showHelpModal = false" class="font-bold text-[#0F3D2E] hover:text-[#D9A441] underline underline-offset-2 decoration-[#0F3D2E]/40 hover:decoration-[#D9A441] transition-colors">form registrasi pemohon</a>, lalu masuk menggunakan <strong>Alamat Email</strong> setelah akun disetujui Admin.</li>
                    </ul>
                </div>

                <!-- Kontak Bantuan Langsung -->
                <div class="space-y-2.5">
                    <span class="block text-xs font-bold text-[#0F3D2E] tracking-tight">Saluran Kontak Bantuan Resmi:</span>

                    <!-- WhatsApp Option -->
                    <a href="https://wa.me/628557590909?text=Halo%20Admin%20SIPETRON%20UIN%20SSC,%20saya%20membutuhkan%20bantuan%20terkait%20layanan%20SIPETRON." 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="flex items-center justify-between p-3 rounded-[14px] border border-[#DCE7DF] hover:border-emerald-500 hover:bg-emerald-50/40 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.01 2C6.5 2 2.02 6.48 2.02 12c0 1.84.5 3.56 1.37 5.05L2 22l5.09-1.34c1.44.79 3.09 1.23 4.92 1.23 5.51 0 9.99-4.48 9.99-10S17.52 2 12.01 2zm5.83 14.12c-.24.68-1.4 1.25-1.94 1.32-.51.07-1.15.1-3.34-.78-2.8-1.13-4.59-3.99-4.73-4.18-.14-.19-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.35.26-.28.56-.35.75-.35.19 0 .38 0 .54.01.17.01.4-.06.63.48.24.57.81 1.97.88 2.11.07.14.12.31.02.5-.1.19-.15.31-.29.47-.14.16-.3.35-.43.47-.14.14-.29.3-.12.59.16.29.73 1.21 1.57 1.96 1.08.97 1.99 1.27 2.28 1.41.28.14.45.12.61-.07.17-.19.72-.84.91-1.13.19-.28.38-.24.64-.14.26.09 1.66.78 1.94.92.28.14.47.21.54.33.07.12.07.69-.17 1.37z"/>
                                </svg>
                            </span>
                            <div class="text-left">
                                <h4 class="text-xs font-bold text-[#0F3D2E] group-hover:text-emerald-800">WhatsApp Helpdesk E-VITRON</h4>
                                <p class="text-[11px] text-[#647B71]">+62 855-7590-909 (Chat Langsung)</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            Hubungi
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </a>

                    <!-- Email Option Anti-Truncate di Mobile & Desktop -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-[14px] border border-[#DCE7DF] bg-white gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E8F3EC] text-[#0F3D2E]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div class="text-left min-w-0">
                                <h4 class="text-xs font-bold text-[#0F3D2E]">Email Resmi Layanan</h4>
                                <p class="text-xs text-[#0F3D2E] font-semibold font-mono tracking-tight select-all">info@uinssc.ac.id</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 pl-12 sm:pl-0">
                            <!-- Tombol Salin Email -->
                            <button 
                                type="button" 
                                @click="copyEmail()"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#0F3D2E] bg-[#E8F3EC] hover:bg-[#D4E8DC] transition-colors flex items-center gap-1 cursor-pointer"
                                title="Salin alamat email info@uinssc.ac.id">
                                <svg class="w-3.5 h-3.5 text-[#0F3D2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"></path>
                                </svg>
                                <span x-text="copiedEmail ? 'Tersalin! ✓' : 'Salin'">Salin</span>
                            </button>
                            <!-- Tombol Kirim Email Langsung -->
                            <a 
                                href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=info@uinssc.ac.id&amp;su=Bantuan%20Layanan%20SIPETRON%20UIN%20SSC" target="_blank" rel="noopener noreferrer"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0F3D2E] hover:bg-[#0A2A20] transition-colors inline-flex items-center gap-1"
                                title="Kirim email langsung melalui aplikasi email">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>Kirim</span>
                            </a>
                        </div>
                    </div>

                    <!-- Lokasi Offline -->
                    <div class="flex items-start gap-2.5 p-3 rounded-[14px] bg-[#F5F8F5] text-xs text-[#647B71]">
                        <svg class="w-4 h-4 text-[#0F3D2E] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-[11px] leading-relaxed">
                            <strong>Layanan Langsung:</strong> Gedung Rektorat Kampus UIN Siber Syekh Nurjati Cirebon (UINSSC). Jam pelayanan: Senin – Jumat (08.00 – 16.00 WIB).
                        </p>
                    </div>

                </div>

            </div>

            <!-- Footer Modal -->
            <div class="pt-3 border-t border-[#DCE7DF] flex justify-end">
                <button 
                    type="button" 
                    @click="showHelpModal = false"
                    class="px-5 py-2 rounded-xl bg-[#0F3D2E] text-white text-xs font-bold hover:bg-[#0A2A20] transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>
