    {{-- CARD 1: STATUS BANNER & PANDUAN PENGAJU --}}
    @if($submission->status === 'menunggu')
        <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3.5 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5 border border-amber-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs sm:text-sm font-bold text-amber-950">Menunggu Pemeriksaan Admin</h4>
                    <p class="text-xs text-amber-900/85 mt-1 leading-relaxed">
                        Pengajuan Anda berada dalam antrean kurasi Tim Admin E-VITRON. Tim kurasi akan memeriksa kelengkapan berkas surat dan kesiapan materi tayang Anda.
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-amber-200/60 flex items-center gap-1.5 text-[11px] text-amber-900/75 flex-wrap">
                        <span>Ada kesalahan data atau agenda dibatalkan?</span>
                        <button type="button"
                                @click="confirmCancel('{{ $submission->ticket_code }}', '{{ addslashes($submission->title) }}', $el)"
                                class="inline-flex items-center gap-0.5 font-bold text-rose-600 hover:text-rose-700 underline underline-offset-2 hover:translate-x-0.5 active:scale-95 transition-all duration-150 cursor-pointer select-none">
                            <span>Batalkan pengajuan ini</span>
                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($dispStatus === 'tayang')
        <div class="rounded-xl border border-emerald-300 bg-emerald-50/90 p-3.5 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs sm:text-sm font-bold text-emerald-950">Sedang Tayang di Videotron!</h4>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[9.5px] font-extrabold text-emerald-800 border border-emerald-300">
                            AKTIF
                        </span>
                    </div>
                    <p class="text-xs text-emerald-900/85 mt-1 leading-relaxed">
                        Materi publikasi Anda saat ini sedang disiarkan secara aktif pada layar videotron kampus terpilih hingga {{ $eDate?->translatedFormat('d M Y') }}.
                    </p>
                </div>
            </div>
        </div>
    @elseif($dispStatus === 'terjadwal' || ($submission->status === 'disetujui' && $dispStatus !== 'selesai'))
        <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-3.5 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 mt-0.5 border border-teal-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-teal-950">Pengajuan Disetujui (Menunggu Jadwal Tayang)</h4>
                    <p class="text-xs text-teal-900/80 mt-0.5 leading-relaxed">
                        Materi telah lolos kurasi Admin E-VITRON dan siap disiarkan secara otomatis saat tanggal penayangan dimulai ({{ $sDate?->translatedFormat('d M Y') }}).
                    </p>
                </div>
            </div>
        </div>
    @elseif($submission->status === 'revisi')
        @php
            $revType = $submission->effective_revision_type;
            $showGdrive = in_array($revType, ['materi', 'keduanya'], true);
            $showSurat = in_array($revType, ['surat', 'keduanya'], true);
        @endphp
        <div class="rounded-xl border border-orange-300 bg-orange-50/90 p-3.5 shadow-2xs space-y-3">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-orange-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-orange-950">
                        @if($revType === 'surat')
                            Perlu Perbaikan Surat Permohonan Resmi
                        @elseif($revType === 'materi')
                            Perlu Perbaikan Materi Tayang (Revisi)
                        @else
                            Perlu Perbaikan Berkas &amp; Materi Tayang
                        @endif
                    </h4>
                    <p class="text-xs text-orange-900/80 mt-0.5 leading-relaxed">
                        @if($revType === 'surat')
                            Admin E-VITRON meminta pembaruan dokumen Surat Permohonan resmi sebelum pengajuan ini dapat disetujui.
                        @elseif($revType === 'materi')
                            Admin E-VITRON meminta perbaikan materi/tautan Google Drive sebelum pengajuan ini dapat disetujui.
                        @else
                            Admin E-VITRON meminta perbaikan dokumen surat dan tautan materi sebelum pengajuan ini dapat disetujui.
                        @endif
                    </p>
                </div>
            </div>

            @if($submission->admin_notes)
                <div class="bg-white/95 p-3 rounded-xl border border-orange-200 text-xs text-orange-950 leading-relaxed">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-orange-700 mb-0.5">Catatan Perbaikan dari Admin:</span>
                    "{{ $submission->admin_notes }}"
                </div>
            @endif

            {{-- Form Perbaikan Cepat Langsung di Drawer --}}
            <form action="{{ route('pengajuan.revision', $submission->ticket_code) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  x-data="{
                      originalGdrive: '{{ $submission->gdrive_link }}',
                      currentGdrive: '{{ $submission->gdrive_link }}',
                      fileName: '',
                      fileSize: '',
                      isDragging: false,
                      fileError: '',
                      validateSubmit(e) {
                          if ('{{ $revType }}' === 'surat' && !this.fileName) {
                              e.preventDefault();
                              this.fileError = 'Wajib memilih berkas Surat Permohonan baru (PDF).';
                              return false;
                          }
                          this.fileError = '';
                          return true;
                      },
                      handleFile(file) {
                          if (!file) return;
                          if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                              this.fileError = 'Berkas surat harus berformat PDF (.pdf).';
                              this.clearFile();
                              return;
                          }
                          if (file.size > 2 * 1024 * 1024) {
                              this.fileError = 'Ukuran berkas surat maksimal 2 MB.';
                              this.clearFile();
                              return;
                          }
                          this.fileError = '';
                          this.fileName = file.name;
                          this.fileSize = (file.size / 1024).toFixed(0) + ' KB';
                      },
                      handleFileSelect(e) {
                          if (e.target.files && e.target.files[0]) {
                              this.handleFile(e.target.files[0]);
                          }
                      },
                      handleFileDrop(e) {
                          this.isDragging = false;
                          if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                              const file = e.dataTransfer.files[0];
                              this.$refs.suratInput.files = e.dataTransfer.files;
                              this.handleFile(file);
                          }
                      },
                      clearFile() {
                          this.fileName = '';
                          this.fileSize = '';
                          if (this.$refs.suratInput) {
                              this.$refs.suratInput.value = '';
                          }
                      }
                  }"
                  @submit="validateSubmit($event)"
                  class="space-y-3 pt-2 border-t border-orange-200/80">
                @csrf

                @if($showGdrive)
                    <div class="space-y-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <label class="block text-[10.5px] font-bold uppercase tracking-wider text-slate-700">
                                Tautan Google Drive Materi <span class="text-rose-500">*</span>
                            </label>
                            <template x-if="currentGdrive">
                                <a :href="currentGdrive" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-[#0E4D3C] hover:underline bg-[#EAF5F0] px-2 py-0.5 rounded-md border border-[#CDE5D9] transition-colors"
                                   title="Buka tautan Google Drive ini di tab baru">
                                    <svg class="w-3 h-3 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Buka Drive ↗</span>
                                </a>
                            </template>
                        </div>
                        <input type="url" name="gdrive_link" x-model="currentGdrive" {{ $revType === 'materi' ? 'required' : '' }}
                               placeholder="https://drive.google.com/..."
                               class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-slate-900 text-xs focus:outline-none focus:border-[#0E4D3C] font-mono shadow-2xs">
                        <div class="text-[10px] leading-tight">
                            <span x-show="originalGdrive && currentGdrive === originalGdrive" class="text-slate-500">
                                &bull; Tautan lama dipertahankan. Cukup pastikan izin publik dibuka atau berkas baru telah diunggah di Drive.
                            </span>
                            <span x-show="currentGdrive && currentGdrive !== originalGdrive" x-cloak class="text-emerald-700 font-medium">
                                ✓ Tautan baru akan disimpan untuk ditinjau admin.
                            </span>
                        </div>
                    </div>
                @endif

                @if($showSurat)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <label class="block text-[10.5px] font-bold uppercase tracking-wider text-slate-700">
                                Berkas Surat Permohonan Baru (PDF) <span class="text-rose-500">*</span>
                            </label>
                            @if($submission->surat_peminjaman)
                                <a href="{{ $submission->surat_peminjaman_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-[#0E4D3C] hover:underline bg-[#EAF5F0] px-2 py-0.5 rounded-md border border-[#CDE5D9] transition-colors"
                                   title="Buka dokumen surat peminjaman sebelumnya di tab baru">
                                    <svg class="w-3 h-3 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Lihat Berkas Sebelumnya</span>
                                </a>
                            @endif
                        </div>

                        <!-- Dropzone Area (Belum ada file baru dipilih) -->
                        <div x-show="!fileName"
                             @click="$refs.suratInput.click()"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleFileDrop($event)"
                             class="relative border-2 border-dashed rounded-xl p-3 sm:p-3.5 text-center cursor-pointer transition-colors duration-150 flex items-center gap-3 bg-white hover:bg-slate-50/70"
                             :class="[
                                 isDragging ? 'border-[#0E4D3C] bg-[#EAF5F0]/70 scale-[0.99]' : 'border-slate-300 hover:border-slate-400',
                                 fileError ? 'border-rose-400 bg-rose-50/20' : ''
                             ]">
                            <!-- Icon PDF Badge -->
                            <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-600 flex flex-col items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-black text-[7px] uppercase tracking-wider leading-none mt-0.5">PDF</span>
                            </div>

                            <div class="text-left min-w-0 flex-1">
                                <span class="text-xs font-bold text-slate-800 block truncate">
                                    Klik atau seret berkas surat PDF baru ke sini
                                </span>
                                <p class="text-[10px] text-slate-500 mt-0.5 truncate">
                                    Format PDF resmi bertanda tangan &amp; stempel &bull; Maksimal 2 MB
                                </p>
                            </div>

                            <button type="button" class="shrink-0 px-3 py-1.5 rounded-lg text-[11px] font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300/90 shadow-2xs transition-colors pointer-events-none">
                                Pilih File
                            </button>
                        </div>

                        <!-- Card File Baru Terpilih -->
                        <div x-show="fileName" x-cloak
                             class="p-2.5 sm:p-3 rounded-xl border border-emerald-300 bg-emerald-50/40 shadow-2xs flex items-center justify-between gap-2.5 transition-all">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-rose-200 shadow-2xs">
                                    PDF
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate" :title="fileName" x-text="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10.5px] text-slate-500 font-mono" x-text="fileSize"></span>
                                        <span class="text-slate-300 text-[9px]">&bull;</span>
                                        <span class="inline-flex items-center gap-1 text-[9.5px] font-bold text-[#0E4D3C] bg-[#EAF5F0] px-1.5 py-0.5 rounded border border-[#CDE5D9]">
                                            <svg class="w-2.5 h-2.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Siap Dilampirkan</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button"
                                        @click="$refs.suratInput.click()"
                                        class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-[#0E4D3C] border border-slate-200 shadow-2xs transition-all flex items-center gap-1 cursor-pointer"
                                        title="Pilih file PDF lain">
                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button"
                                        @click="clearFile()"
                                        class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all cursor-pointer shadow-2xs"
                                        title="Batalkan pilihan file ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Native hidden file input -->
                        <input type="file"
                               x-ref="suratInput"
                               name="surat_peminjaman"
                               accept=".pdf,application/pdf"
                               @change="handleFileSelect($event)"
                               class="hidden">

                        <!-- Pesan Kesalahan Validasi Klien -->
                        <div x-show="fileError" x-cloak class="flex items-center gap-1.5 text-[11px] text-rose-600 font-medium mt-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span x-text="fileError"></span>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-[10.5px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Balasan Anda <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="user_reply" rows="2" required
                              placeholder="{{ $revType === 'surat' ? 'Contoh: Surat permohonan baru bertanda tangan dan berstempel resmi telah dilampirkan...' : 'Contoh: Akses Google Drive sudah dibuka dan resolusi materi disesuaikan...' }}"
                              class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-slate-900 text-xs focus:outline-none focus:border-[#0E4D3C] shadow-2xs"></textarea>
                </div>

                <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-lg px-4 text-xs font-bold text-white bg-[#0E4D3C] hover:bg-[#123F33] select-none shadow-xs cursor-pointer transition-colors duration-150">
                    <span>Kirim Ulang ke Admin</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>
    @elseif($submission->status === 'ditolak')
        <div class="rounded-xl border border-rose-200 bg-rose-50/70 p-3.5 shadow-2xs space-y-2">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center shrink-0 mt-0.5 border border-rose-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-rose-950">Pengajuan Tidak Disetujui (Ditolak)</h4>
                    <p class="text-xs text-rose-900/80 mt-0.5 leading-relaxed">
                        Materi pengajuan tidak memenuhi kriteria kelayakan penayangan videotron UIN Siber Cirebon.
                    </p>
                </div>
            </div>

            @if($submission->admin_notes)
                <div class="bg-white/95 p-3 rounded-xl border border-rose-200 text-xs text-rose-950 leading-relaxed">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-rose-700 mb-0.5">Alasan Penolakan:</span>
                    "{{ $submission->admin_notes }}"
                </div>
            @endif
        </div>
    @elseif($submission->status === 'dibatalkan')
        <div class="rounded-xl border border-slate-200 bg-slate-100/70 p-3.5 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-slate-800">Pengajuan Dibatalkan</h4>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                        Pengajuan ini telah dibatalkan secara mandiri sebelum proses verifikasi selesai.
                    </p>
                </div>
            </div>
        </div>
    @elseif($submission->status === 'selesai' || $dispStatus === 'selesai')
        <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-3.5 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="h-7 w-7 rounded-full bg-sky-100 text-sky-800 flex items-center justify-center shrink-0 mt-0.5 border border-sky-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-sky-950">Masa Penayangan Selesai</h4>
                    <p class="text-xs text-sky-900/80 mt-0.5 leading-relaxed">
                        Periode tayang materi videotron telah berakhir dan telah diarsipkan secara aman di sistem.
                    </p>
                </div>
            </div>
        </div>
    @endif
