        {{-- ==============================================================
             LANGKAH 5 — KONFIRMASI BERHASIL (Sesuai Mockup Referensi)
             ============================================================== --}}
        @if(session('submission_success'))
            @php $subData = session('submission_success'); @endphp
            <div data-step="5" 
                 x-show="currentStep === 5" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1.5"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="relative overflow-hidden bg-white/95 backdrop-blur-md rounded-[22px] border border-white/90 shadow-sm p-6 sm:p-10 text-center space-y-6">
                
                {{-- Watermark ornamen di sudut form --}}
                <div class="ornament-pattern" style="position:absolute;right:-25px;bottom:-25px;height:120px;width:120px;opacity:0.12;pointer-events:none;"></div>

                <!-- Ikon Centang Hijau Besar -->
                <div>
                    <div class="w-14 h-14 rounded-full bg-[#0E4D3C] text-[#D9A93B] flex items-center justify-center mx-auto mb-3 shadow-xs border border-[#D9A93B]/20">
                        <svg class="w-7 h-7 text-[#D9A93B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-montserrat font-extrabold text-[#0E4D3C] tracking-tight">
                        Pengajuan Berhasil Dikirim!
                    </h2>
                    <p class="text-xs text-[#4E655F] mt-1.5 max-w-md mx-auto leading-relaxed">
                        Terima kasih, pengajuan penayangan videotron Anda telah berhasil dikirim dan sedang menunggu pemeriksaan tim admin.
                    </p>
                </div>

                <!-- Box Kode Tiket Krem-Emas dengan Tombol Salin Interaktif (Tanpa alert jadul) -->
                <div x-data="{ copied: false }" 
                     class="inline-flex flex-col items-center bg-[#FEF9EE] border border-[#F3E2B8] rounded-xl py-2.5 px-6 sm:px-8 shadow-2xs relative">
                    <span class="text-[11px] font-semibold text-amber-800">Kode Tiket</span>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="font-mono text-lg font-extrabold text-amber-950 tracking-wide" id="ticket-text">{{ $subData['ticket_code'] }}</span>
                        <div class="relative flex items-center">
                            <button type="button" 
                                    onclick="window.copyTicketToClipboard('{{ $subData['ticket_code'] }}', this)"
                                    @click="if (window.copyTicketToClipboard) window.copyTicketToClipboard('{{ $subData['ticket_code'] }}', $el); copied = true; setTimeout(() => copied = false, 2000);" 
                                    class="p-1.5 rounded-lg text-amber-800 hover:text-amber-950 hover:bg-amber-100/80 transition-all cursor-pointer" 
                                    title="Salin Kode Tiket">
                                <template x-if="!copied">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </template>
                                <template x-if="copied">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                            </button>
                            <!-- Tooltip Tersalin -->
                            <span x-show="copied" 
                                  x-transition:enter="transition ease-out duration-150"
                                  x-transition:enter-start="opacity-0 -translate-y-1"
                                  x-transition:enter-end="opacity-100 translate-y-0"
                                  x-transition:leave="transition ease-in duration-100"
                                  x-transition:leave-start="opacity-100 translate-y-0"
                                  x-transition:leave-end="opacity-0 -translate-y-1"
                                  x-cloak
                                  class="absolute -top-7 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-slate-900 text-white text-[10px] font-semibold rounded shadow-md whitespace-nowrap pointer-events-none">
                                Tersalin! ✓
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 4 Kolom Info Ringkasan (Executive Style, Bebas Text Truncate Periode) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-left max-w-3xl mx-auto pt-1">
                    <div class="p-3 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Judul Konten</span>
                        <span class="text-xs sm:text-[13px] font-bold text-slate-800 truncate block mt-0.5" title="{{ $subData['title'] }}">{{ $subData['title'] }}</span>
                    </div>

                    <div class="p-3 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Videotron</span>
                        <span class="text-xs sm:text-[13px] font-bold text-slate-800 truncate block mt-0.5" title="{{ implode(', ', $subData['videotrons']) }}">{{ implode(', ', $subData['videotrons']) }}</span>
                    </div>

                    <div class="p-3 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Periode</span>
                        <span class="text-xs sm:text-[13px] font-bold text-slate-800 block mt-0.5 leading-snug whitespace-normal">{{ $subData['period'] }}</span>
                    </div>

                    <div class="p-3 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white shadow-2xs flex flex-col justify-between">
                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Status Awal</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/80 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>{{ $subData['status'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info Box Langkah Selanjutnya (Next Steps Guide) -->
                <div class="max-w-3xl mx-auto p-3.5 rounded-xl border border-emerald-100 bg-[#EAF5F0]/70 text-left flex items-start gap-3 shadow-2xs">
                    <div class="p-1 rounded-lg bg-emerald-100 text-[#0E4D3C] shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs text-[#0E4D3C] leading-relaxed">
                        <span class="font-bold">Langkah Selanjutnya:</span> Berkas dan slot jadwal akan diverifikasi oleh tim admin di <strong class="font-semibold">jam kerja</strong>. Anda dapat memantau pembaruan status dan catatan admin secara berkala melalui menu <strong>Pengajuan Saya</strong>.
                    </div>
                </div>

                <!-- Tombol Aksi Step 5 Sesuai Mockup dengan Animasi Mikro & Ikon Semantik -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <!-- Tombol Lihat Detail Pengajuan (Utama / Primary Focus - Tampil di Atas pada Mobile) -->
                    <a href="{{ route('pengajuan.show', $subData['ticket_code']) }}" 
                       class="group btn-wizard-primary gap-2 w-full sm:w-auto px-5 py-2.5 sm:order-2">
                        <svg class="w-4 h-4 text-[#0E4D3C] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat Detail Pengajuan</span>
                    </a>

                    <!-- Tombol Pengajuan Saya (Sekunder) -->
                    <a href="{{ route('pengajuan.index') }}" 
                       class="group btn-wizard-secondary gap-2 w-full sm:w-auto px-5 py-2.5 sm:order-1">
                        <svg class="w-4 h-4 text-[#0E4D3C] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Pengajuan Saya</span>
                    </a>
                </div>
            </div>
        @endif

