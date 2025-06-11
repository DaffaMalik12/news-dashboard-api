<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataStatistikResource\Pages;
use App\Filament\Resources\DataStatistikResource\RelationManagers;
use App\Models\DataStatistik;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\TextInput;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\FontWeight;

class DataStatistikResource extends Resource
{
    protected static ?string $model = DataStatistik::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Data Statistik';

    protected static ?string $modelLabel = 'Data Statistik';

    protected static ?string $pluralModelLabel = 'Data Statistik';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 3;

    // Menampilkan total record di navigation badge
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Data Statistik')
                    ->description('Kelola data statistik dan angka-angka penting')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_data')
                                    ->label('Nama Data')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Jumlah Siswa, Total Guru, dll')
                                    ->helperText('Masukkan nama atau jenis data statistik')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah/Nilai')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1)
                                    ->placeholder('0')
                                    ->helperText('Masukkan nilai numerik')
                                    ->suffixIcon('heroicon-o-calculator')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_data')
                    ->label('Nama Data')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-o-tag')
                    ->wrap(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah/Nilai')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->color('success')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn(string $state): string => number_format((float)$state))
                    ->icon('heroicon-o-calculator'),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->button(),
                Tables\Actions\EditAction::make()
                    ->color('warning')
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('update_all')
                        ->label('Update Semua')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->form([
                            Forms\Components\TextInput::make('jumlah_baru')
                                ->label('Nilai Baru')
                                ->numeric()
                                ->required()
                                ->helperText('Nilai ini akan diterapkan ke semua data yang dipilih'),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['jumlah' => $data['jumlah_baru']]);
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
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
            'index' => Pages\ListDataStatistiks::route('/'),
            'create' => Pages\CreateDataStatistik::route('/create'),
            'edit' => Pages\EditDataStatistik::route('/{record}/edit'),
        ];
    }
}
