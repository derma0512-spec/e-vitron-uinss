<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideotronResource\Pages;
use App\Models\Videotron;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class VideotronResource extends Resource
{
    protected static ?string $model = Videotron::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static ?string $navigationLabel = 'Kelola Videotron';

    protected static ?string $modelLabel = 'Perangkat Videotron';

    protected static ?string $pluralModelLabel = 'Daftar Videotron';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Perangkat')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Perangkat')
                            ->required()
                            ->autofocus(false)
                            ->placeholder('Contoh: Videotron Gedung Siber'),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi Fisik')
                            ->required()
                            ->placeholder('Contoh: Lobby Utama Gedung SSC'),
                        Forms\Components\TextInput::make('resolution')
                            ->label('Resolusi')
                            ->placeholder('Contoh: 1920x1080')
                            ->default('1920x1080')
                            ->required(),
                        Forms\Components\ToggleButtons::make('orientation')
                            ->label('Orientasi Layar')
                            ->options([
                                'landscape' => 'Landscape (Mendatar)',
                                'portrait' => 'Portrait (Tegak)',
                            ])
                            ->icons([
                                'landscape' => 'heroicon-m-computer-desktop',
                                'portrait' => 'heroicon-m-device-phone-mobile',
                            ])
                            ->default('landscape')
                            ->inline()
                            ->required(),
                        Forms\Components\TextInput::make('operating_hours')
                            ->label('Jam Operasional')
                            ->prefixIcon('heroicon-m-clock')
                            ->placeholder('Contoh: 06.00 – 22.00 WIB')
                            ->default('06.00 – 22.00 WIB')
                            ->helperText('Jadwal tayang harian videotron yang tampil pada kartu informasi & formulir pemohon.')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Perangkat Videotron')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->rules(['mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp'])
                            ->directory('videotrons')
                            ->disk('public')
                            ->maxSize(5120)
                            ->imagePreviewHeight('220')
                            ->imageEditor()
                            ->placeholder(new HtmlString('Tarik & lepas file ke sini atau <span class="filepond--label-action">Telusuri</span>'))
                            ->helperText(function (?Videotron $record): HtmlString {
                                $hint = '<div style="font-size: 12px; color: #64748B; margin-top: 6px;">Format yang didukung: JPG, PNG, WEBP (maksimal 5MB).</div>';

                                if (! $record) {
                                    return new HtmlString($hint);
                                }

                                if ($record->hasCustomImage()) {
                                    return new HtmlString(
                                        $hint
                                        .'<div style="font-size: 12px; color: #065F46; background-color: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 10px 12px; margin-top: 10px; line-height: 1.45;">'
                                        .'<strong>Foto unggahan khusus aktif</strong> pada pratinjau di atas. Klik ikon <strong>✕</strong> pada pratinjau jika ingin menghapusnya dan kembali memakai foto resmi kampus.'
                                        .'</div>'
                                    );
                                }

                                $url = e($record->image_url);
                                $isCampusAsset = str_starts_with($record->getDefaultImagePath(), 'images/videotrons/');
                                $sourceText = $isCampusAsset ? 'Foto Resmi Kampus' : 'Foto Default Sistem';
                                $badgeBg = $isCampusAsset ? '#ECFDF5' : '#F1F5F9';
                                $badgeColor = $isCampusAsset ? '#065F46' : '#475569';
                                $badgeBorder = $isCampusAsset ? '#A7F3D0' : '#CBD5E1';
                                $caption = $isCampusAsset
                                    ? 'Tersimpan permanen di sistem (format WebP). Unggah file baru di atas hanya jika ingin menggantinya dengan foto lain.'
                                    : 'Unggah file baru di atas jika ingin mengganti foto dokumentasi fisik perangkat ini.';

                                return new HtmlString('
                                    '.$hint.'
                                    <div class="videotron-active-photo-card" style="margin-top: 12px; display: flex; align-items: center; gap: 14px; padding: 12px; background-color: #F8FAF9; border: 1px solid #E2E8F0; border-radius: 12px; max-width: 100%;">
                                        <div style="width: 140px; min-width: 140px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #CBD5E1; background: #E2E8F0; flex-shrink: 0;">
                                            <img src="'.$url.'" alt="Foto Videotron" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 4px; min-width: 0;">
                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                <span style="font-size: 13px; font-weight: 700; color: #1E293B;">Foto Aktif Saat Ini</span>
                                                <span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background-color: '.$badgeBg.'; color: '.$badgeColor.'; border: 1px solid '.$badgeBorder.';">'.$sourceText.'</span>
                                            </div>
                                            <span style="font-size: 12px; color: #64748B; line-height: 1.4;">'.$caption.'</span>
                                        </div>
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Cari videotron atau lokasi...')
            ->searchDebounce('300ms')
            ->hiddenFilterIndicators()
            ->emptyStateHeading('Tidak ada videotron ditemukan')
            ->emptyStateDescription('Coba gunakan kata kunci pencarian nama atau lokasi yang lain.')
            ->emptyStateIcon('heroicon-o-tv')
            ->recordClasses(function (Videotron $record, Table $table): ?string {
                $mountedKey = $table->getLivewire()?->getMountedTableActionRecordKey();

                return ($mountedKey !== null && (string) $mountedKey === (string) $record->getKey())
                    ? 'videotron-row-active'
                    : null;
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->getStateUsing(fn (Videotron $record): string => $record->image_url)
                    ->circular()
                    ->width(44)
                    ->height(44)
                    ->alignLeft(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Videotron & Lokasi')
                    ->weight('bold')
                    ->description(fn (Videotron $record): string => $record->location)
                    ->searchable(['name', 'location'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('resolution')
                    ->label('Resolusi & Rasio')
                    ->formatStateUsing(fn ($state, Videotron $record): string => str_replace('x', ' × ', (string) $record->resolution))
                    ->description(fn (Videotron $record): string => match ($record->orientation) {
                        'landscape' => 'Landscape (16:9)',
                        'portrait' => 'Portrait (9:16)',
                        default => ucfirst($record->orientation),
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn (string $state, Videotron $record): string => match (true) {
                        ! $record->is_active && $state === 'normal' => 'Nonaktif',
                        $state === 'normal' => 'Normal',
                        $state === 'gangguan' => 'Gangguan',
                        $state === 'dalam_perbaikan' => 'Perbaikan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state, Videotron $record): string => match (true) {
                        ! $record->is_active && $state === 'normal' => 'gray',
                        $state === 'normal' => 'success',
                        $state === 'gangguan' => 'danger',
                        $state === 'dalam_perbaikan' => 'warning',
                        default => 'gray',
                    })
                    ->description(function (Videotron $record): string|HtmlString|null {
                        $isOverdue = (! $record->is_active || $record->condition !== 'normal')
                            && $record->estimated_recovery_date
                            && $record->estimated_recovery_date->endOfDay()->isPast();

                        if ($isOverdue) {
                            return new HtmlString('
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.6875rem; font-weight: 600; color: #D97706; margin-top: 2px;">
                                    <svg style="width: 13px; height: 13px; flex-shrink: 0; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <span>Tenggat lewat ('.$record->estimated_recovery_date->format('d/m').')</span>
                                </span>
                            ');
                        }

                        return (! $record->is_active || $record->condition !== 'normal') && $record->issue_notes
                            ? Str::limit($record->issue_notes, 26)
                            : null;
                    })
                    ->tooltip(function (Videotron $record): ?string {
                        $isOverdue = (! $record->is_active || $record->condition !== 'normal')
                            && $record->estimated_recovery_date
                            && $record->estimated_recovery_date->endOfDay()->isPast();

                        $text = $record->issue_notes ?: 'Tidak ada catatan teknis';

                        return $isOverdue
                            ? 'Tenggat pemulihan telah lewat ('.$record->estimated_recovery_date->format('d M Y').'). Klik switch Aktif atau tombol Kondisi untuk memulihkan.'
                            : $text;
                    })
                    ->alignCenter(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->alignCenter()
                    ->afterStateUpdated(function (Videotron $record, bool $state) {
                        $oldCondition = $record->condition;
                        if ($state && in_array($oldCondition, ['gangguan', 'dalam_perbaikan'])) {
                            $record->condition = 'normal';
                            $record->issue_notes = null;
                            $record->estimated_recovery_date = null;
                            $record->save();

                            $record->notifyAffectedSubmissions('recovered');
                        }
                    }),
            ])
            ->filters([])
            ->recordAction('edit')
            ->actionsColumnLabel('Aksi')
            ->actionsAlignment(Alignment::Center->value)
            ->actions([
                Tables\Actions\Action::make('quick_condition')
                    ->label('Kondisi')
                    ->icon('heroicon-m-wrench')
                    ->button()
                    ->size('xs')
                    ->color(fn (Videotron $record): string => match (true) {
                        ! $record->is_active && $record->condition === 'normal' => 'gray',
                        $record->condition === 'normal' => 'success',
                        $record->condition === 'gangguan' => 'danger',
                        $record->condition === 'dalam_perbaikan' => 'warning',
                        default => 'gray',
                    })
                    ->extraModalWindowAttributes(['class' => 'modal-ubah-status-videotron'])
                    ->modalWidth('lg')
                    ->modalHeading('Ubah Status Videotron')
                    ->modalDescription(fn (Videotron $record): string => $record->name.' • '.$record->location)
                    ->modalIcon('heroicon-o-tv')
                    ->modalIconColor('primary')
                    ->modalAlignment(Alignment::Left)
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label(new HtmlString('<span wire:loading.remove wire:target="callMountedTableAction">Simpan Perubahan</span><span wire:loading wire:target="callMountedTableAction">Menyimpan...</span>'))
                        ->color('primary')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->form([
                        Forms\Components\Hidden::make('condition_drafts')
                            ->default(function (Videotron $record): array {
                                $currentCondition = (! $record->is_active && $record->condition === 'normal') ? 'nonaktif' : $record->condition;

                                return [
                                    $currentCondition => [
                                        'date' => $record->estimated_recovery_date?->format('Y-m-d'),
                                        'notes' => $record->issue_notes,
                                    ],
                                ];
                            }),
                        Forms\Components\Radio::make('condition')
                            ->label('Kondisi Layar')
                            ->columns(2)
                            ->gridDirection('row')
                            ->extraAttributes(['class' => 'videotron-condition-radio-grid'])
                            ->options([
                                'normal' => 'Normal',
                                'dalam_perbaikan' => 'Dalam Perbaikan',
                                'gangguan' => 'Gangguan',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->descriptions([
                                'normal' => 'Aktif & siap beroperasi',
                                'dalam_perbaikan' => 'Sedang dalam perbaikan',
                                'gangguan' => 'Layar mati / rusak',
                                'nonaktif' => 'Dinonaktifkan sementara',
                            ])
                            ->default(fn (Videotron $record): string => (! $record->is_active && $record->condition === 'normal') ? 'nonaktif' : $record->condition)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state, ?string $old) {
                                if (! $state || $state === $old) {
                                    return;
                                }

                                $drafts = $get('condition_drafts') ?? [];
                                if ($old) {
                                    $drafts[$old] = [
                                        'date' => $get('estimated_recovery_date'),
                                        'notes' => $get('issue_notes'),
                                    ];
                                    $set('condition_drafts', $drafts);
                                }

                                if ($state === 'normal') {
                                    $set('estimated_recovery_date', null);
                                    $set('issue_notes', null);

                                    return;
                                }

                                $targetDraft = $drafts[$state] ?? null;
                                $set('estimated_recovery_date', $targetDraft['date'] ?? null);
                                $set('issue_notes', $targetDraft['notes'] ?? null);
                            })
                            ->required(),
                        Forms\Components\Placeholder::make('auto_inactive_notice')
                            ->hiddenLabel()
                            ->content(new HtmlString('
                                <div class="videotron-notice-box">
                                    <svg class="videotron-notice-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Perangkat otomatis dinonaktifkan dari sistem pengajuan saat berstatus Gangguan atau Dalam Perbaikan.</span>
                                </div>
                            '))
                            ->visible(fn (Forms\Get $get): bool => in_array($get('condition'), ['gangguan', 'dalam_perbaikan'])),
                        Forms\Components\DatePicker::make('estimated_recovery_date')
                            ->label(fn (Forms\Get $get): HtmlString => match ($get('condition')) {
                                'nonaktif' => new HtmlString('Aktif Kembali Pada <span class="font-normal text-slate-400" style="font-size: 11px;">(opsional)</span>'),
                                default => new HtmlString('Perkiraan Normal Kembali <span class="font-normal text-slate-400" style="font-size: 11px;">(opsional)</span>'),
                            })
                            ->placeholder('Pilih tanggal')
                            ->helperText(fn (Forms\Get $get): string => match ($get('condition')) {
                                'nonaktif' => 'Perkiraan tanggal videotron akan diaktifkan kembali.',
                                default => 'Perkiraan tanggal videotron selesai diperbaiki dan siap beroperasi.',
                            })
                            ->default(fn (Videotron $record) => $record->estimated_recovery_date?->format('Y-m-d'))
                            ->displayFormat('d M Y')
                            ->timezone(config('app.timezone'))
                            ->locale('id')
                            ->weekStartsOnMonday()
                            ->minDate(fn (): string => now('Asia/Jakarta')->toDateString())
                            ->validationMessages([
                                'after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
                            ])
                            ->closeOnDateSelection()
                            ->native(false)
                            ->extraAlpineAttributes([
                                'x-init' => '$nextTick(() => window.initVideotronCalendar && window.initVideotronCalendar($el, $refs.panel))',
                            ])
                            ->suffix(new HtmlString('
                                <button type="button" tabindex="0" aria-label="Buka kalender" class="videotron-calendar-trigger-btn me-1"
                                    x-on:click.stop="window.toggleVideotronCalendar &amp;&amp; window.toggleVideotronCalendar($el)"
                                    x-on:keydown.enter.stop.prevent="$el.click()"
                                    x-on:keydown.space.stop.prevent="$el.click()">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </button>
                            '), isInline: true)
                            ->visible(fn (Forms\Get $get): bool => in_array($get('condition'), ['gangguan', 'dalam_perbaikan', 'nonaktif'])),
                        Forms\Components\Textarea::make('issue_notes')
                            ->label(fn (Forms\Get $get): string => match ($get('condition')) {
                                'nonaktif' => 'Alasan Dinonaktifkan',
                                default => 'Catatan Kendala (opsional)',
                            })
                            ->markAsRequired(fn (Forms\Get $get): bool => $get('condition') === 'nonaktif')
                            ->rules(fn (Forms\Get $get): array => $get('condition') === 'nonaktif' ? ['required'] : [])
                            ->validationMessages([
                                'required' => 'Alasan dinonaktifkan wajib diisi.',
                                'max' => 'Catatan maksimal 500 karakter.',
                            ])
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? trim($state) : null)
                            ->rows(3)
                            ->maxLength(500)
                            ->default(fn (Videotron $record): ?string => $record->issue_notes)
                            ->placeholder(fn (Forms\Get $get): string => match ($get('condition')) {
                                'gangguan' => 'Jelaskan modul yang rusak atau kendala fisik / sistem...',
                                'dalam_perbaikan' => 'Jelaskan komponen yang diservis atau progres teknisi...',
                                'nonaktif' => 'Contoh: dimatikan saat libur semester',
                                default => 'Tuliskan catatan...',
                            })
                            ->helperText(function (Forms\Components\Textarea $component, Forms\Get $get): HtmlString {
                                $condition = $get('condition');
                                $hint = match ($condition) {
                                    'dalam_perbaikan' => 'Jika dikosongkan, pemohon otomatis melihat: <em>"Sedang dalam pemeliharaan teknisi."</em>',
                                    'gangguan' => 'Jika dikosongkan, pemohon otomatis melihat: <em>"Ada kendala teknis pada perangkat ini."</em>',
                                    'nonaktif' => 'Alasan wajib diisi untuk arsip operasional.',
                                    default => 'Penjelasan status operasional.',
                                };

                                return new HtmlString('
                                    <div class="videotron-notes-footer" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-top: 0.25rem;">
                                        <span class="videotron-notes-hint" style="font-size: 0.75rem; color: #64748B; line-height: 1.4;">'.$hint.'</span>
                                        <span class="videotron-char-counter" style="font-size: 0.75rem; color: #64748B; white-space: nowrap; font-variant-numeric: tabular-nums;" x-text="String($wire.get(\''.e($component->getStatePath()).'\') ?? \'\').length + \'/500\'">0/500</span>
                                    </div>
                                ');
                            })
                            ->visible(fn (Forms\Get $get): bool => in_array($get('condition'), ['gangguan', 'dalam_perbaikan', 'nonaktif'])),
                    ])
                    ->action(function (Videotron $record, array $data): void {
                        $selected = $data['condition'];
                        $oldCondition = $record->condition;
                        $oldIsActive = (bool) $record->is_active;

                        if ($selected === 'normal') {
                            $record->condition = 'normal';
                            $record->is_active = true;
                            $record->issue_notes = null;
                            $record->estimated_recovery_date = null;
                        } elseif ($selected === 'nonaktif') {
                            $record->condition = 'normal';
                            $record->is_active = false;
                            $record->issue_notes = $data['issue_notes'] ?? null;
                            $record->estimated_recovery_date = $data['estimated_recovery_date'] ?? null;
                        } else {
                            $record->condition = $selected;
                            $record->is_active = false;
                            $record->issue_notes = $data['issue_notes'] ?? null;
                            $record->estimated_recovery_date = $data['estimated_recovery_date'] ?? null;
                        }
                        $record->save();

                        $notifCount = 0;
                        $toastBody = 'Kondisi '.$record->name.' telah diperbarui.';

                        if (in_array($selected, ['gangguan', 'dalam_perbaikan'])) {
                            $notifCount = $record->notifyAffectedSubmissions('disruption');
                            if ($notifCount > 0) {
                                $toastBody = 'Kondisi '.$record->name.' diperbarui. Notifikasi otomatis dikirim ke '.$notifCount.' pengaju terdampak.';
                            }
                        } elseif ($selected === 'normal' && (in_array($oldCondition, ['gangguan', 'dalam_perbaikan']) || ! $oldIsActive)) {
                            $notifCount = $record->notifyAffectedSubmissions('recovered');
                            if ($notifCount > 0) {
                                $toastBody = 'Kondisi '.$record->name.' kembali Normal. Notifikasi pemulihan otomatis dikirim ke '.$notifCount.' pengaju.';
                            }
                        }

                        Notification::make()
                            ->title('Status Berhasil Diperbarui')
                            ->body($toastBody)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->label('Ubah Data')
                    ->icon('heroicon-m-pencil-square')
                    ->button()
                    ->size('xs')
                    ->color('gray')
                    ->slideOver()
                    ->mutateRecordDataUsing(function (array $data, Videotron $record): array {
                        if (! $record->hasCustomImage()) {
                            $data['image'] = null;
                        }

                        return $data;
                    })
                    ->modalIcon('heroicon-o-pencil-square')
                    ->modalIconColor('primary')
                    ->modalAlignment(Alignment::Left)
                    ->modalHeading('Ubah Data Videotron')
                    ->modalDescription(fn (Videotron $record): HtmlString => new HtmlString(
                        '<div class="videotron-modal-desc-row">'
                        .'<span class="videotron-modal-desc-name">'.e($record->name).'</span>'
                        .'<span class="videotron-modal-desc-badge">'
                        .'<svg class="videotron-modal-desc-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>'
                        .e($record->location)
                        .'</span>'
                        .'</div>'
                    ))
                    ->modalSubmitAction(fn (StaticAction $action) => $action
                        ->label(new HtmlString('<span wire:loading.remove wire:target="callMountedTableAction">Simpan Perubahan</span><span wire:loading wire:target="callMountedTableAction">Menyimpan...</span>'))
                        ->color('primary')
                    )
                    ->modalCancelAction(fn (StaticAction $action) => $action
                        ->label('Batal')
                        ->color('gray')
                    )
                    ->modalAutofocus(false)
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->extraModalWindowAttributes([
                        'class' => 'modal-ubah-data-videotron',
                        'tabindex' => '-1',
                    ]),
            ])
            ->bulkActions([])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([5, 10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideotrons::route('/'),
        ];
    }
}
