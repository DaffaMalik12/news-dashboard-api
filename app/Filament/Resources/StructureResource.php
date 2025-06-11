<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StructureResource\Pages;
use App\Filament\Resources\StructureResource\RelationManagers;
use App\Models\structure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class StructureResource extends Resource
{
    protected static ?string $model = Structure::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Struktur Organisasi';
    protected static ?string $pluralModelLabel = 'Struktur Organisasi';
    protected static ?string $modelLabel = 'Struktur Organisasi';

    protected static ?string $navigationGroup = 'Akademik';

    // ... sisanya kode Anda tetap sama ...

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Anggota Struktur Organisasi')
                    ->description('Lengkapi informasi anggota struktur organisasi')
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('gambar')
                                    ->label('Foto Anggota')
                                    ->disk('public')
                                    ->directory('structure-images')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('400')
                                    ->imageResizeTargetHeight('400')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('Upload foto dengan rasio 1:1. Maksimal 2MB'),

                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap')
                                    ->prefixIcon('heroicon-m-user'),

                                Forms\Components\TextInput::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan jabatan')
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->datalist([
                                        'Ketua',
                                        'Wakil Ketua',
                                        'Sekretaris',
                                        'Bendahara',
                                        'Koordinator',
                                        'Anggota'
                                    ]),

                                Forms\Components\Textarea::make('detail')
                                    ->label('Detail Jabatan')
                                    ->maxLength(500)
                                    ->placeholder('Masukkan detail jabatan atau tugas')
                                    ->helperText('Opsional, berikan penjelasan lebih lanjut tentang tugas atau tanggung jawab anggota ini.')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\ImageColumn::make('gambar')
                            ->label('Foto')
                            ->circular()
                            ->size(80)
                            ->defaultImageUrl(url('/images/default-avatar.png')),

                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('nama')
                                ->weight('bold')
                                ->size('lg')
                                ->color('primary'),

                            Tables\Columns\TextColumn::make('jabatan')
                                ->badge()
                                ->color(fn(string $state): string => match (true) {
                                    str_contains(strtolower($state), 'ketua') => 'success',
                                    str_contains(strtolower($state), 'sekretaris') => 'info',
                                    str_contains(strtolower($state), 'bendahara') => 'warning',
                                    str_contains(strtolower($state), 'koordinator') => 'primary',
                                    default => 'gray',
                                }),
                        ])->space(1),


                    ])->from('md'),
                ])->space(2)


            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Filter Jabatan')
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->before(function ($record) {
                            if ($record->gambar && Storage::disk('public')->exists($record->gambar)) {
                                Storage::disk('public')->delete($record->gambar);
                            }
                        }),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->gambar && Storage::disk('public')->exists($record->gambar)) {
                                    Storage::disk('public')->delete($record->gambar);
                                }
                            }
                        }),
                ])
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Anggota Pertama')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('Belum ada anggota struktur organisasi')
            ->emptyStateDescription('Mulai dengan menambahkan anggota struktur organisasi pertama.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped()
            ->defaultPaginationPageOption(12)
            ->paginationPageOptions([6, 12, 24])
            ->searchPlaceholder('Cari nama atau jabatan...');
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
            'index' => Pages\ListStructures::route('/'),
            'create' => Pages\CreateStructure::route('/create'),
            'edit' => Pages\EditStructure::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama', 'jabatan'];
    }
}
