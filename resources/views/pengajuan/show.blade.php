@extends('layouts.portal')

@section('title', 'Detail Pengajuan ' . $submission->ticket_code . ' | E-VITRON UIN Siber Cirebon')

@section('content')
<div x-data="{ showCancelModal: false }" class="w-full overflow-x-hidden min-h-[calc(100vh-200px)] relative" style="background:#F4F8F6;">
    {{-- Islamic Corner Ornaments Background --}}
    <div class="ornament-pattern" style="position:absolute;left:-40px;top:20px;height:240px;width:240px;opacity:0.35;pointer-events:none;"></div>
    <div class="ornament-pattern" style="position:absolute;right:-40px;top:80px;height:260px;width:260px;opacity:0.35;pointer-events:none;"></div>

    <div class="relative mx-auto max-w-[1400px] px-6 sm:px-10 lg:px-14 py-8 space-y-6 z-10">

        {{-- Bar Navigasi Atas: Tombol Kembali & Nomor Tiket --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('pengajuan.index') }}"
                class="group inline-flex items-center gap-2 rounded-full border border-[#0E4D3C]/20 bg-white/90 px-4 py-1.5 text-xs font-bold text-[#0E4D3C] shadow-xs backdrop-blur-xs transition-all duration-200 hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95 select-none">
                <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Daftar Pengajuan</span>
            </a>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#E3F1EA] border border-[#0E4D3C]/20 text-[#0E4D3C] text-xs font-bold shadow-xs">
                <span class="text-[10px] uppercase font-bold text-[#3D6B5C]">Kode Tiket:</span>
                <span class="font-mono font-bold tracking-wider">{{ $submission->ticket_code }}</span>
            </div>
        </div>

        {{-- Status Banner --}}
        @php
            $statusInfo = [
                'menunggu'   => ['title' => 'Menunggu Pemeriksaan Admin',  'desc' => 'Pengajuan Anda sedang dalam antrean verifikasi Tim Admin E-VITRON.', 'bg' => '#FEF3C7', 'border' => '#FDE68A', 'color' => '#92400E', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'tayang'     => ['title' => 'Materi Sedang Aktif Ditayangkan!', 'desc' => 'Materi Anda saat ini sedang disiarkan secara aktif pada perangkat videotron terpilih.', 'bg' => '#DCFCE7', 'border' => '#BBF7D0', 'color' => '#15803D', 'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'terjadwal'  => ['title' => 'Pengajuan Disetujui (Menunggu Jadwal)', 'desc' => 'Materi Anda telah lolos kurasi dan siap disiarkan otomatis saat tanggal mulai penayangan tiba.', 'bg' => '#CCFBF1', 'border' => '#99F6E4', 'color' => '#0F766E', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'disetujui'  => ['title' => 'Pengajuan Disetujui!',        'desc' => 'Materi Anda telah lolos kurasi dan akan ditayangkan sesuai jadwal.', 'bg' => '#CCFBF1', 'border' => '#99F6E4', 'color' => '#0F766E', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'revisi'     => ['title' => 'Perlu Perbaikan (Revisi)',    'desc' => 'Admin E-VITRON meminta perbaikan materi/link sebelum pengajuan dapat disetujui.', 'bg' => '#FFEDD5', 'border' => '#FDBA74', 'color' => '#C2410C', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                'ditolak'    => ['title' => 'Pengajuan Ditolak',          'desc' => 'Materi pengajuan tidak memenuhi syarat kelayakan penayangan videotron.', 'bg' => '#FEE2E2', 'border' => '#FECACA', 'color' => '#991B1B', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'dibatalkan' => ['title' => 'Pengajuan Dibatalkan',       'desc' => 'Pengajuan ini telah dibatalkan atas permintaan Anda.', 'bg' => '#F1F5F9', 'border' => '#E2E8F0', 'color' => '#64748B', 'icon' => 'M6 18L18 6M6 6l12 12'],
                'selesai'    => ['title' => 'Penayangan Selesai',          'desc' => 'Masa tayang materi videotron ini telah selesai.', 'bg' => '#EEF2FF', 'border' => '#C7D2FE', 'color' => '#4338CA', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            $dispStatus = $submission->display_status;
            $currentStatus = $statusInfo[$dispStatus] ?? ($statusInfo[$submission->status] ?? $statusInfo['menunggu']);
        @endphp

        <div class="p-5 sm:p-6 rounded-2xl border shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            style="background:{{ $currentStatus['bg'] }};border-color:{{ $currentStatus['border'] }};color:{{ $currentStatus['color'] }};">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/70 shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $currentStatus['icon'] }}"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-base sm:text-lg font-bold leading-tight">{{ $currentStatus['title'] }}</h2>
                    <p class="text-xs opacity-90 mt-0.5 leading-relaxed">{{ $currentStatus['desc'] }}</p>
                </div>
            </div>

            @if($submission->status === 'menunggu')
                <div>
                    <button type="button"
                        @click="showCancelModal = true"
                        class="group inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white text-rose-700 hover:bg-rose-700 hover:text-white font-bold text-xs border border-rose-300 shadow-xs transition-all duration-200 hover:-translate-y-0.5 active:scale-95 select-none cursor-pointer">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Batalkan Pengajuan</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- FORM REVISI (Jika status adalah revisi) --}}
        @if($submission->status === 'revisi')
            @php
                $revType = $submission->effective_revision_type;
                $showGdrive = in_array($revType, ['materi', 'keduanya'], true);
                $showSurat = in_array($revType, ['surat', 'keduanya'], true);
            @endphp
            <div class="rounded-2xl p-6 sm:p-7 space-y-4 border border-orange-300 bg-orange-50/80 shadow-md relative overflow-hidden">
                <div class="flex items-center gap-2.5 text-orange-950 font-bold text-base">
                    <div class="h-7 w-7 rounded-full bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <span>
                        @if($revType === 'surat')
                            Perlu Perbaikan Surat Permohonan Resmi
                        @elseif($revType === 'materi')
                            Perlu Perbaikan Materi Tayang (Revisi)
                        @else
                            Perlu Perbaikan Berkas &amp; Materi Tayang
                        @endif
                    </span>
                </div>

                @if($submission->admin_notes)
                    <div class="bg-white p-4 rounded-xl border border-orange-200 text-xs sm:text-sm text-orange-950 font-medium leading-relaxed shadow-2xs">
                        <span class="block text-[10.5px] font-bold uppercase tracking-wider text-orange-700 mb-1">Catatan Perbaikan dari Tim Admin E-VITRON:</span>
                        "{{ $submission->admin_notes }}"
                    </div>
                @endif

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
                      class="space-y-4 pt-2">
                    @csrf

                    @if($showGdrive)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <label class="block text-xs font-bold uppercase text-slate-700">
                                    Tautan Google Drive Materi <span class="text-rose-500">*</span>
                                </label>
                                <template x-if="currentGdrive">
                                    <a :href="currentGdrive" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0E4D3C] hover:underline bg-[#EAF5F0] px-2.5 py-1 rounded-lg border border-[#CDE5D9] transition-colors"
                                       title="Buka tautan Google Drive ini di tab baru">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        <span>Buka Drive ↗</span>
                                    </a>
                                </template>
                            </div>
                            <input type="url" name="gdrive_link" x-model="currentGdrive" {{ $revType === 'materi' ? 'required' : '' }}
                                   placeholder="https://drive.google.com/..."
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-[#0E4D3C] font-mono shadow-2xs">
                            <div class="text-[11px] leading-tight">
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
                                <label class="block text-xs font-bold uppercase text-slate-700">
                                    Unggah Berkas Surat Permohonan Baru (PDF) <span class="text-rose-500">*</span>
                                </label>
                                @if($submission->surat_peminjaman)
                                    <a href="{{ $submission->surat_peminjaman_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0E4D3C] hover:underline bg-[#EAF5F0] px-2.5 py-1 rounded-lg border border-[#CDE5D9] transition-colors"
                                       title="Buka dokumen surat peminjaman sebelumnya di tab baru">
                                        <svg class="w-3.5 h-3.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                                 class="relative border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-colors duration-150 flex flex-col sm:flex-row items-center gap-3.5 bg-white hover:bg-slate-50/70"
                                 :class="[
                                     isDragging ? 'border-[#0E4D3C] bg-[#EAF5F0]/70 scale-[0.99]' : 'border-slate-300 hover:border-slate-400',
                                     fileError ? 'border-rose-400 bg-rose-50/20' : ''
                                 ]">
                                <!-- Icon PDF Badge -->
                                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex flex-col items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-black text-[8px] uppercase tracking-wider leading-none mt-0.5">PDF</span>
                                </div>

                                <div class="text-center sm:text-left min-w-0 flex-1">
                                    <span class="text-xs sm:text-sm font-bold text-slate-800 block">
                                        Klik atau seret berkas surat PDF baru ke sini
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Format PDF resmi bertanda tangan &amp; berstempel &bull; Ukuran maksimal 2 MB
                                    </p>
                                </div>

                                <button type="button" class="shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300/90 shadow-2xs transition-colors pointer-events-none">
                                    Pilih File
                                </button>
                            </div>

                            <!-- Card File Baru Terpilih -->
                            <div x-show="fileName" x-cloak
                                 class="p-3 sm:p-3.5 rounded-xl border border-emerald-300 bg-emerald-50/40 shadow-2xs flex items-center justify-between gap-3 transition-all">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center shrink-0 border border-rose-200 shadow-2xs">
                                        PDF
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-bold text-slate-900 truncate" :title="fileName" x-text="fileName"></p>
                                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                            <span class="text-[11px] text-slate-500 font-mono" x-text="fileSize"></span>
                                            <span class="text-slate-300 text-[10px]">&bull;</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#0E4D3C] bg-[#EAF5F0] px-2 py-0.5 rounded-full border border-[#CDE5D9]">
                                                <svg class="w-2.5 h-2.5 text-[#0E4D3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <span>Siap Dilampirkan</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            @click="$refs.suratInput.click()"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-[#0E4D3C] border border-slate-200 shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer"
                                            title="Pilih file PDF lain">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Ganti File</span>
                                    </button>
                                    <button type="button"
                                            @click="clearFile()"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all cursor-pointer shadow-2xs"
                                            title="Batalkan pilihan file ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                            <div x-show="fileError" x-cloak class="flex items-center gap-1.5 text-xs text-rose-600 font-medium mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span x-text="fileError"></span>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Catatan Balasan Anda <span class="text-rose-500">*</span></label>
                        <textarea name="user_reply" rows="2" required placeholder="{{ $revType === 'surat' ? 'Contoh: Surat permohonan baru bertanda tangan dan berstempel resmi telah dilampirkan...' : 'Contoh: Akses publik Google Drive sudah dibuka dan resolusi video sudah disesuaikan...' }}"
                                  class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-[#0E4D3C] shadow-2xs"></textarea>
                    </div>

                    <button type="submit" class="group inline-flex h-10 items-center gap-2 rounded-full px-6 text-xs font-bold text-white bg-[#0E4D3C] hover:bg-[#123F33] select-none shadow-md cursor-pointer transition-colors duration-150">
                        <span>Kirim Ulang Revisi ke Admin</span>
                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        @endif

        {{-- Detail Informasi Konten --}}
        <div class="relative overflow-hidden"
            style="padding:24px 28px;border-radius:20px;background:linear-gradient(145deg, rgba(255,255,255,0.97) 0%, rgba(252,254,252,0.88) 60%, rgba(255,255,255,0.94) 100%);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.98);box-shadow:0 2px 4px rgba(14,77,60,0.04), 0 6px 20px -3px rgba(14,77,60,0.09), 0 12px 32px -6px rgba(14,77,60,0.06), inset 0 1px 0 rgba(255,255,255,1);">

            <div class="ornament-pattern" style="position:absolute;right:-20px;bottom:-20px;height:100px;width:100px;opacity:0.15;pointer-events:none;"></div>

            <h3 class="text-base sm:text-lg font-montserrat font-extrabold text-[#0E4D3C] border-b border-slate-100 pb-3">
                Detail Informasi Konten
            </h3>

            <div class="grid sm:grid-cols-2 gap-5 text-xs sm:text-sm mt-5">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Judul Kegiatan / Konten</span>
                    <strong class="text-base font-bold text-[#0E4D3C]">{{ $submission->title }}</strong>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Jenis Konten</span>
                    @if($submission->content_type === 'video')
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                            Video @if($submission->video_duration_seconds)({{ $submission->video_duration_seconds }} Detik)@endif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold bg-[#E6F4EA] text-[#0D4734] border border-[#0D4734]/20">
                            Poster
                        </span>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Jadwal Penayangan</span>
                    <strong class="text-slate-800 font-semibold">{{ $submission->start_date->format('d M Y') }} – {{ $submission->end_date->format('d M Y') }}</strong>
                    <span class="text-xs text-slate-400 block mt-0.5">({{ $submission->start_date->diffInDays($submission->end_date) + 1 }} Hari Penayangan)</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Penanggung Jawab (PIC)</span>
                    <strong class="text-slate-800 font-semibold">{{ $submission->pic_name }}</strong>
                    <p class="text-xs text-slate-500 mt-0.5">WA: {{ $submission->whatsapp }} | Unit: {{ $submission->organization ?? '-' }}</p>
                </div>

                <div class="sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Perangkat Videotron Target</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($submission->videotrons as $v)
                            @php
                                $isTroubled = in_array($v->condition, ['gangguan', 'dalam_perbaikan']) || ! (bool) $v->is_active;
                                $statusBadgeText = match(true) {
                                    $v->condition === 'gangguan' => 'Gangguan',
                                    $v->condition === 'dalam_perbaikan' => 'Perbaikan',
                                    ! $v->is_active => 'Nonaktif',
                                    default => 'Normal',
                                };
                            @endphp
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full {{ $isTroubled ? 'bg-amber-50 border border-amber-300 text-amber-900' : 'bg-[#E3F1EA] border border-[#0E4D3C]/20 text-[#0E4D3C]' }} text-xs">
                                <span class="h-1.5 w-1.5 rounded-full {{ $isTroubled ? 'bg-amber-500 animate-pulse' : 'bg-[#0E4D3C]' }}"></span>
                                <span class="font-bold">{{ $v->name }}</span>
                                <span class="{{ $isTroubled ? 'text-amber-700' : 'text-[#3D6B5C]' }}">({{ $v->location }})</span>
                                @if($isTroubled)
                                    <span class="text-[9.5px] font-extrabold px-1.5 py-0.2 rounded-full bg-amber-200/90 text-amber-900 border border-amber-300">
                                        {{ $statusBadgeText }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Banner Peringatan jika ada videotron target yang sedang berkendala --}}
                    @php
                        $troubledList = $submission->videotrons->filter(fn ($v) => in_array($v->condition, ['gangguan', 'dalam_perbaikan']) || ! (bool) $v->is_active);
                    @endphp
                    @if($troubledList->isNotEmpty() && in_array($dispStatus, ['menunggu', 'terjadwal', 'tayang']))
                        <div class="mt-3 p-3.5 rounded-xl bg-amber-50/90 border border-amber-200/90 text-xs text-amber-900 space-y-2 shadow-2xs">
                            @foreach($troubledList as $tv)
                                <div class="flex items-start gap-2.5">
                                    <svg class="h-4 w-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="leading-relaxed">
                                        <strong class="text-amber-950 font-bold">Kendala Teknis {{ $tv->name }}:</strong>
                                        {{ filled($tv->issue_notes) ? $tv->issue_notes : 'Sedang mengalami kendala teknis / pemeliharaan.' }}
                                        @if($tv->estimated_recovery_date)
                                            <span class="font-semibold block sm:inline sm:ml-1 text-amber-800">
                                                (Estimasi normal: {{ \Carbon\Carbon::parse($tv->estimated_recovery_date)->locale('id')->translatedFormat('d M Y') }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tautan Materi Google Drive</span>
                    @php
                        $gdriveDisplay = preg_replace('#^https?://(www\.)?#i', '', (string) ($submission->gdrive_link ?? ''));
                        if (str_contains($gdriveDisplay, 'folders/')) {
                            $cleanDriveText = 'drive.google.com/folders/...';
                        } elseif (str_contains($gdriveDisplay, 'drive.google.com')) {
                            $cleanDriveText = 'drive.google.com/...';
                        } else {
                            $cleanDriveText = \Illuminate\Support\Str::limit($gdriveDisplay, 28, '...');
                        }
                    @endphp
                    <a href="{{ $submission->gdrive_link }}" target="_blank" rel="noopener noreferrer"
                       class="group inline-flex items-center gap-2 text-[#0E4D3C] hover:text-[#D9A93B] font-mono text-xs bg-slate-50 hover:bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200 transition-colors" title="{{ $submission->gdrive_link }}">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#D9A93B] shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                        </svg>
                        <span>{{ $cleanDriveText }}</span>
                    </a>
                </div>

                <div class="sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Surat Permohonan / Peminjaman (PDF)</span>
                    @if($submission->surat_peminjaman)
                        <div class="flex items-center justify-between gap-3 p-3 bg-white rounded-xl border border-slate-200 shadow-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="h-8 w-8 rounded-lg bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center shrink-0">PDF</span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate" title="Dokumen Surat Permohonan (.pdf)">Dokumen Surat Permohonan (.pdf)</div>
                                    <div class="text-[11px] text-emerald-600 font-medium">✓ Berkas Resmi Terlampir</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ $submission->surat_peminjaman_url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0E4D3C] hover:bg-[#165641] text-white text-xs font-bold shadow-2xs transition-all select-none cursor-pointer">
                                    Lihat
                                </a>
                                <a href="{{ $submission->surat_peminjaman_url }}" download
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 text-xs font-semibold shadow-2xs transition-all select-none cursor-pointer">
                                    Unduh
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/80 text-amber-800 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Belum ada surat peminjaman yang dilampirkan.</span>
                        </div>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Deskripsi Kegiatan</span>
                    <p class="text-slate-700 text-xs leading-relaxed bg-[#F8FAF9] p-4 rounded-xl border border-slate-200/80">
                        {{ $submission->description }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Timeline Riwayat Status (Audit Trail) --}}
        <div class="relative overflow-hidden"
            style="padding:24px 28px;border-radius:20px;background:linear-gradient(145deg, rgba(255,255,255,0.97) 0%, rgba(252,254,252,0.88) 60%, rgba(255,255,255,0.94) 100%);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.98);box-shadow:0 2px 4px rgba(14,77,60,0.04), 0 6px 20px -3px rgba(14,77,60,0.09), 0 12px 32px -6px rgba(14,77,60,0.06), inset 0 1px 0 rgba(255,255,255,1);">

            <h3 class="text-base sm:text-lg font-montserrat font-extrabold text-[#0E4D3C] mb-6">
                Riwayat Status Tiket
            </h3>

            <div class="relative pl-6 border-l-2 border-[#0E4D3C]/20 space-y-6">
                @foreach($submission->statusHistories as $history)
                    <div class="relative group">
                        <!-- Dot Indicator -->
                        <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#0E4D3C] border-2 border-white shadow-xs"></div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-[#0E4D3C] capitalize">Status: {{ $history->to_status }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $history->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $history->notes }}</p>
                            @if($history->user)
                                <span class="text-[10px] text-[#3D6B5C] font-semibold">Oleh: {{ $history->user->name }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- MODAL KONFIRMASI PEMBATALAN PENGAJUAN (Selaras Desain UIN SSC) --}}
    <div x-cloak x-show="showCancelModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
         role="dialog" aria-modal="true">

        {{-- Backdrop Gelap Lembut --}}
        <div x-show="showCancelModal"
             @click="showCancelModal = false"
             class="fixed inset-0 bg-black/60 cursor-pointer"
             style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;"></div>

        {{-- Kotak Modal Dialog --}}
        <div x-show="showCancelModal"
             class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-slate-100 z-10 text-center">

            {{-- Ornamen Watermark Sudut Modal --}}
            <div class="ornament-pattern" style="position:absolute;right:-15px;bottom:-15px;height:75px;width:75px;opacity:0.18;pointer-events:none;"></div>

            {{-- Ikon Peringatan Rose --}}
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600 mb-3.5 border border-rose-100">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 class="font-montserrat text-lg font-bold text-slate-900">
                Batalkan Pengajuan Ini?
            </h3>

            <p class="mt-2 text-xs sm:text-[13px] leading-relaxed text-slate-600">
                Apakah Anda yakin ingin membatalkan pengajuan <span class="font-bold text-[#0E4D3C]">"{{ $submission->title }}"</span> (<span class="font-mono font-bold text-slate-700">{{ $submission->ticket_code }}</span>)?
            </p>

            <p class="mt-2 text-xs text-rose-600 bg-rose-50 py-2 px-3.5 rounded-xl border border-rose-100 font-medium">
                Pengajuan yang dibatalkan tidak dapat diproses lagi oleh tim Admin.
            </p>

            {{-- Tombol Aksi Modal: Selaras Penuh dengan Desain & Animasi Aplikasi --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-2.5">
                <button type="button"
                        @click="showCancelModal = false"
                        class="group w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-full border border-[#0E4D3C]/20 bg-white/90 px-5 py-2.5 text-xs font-bold text-[#0E4D3C] shadow-xs backdrop-blur-xs transition-all duration-200 hover:border-[#0E4D3C] hover:bg-[#0E4D3C] hover:text-white hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:scale-95 select-none cursor-pointer">
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali</span>
                </button>

                <form action="{{ route('pengajuan.cancel', $submission->ticket_code) }}" method="POST" class="w-full sm:w-auto m-0">
                    @csrf
                    <button type="submit"
                            class="group w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-full bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-rose-600/30 hover:bg-rose-700 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 select-none cursor-pointer">
                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Ya, Batalkan Pengajuan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
