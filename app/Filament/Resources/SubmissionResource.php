<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use App\Services\SubmissionDecisionService;
use DomainException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?string $breadcrumb = 'Pengajuan';

    protected static ?string $modelLabel = 'Pengajuan';

    protected static ?string $pluralModelLabel = 'Pengajuan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = Submission::getDisplayStatusCounts()['menunggu'] ?? 0;

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tiket & Pengaju')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('ticket_code')
                            ->label('Kode Tiket')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('Pengaju (Akun)')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pengajuan')
                            ->options([
                                'menunggu' => 'Menunggu',
                                'revisi' => 'Revisi',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'dibatalkan' => 'Dibatalkan',
                                'selesai' => 'Selesai',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Konten & Materi Penayangan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kegiatan')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('content_type')
                            ->label('Jenis Konten')
                            ->options([
                                'poster' => 'Poster / Gambar',
                                'video' => 'Video',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('video_duration_seconds')
                            ->label('Durasi Video (Detik)')
                            ->numeric(),
                        Forms\Components\TextInput::make('pic_name')
                            ->label('Penanggung Jawab (PIC)')
                            ->required(),
                        Forms\Components\TextInput::make('organization')
                            ->label('Organisasi / Unit'),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('No. WhatsApp PIC')
                            ->required(),
                        Forms\Components\TextInput::make('gdrive_link')
                            ->label('Tautan Google Drive')
                            ->url()
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('openLink')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->tooltip('Buka link Google Drive')
                                    ->url(fn ($record) => $record?->gdrive_link, shouldOpenInNewTab: true)
                            )
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Konten')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Jadwal & Target Videotron')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai Tayang')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai Tayang')
                            ->required(),
                        Forms\Components\CheckboxList::make('videotrons')
                            ->label('Pilihan Perangkat Videotron')
                            ->relationship('videotrons', 'name')
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Catatan Admin E-VITRON')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Keputusan / Alasan Revisi / Alasan Tolak')
                            ->placeholder('Tuliskan catatan jelas untuk pengaju bila meminta revisi atau menolak...'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pengaju & Tiket')
                    ->icon('heroicon-m-identification')
                    ->columns(4)
                    ->schema([
                        Infolists\Components\TextEntry::make('ticket_code')
                            ->label('Kode Tiket')
                            ->weight('bold')
                            ->copyable()
                            ->tooltip('Klik untuk menyalin kode tiket'),

                        Infolists\Components\TextEntry::make('display_status')
                            ->label('Status Pengajuan')
                            ->badge()
                            ->formatStateUsing(fn ($state) => Submission::getStatusLabel((string) $state, 'admin'))
                            ->color(fn (string $state): string => match ($state) {
                                'menunggu' => 'warning',
                                'revisi' => 'info',
                                'terjadwal' => 'teal',
                                'tayang' => 'success',
                                'selesai' => 'indigo',
                                'ditolak' => 'danger',
                                'dibatalkan' => 'gray',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('d M Y, H:i')
                            ->suffix(' WIB'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Akun Pengaju')
                            ->helperText(fn (Submission $record) => $record->user?->email),

                        Infolists\Components\TextEntry::make('pic_name')
                            ->label('Penanggung Jawab (PIC)'),

                        Infolists\Components\TextEntry::make('organization')
                            ->label('Organisasi / Unit')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('whatsapp')
                            ->label('No. WhatsApp')
                            ->url(fn (Submission $record) => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $record->whatsapp), shouldOpenInNewTab: true)
                            ->copyable(),

                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Catatan Admin Terakhir')
                            ->placeholder('Tidak ada catatan khusus.')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Konten & Materi Penayangan')
                    ->icon('heroicon-m-film')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Judul Konten Kegiatan')
                            ->weight('bold')
                            ->columnSpan(2),

                        Infolists\Components\TextEntry::make('content_type')
                            ->label('Jenis Konten')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'video' => 'Video',
                                'poster' => 'Poster / Gambar',
                                default => ucfirst($state),
                            })
                            ->color(fn (string $state) => match ($state) {
                                'video' => 'info',
                                'poster' => 'success',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('video_duration_seconds')
                            ->label('Durasi Video')
                            ->formatStateUsing(fn ($state) => $state ? "{$state} Detik" : 'Tidak Berlaku (Poster)')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('gdrive_link')
                            ->label('Tautan Materi Google Drive')
                            ->url(fn (Submission $record) => $record->gdrive_link, shouldOpenInNewTab: true)
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->columnSpan(2),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi Konten')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Jadwal & Target Perangkat Videotron')
                    ->icon('heroicon-m-calendar-days')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('Rentang Penayangan')
                            ->formatStateUsing(fn (Submission $record) => $record->start_date?->format('d F Y').' s/d '.$record->end_date?->format('d F Y')),

                        Infolists\Components\TextEntry::make('videotrons.name')
                            ->label('Perangkat Videotron Terpilih')
                            ->badge()
                            ->separator(', '),
                    ]),

                Infolists\Components\Section::make('Riwayat Status & Catatan Keputusan')
                    ->icon('heroicon-m-clock')
                    ->schema([
                        Infolists\Components\ViewEntry::make('history_timeline')
                            ->label('')
                            ->view('filament.infolists.submission-history'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->tooltip('Klik untuk menyalin tiket'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Konten')
                    ->searchable()
                    ->limit(28)
                    ->tooltip(fn (Submission $record): string => $record->title),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengaju')
                    ->description(fn (Submission $record): string => $record->pic_name.($record->organization ? ' ('.$record->organization.')' : ''))
                    ->searchable(['users.name', 'users.email', 'pic_name', 'organization'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('content_type')
                    ->label('Jenis Konten')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'video' => 'Video',
                        'poster' => 'Poster',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'info',
                        'poster' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('videotrons.name')
                    ->label('Perangkat Dipilih')
                    ->badge()
                    ->separator(', '),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime('d M Y')
                    ->description(fn (Submission $record): string => $record->created_at->format('H:i').' WIB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Rentang Penayangan')
                    ->formatStateUsing(fn (Submission $record): string => $record->start_date?->format('d M').' - '.$record->end_date?->format('d M Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Submission::getStatusLabel($state, 'admin'))
                    ->color(fn (string $state): string => Submission::getStatusColor($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pengajuan')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'revisi' => 'Revisi',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),

                Tables\Filters\Filter::make('tanggal_pengajuan')
                    ->label('Rentang Tanggal Pengajuan')
                    ->form([
                        Forms\Components\DatePicker::make('diajukan_dari')->label('Diajukan Dari'),
                        Forms\Components\DatePicker::make('diajukan_sampai')->label('Diajukan Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['diajukan_dari'], fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date))
                            ->when($data['diajukan_sampai'], fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date));
                    }),

                Tables\Filters\Filter::make('rentang_penayangan')
                    ->label('Rentang Tanggal Penayangan')
                    ->form([
                        Forms\Components\DatePicker::make('mulai_tayang')->label('Mulai Tayang Dari'),
                        Forms\Components\DatePicker::make('selesai_tayang')->label('Selesai Tayang Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['mulai_tayang'], fn (Builder $q, $date): Builder => $q->whereDate('start_date', '>=', $date))
                            ->when($data['selesai_tayang'], fn (Builder $q, $date): Builder => $q->whereDate('end_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('content_type')
                    ->label('Jenis Konten')
                    ->options([
                        'poster' => 'Poster / Gambar',
                        'video' => 'Video',
                    ]),

                Tables\Filters\SelectFilter::make('videotrons')
                    ->label('Pilihan Videotron')
                    ->relationship('videotrons', 'name'),
            ])
            ->actions([
                // Tindakan Utama: Membuka Detail Pengajuan
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->button()
                    ->size('xs')
                    ->color('primary'),

                // Menu Dropdown Aksi Keputusan & Cepat
                Tables\Actions\ActionGroup::make([
                    // Aksi Cepat: Buka Link GDrive
                    Tables\Actions\Action::make('open_gdrive')
                        ->label('Buka Materi GDrive')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (Submission $record) => $record->gdrive_link, shouldOpenInNewTab: true),

                    // Tindakan 1: Setujui Pengajuan (dengan backend validation)
                    Tables\Actions\Action::make('approve')
                        ->label('Setujui Pengajuan')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn (Submission $record): bool => in_array($record->status, ['menunggu', 'revisi'], true))
                        ->form([
                            Forms\Components\Placeholder::make('verify_info')
                                ->label('Verifikasi Sebelum Persetujuan')
                                ->content(function (Submission $record): string {
                                    $layar = $record->videotrons->pluck('name')->join(', ');
                                    $tgl = $record->start_date?->format('d M').' - '.$record->end_date?->format('d M Y');

                                    return "Target: {$layar} | Jadwal: {$tgl}. Sistem backend akan memastikan kesiapan perangkat dan benturan jadwal secara otomatis.";
                                }),
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Catatan Persetujuan (Opsional)')
                                ->placeholder('Tuliskan catatan khusus persetujuan atau kosongkan...'),
                        ])
                        ->action(function (Submission $record, array $data) {
                            try {
                                app(SubmissionDecisionService::class)->approve($record, $data['admin_notes'] ?? null);

                                Notification::make()
                                    ->title('Pengajuan Berhasil Disetujui!')
                                    ->body("Tiket {$record->ticket_code} disetujui untuk penayangan.")
                                    ->success()
                                    ->send();
                            } catch (DomainException $e) {
                                Notification::make()
                                    ->title('Persetujuan Ditolak Sistem')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }),

                    // Tindakan 2: Minta Revisi (dengan catatan wajib)
                    Tables\Actions\Action::make('request_revision')
                        ->label('Minta Revisi')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->visible(fn (Submission $record): bool => $record->status === 'menunggu')
                        ->form([
                            Forms\Components\Textarea::make('admin_feedback')
                                ->label('Catatan Revisi untuk Pengaju')
                                ->required()
                                ->minLength(5)
                                ->placeholder('Jelaskan dengan jelas bagian materi atau data yang harus diperbaiki oleh pengaju...'),
                        ])
                        ->action(function (Submission $record, array $data) {
                            try {
                                app(SubmissionDecisionService::class)->requestRevision($record, $data['admin_feedback']);

                                Notification::make()
                                    ->title('Permintaan Revisi Telah Dikirim')
                                    ->body("Catatan revisi untuk tiket {$record->ticket_code} berhasil dikirim ke pengaju.")
                                    ->warning()
                                    ->send();
                            } catch (DomainException $e) {
                                Notification::make()
                                    ->title('Gagal Meminta Revisi')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Tindakan 3: Tolak Pengajuan (dengan alasan wajib)
                    Tables\Actions\Action::make('reject')
                        ->label('Tolak Pengajuan')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->visible(fn (Submission $record): bool => in_array($record->status, ['menunggu', 'revisi'], true))
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->minLength(5)
                                ->placeholder('Tuliskan alasan penolakan secara jelas dan transparan...'),
                        ])
                        ->action(function (Submission $record, array $data) {
                            try {
                                app(SubmissionDecisionService::class)->reject($record, $data['admin_notes']);

                                Notification::make()
                                    ->title('Pengajuan Ditolak')
                                    ->body("Alasan penolakan tiket {$record->ticket_code} telah disimpan dan diberitahukan ke pengaju.")
                                    ->danger()
                                    ->send();
                            } catch (DomainException $e) {
                                Notification::make()
                                    ->title('Gagal Menolak Pengajuan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                ])
                    ->label('Tindakan')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('xs')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['videotrons', 'user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
        ];
    }
}
