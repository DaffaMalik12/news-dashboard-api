<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use App\Filament\Resources\AgendaResource\RelationManagers;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Carbon\Carbon;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $modelLabel = 'Agenda';

    protected static ?string $pluralModelLabel = 'Agenda';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 2;

    // Tambahkan properti ini untuk menampilkan jumlah record di navigasi sidebar
    protected static ?string $navigationBadge = null; // Bisa juga null, ini akan secara default menampilkan count()



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Agenda')
                    ->description('Masukkan detail lengkap agenda kegiatan')
                    ->schema([
                        Forms\Components\TextInput::make('judul_agenda')
                            ->label('Judul Agenda')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Rapat Koordinasi Tim')
                            ->columnSpanFull()
                            ->helperText('Masukkan judul agenda yang jelas dan deskriptif'),
                    ])
                    ->collapsible(),

                Section::make('Jadwal & Lokasi')
                    ->description('Tentukan waktu dan tempat pelaksanaan agenda')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_agenda')
                            ->label('Tanggal Agenda')
                            ->required()
                            ->default(today())
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->native(false)
                            ->closeOnDateSelection(true)
                            ->helperText('Pilih tanggal pelaksanaan agenda'),

                        Forms\Components\TimePicker::make('waktu')
                            ->label('Waktu Pelaksanaan')
                            ->required()
                            ->seconds(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->placeholder('Contoh: 09:00')
                            ->helperText('Format 24 jam (contoh: 14:30)'),

                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Ruang Rapat Lantai 2')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->helperText('Sebutkan lokasi lengkap pelaksanaan agenda'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Informasi Tambahan')
                    ->description('Detail tambahan untuk agenda (opsional)')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Agenda')
                            ->placeholder('Tambahkan deskripsi atau catatan khusus untuk agenda ini...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status Agenda')
                            ->options([
                                'terjadwal' => 'Terjadwal',
                                'berlangsung' => 'Sedang Berlangsung',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('terjadwal')
                            ->native(false),

                        Forms\Components\Select::make('prioritas')
                            ->label('Tingkat Prioritas')
                            ->options([
                                'rendah' => 'Rendah',
                                'sedang' => 'Sedang',
                                'tinggi' => 'Tinggi',
                                'urgent' => 'Urgent',
                            ])
                            ->default('sedang')
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_agenda')
                    ->label('Judul Agenda')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40)
                    ->weight('medium')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('tanggal_agenda')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(function (string $state): string {
                        $date = Carbon::parse($state);
                        $now = now();

                        if ($date->isToday()) {
                            return 'warning';
                        } elseif ($date->isFuture()) {
                            return 'success';
                        } else {
                            return 'gray';
                        }
                    })
                    ->icon(function (string $state): string {
                        $date = Carbon::parse($state);

                        if ($date->isToday()) {
                            return 'heroicon-o-clock';
                        } elseif ($date->isFuture()) {
                            return 'heroicon-o-calendar';
                        } else {
                            return 'heroicon-o-check-circle';
                        }
                    }),

                Tables\Columns\TextColumn::make('waktu')
                    ->label('Waktu')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->icon('heroicon-o-map-pin')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),






            ])



            ->defaultSort('tanggal_agenda', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereDate('tanggal_agenda', today())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Agenda hari ini';
    }
}
