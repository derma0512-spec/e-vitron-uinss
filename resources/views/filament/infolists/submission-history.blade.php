@php
    $histories = $getRecord()->statusHistories()->with('user')->get();
    $revisions = $getRecord()->revisions()->get();
@endphp

<div class="space-y-6">
    {{-- Card Riwayat Status --}}
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.875rem; padding: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
            <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #E8F3EC; color: #0F3D2E; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1.15rem; height: 1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0;">Riwayat Perubahan Status</h3>
                <span style="font-size: 0.75rem; color: #64748B;">Kronologi keputusan dan aksi administratif</span>
            </div>
        </div>

        @if($histories->isEmpty())
            <div style="text-align: center; padding: 1.5rem; color: #94A3B8; font-size: 0.8125rem;">
                Belum ada riwayat perubahan status untuk pengajuan ini.
            </div>
        @else
            <div style="position: relative; padding-left: 1.5rem; border-left: 2px solid #E2E8F0; margin-left: 0.75rem;" class="space-y-4">
                @foreach($histories as $history)
                    @php
                        $color = match($history->to_status) {
                            'disetujui' => ['dot' => '#16A34A', 'bg' => '#DCFCE7', 'text' => '#15803D', 'label' => 'Disetujui'],
                            'revisi' => ['dot' => '#EA580C', 'bg' => '#FFEDD5', 'text' => '#C2410C', 'label' => 'Revisi'],
                            'ditolak' => ['dot' => '#DC2626', 'bg' => '#FEE2E2', 'text' => '#B91C1C', 'label' => 'Ditolak'],
                            'menunggu' => ['dot' => '#D97706', 'bg' => '#FEF3C7', 'text' => '#B45309', 'label' => 'Menunggu'],
                            'selesai' => ['dot' => '#0284C7', 'bg' => '#E0F2FE', 'text' => '#0369A1', 'label' => 'Selesai'],
                            default => ['dot' => '#64748B', 'bg' => '#F1F5F9', 'text' => '#475569', 'label' => ucfirst($history->to_status)],
                        };
                    @endphp
                    <div style="position: relative;">
                        {{-- Dot Indicator --}}
                        <div style="position: absolute; left: -1.9rem; top: 0.25rem; width: 0.85rem; height: 0.85rem; border-radius: 9999px; background: {{ $color['dot'] }}; border: 2px solid #FFFFFF; box-shadow: 0 0 0 1px {{ $color['dot'] }};"></div>

                        <div style="background: #F8FAF9; border: 1px solid #E2E8F0; border-radius: 0.625rem; padding: 0.75rem 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.35rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                        {{ $color['label'] }}
                                    </span>
                                    @if($history->from_status)
                                        <span style="font-size: 0.75rem; color: #64748B;">
                                            dari <span style="font-weight: 600; text-transform: capitalize;">{{ $history->from_status }}</span>
                                        </span>
                                    @endif
                                </div>
                                <span style="font-size: 0.6875rem; color: #94A3B8; font-variant-numeric: tabular-nums;">
                                    {{ $history->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <div style="font-size: 0.75rem; color: #475569; margin-bottom: 0.25rem;">
                                Oleh: <strong style="color: #0F172A;">{{ $history->user?->name ?? 'Sistem / Pengaju' }}</strong>
                            </div>

                            @if($history->notes)
                                <div style="font-size: 0.8125rem; color: #1E293B; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.375rem; padding: 0.5rem 0.75rem; margin-top: 0.35rem; font-style: italic;">
                                    "{{ $history->notes }}"
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Card Riwayat Revisi (Jika Ada) --}}
    @if($revisions->isNotEmpty())
        <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.875rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #FFEDD5; color: #C2410C; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.15rem; height: 1.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0;">Catatan & Riwayat Revisi Pengaju</h3>
                    <span style="font-size: 0.75rem; color: #64748B;">Riwayat feedback perbaikan yang pernah diminta admin</span>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($revisions as $index => $rev)
                    <div style="background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 0.625rem; padding: 0.875rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #9A3412;">
                                Revisi #{{ $revisions->count() - $index }}
                            </span>
                            <span style="font-size: 0.6875rem; color: #9A3412;">
                                Diminta: {{ $rev->requested_at?->format('d M Y, H:i') ?? '-' }}
                            </span>
                        </div>

                        <div style="font-size: 0.8125rem; color: #7C2D12; background: rgba(255, 255, 255, 0.7); border: 1px solid #FDBA74; border-radius: 0.375rem; padding: 0.5rem 0.75rem; margin-bottom: 0.5rem;">
                            <strong>Catatan Admin:</strong> {{ $rev->admin_feedback }}
                        </div>

                        @if($rev->previous_gdrive_link)
                            <div style="font-size: 0.75rem; color: #64748B;">
                                Link materi saat revisi:
                                <a href="{{ $rev->previous_gdrive_link }}" target="_blank" rel="noopener noreferrer" style="color: #0E4D3C; text-decoration: underline; word-break: break-all;">
                                    {{ Str::limit($rev->previous_gdrive_link, 50) }} &nearr;
                                </a>
                            </div>
                        @endif

                        @if($rev->resolved_at)
                            <div style="font-size: 0.6875rem; color: #15803D; margin-top: 0.35rem; font-weight: 600;">
                                &check; Telah diperbaiki pengaju pada: {{ $rev->resolved_at->format('d M Y, H:i') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
