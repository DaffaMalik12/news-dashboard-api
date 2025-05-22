<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaResource\Pages;
use App\Models\Berita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class BeritaResource extends Resource
{
    protected static ?string $model = Berita::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = 'Berita';
    
    protected static ?string $pluralModelLabel = 'Berita';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Berita')
                    ->description('Informasi dasar tentang berita')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul Berita')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->helperText('URL slug akan otomatis dibuat dari judul')
                                    ->columnSpan(2),

                                Forms\Components\Select::make('kategori')
                                    ->label('Kategori')
                                    ->required()
                                    ->options([
                                        'Teknologi' => 'Teknologi',
                                        'Pendidikan' => 'Pendidikan',
                                        'Kesehatan' => 'Kesehatan',
                                        'Politik' => 'Politik',
                                        'Olahraga' => 'Olahraga',
                                        'Ekonomi' => 'Ekonomi',
                                        'Hiburan' => 'Hiburan',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('penulis')
                                    ->label('Penulis')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(auth()->user()->name ?? ''),
                            ]),

                        Forms\Components\DatePicker::make('tanggal_publish')
                            ->label('Tanggal Publish')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->helperText('Tanggal berita akan dipublikasikan'),
                    ]),

                Section::make('Konten Berita')
                    ->description('Isi dan media berita')
                    ->schema([
                        Forms\Components\RichEditor::make('isi')
                            ->label('Isi Berita')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),

                        Forms\Components\FileUpload::make('gambar')
                            ->label('Thumbnail Berita')
                            ->directory('berita-images')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->loadingIndicatorPosition('center')
                            ->panelLayout('integrated')
                            ->uploadButtonPosition('center')
                            ->uploadProgressIndicatorPosition('center')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload gambar dengan rasio 16:9, maksimal 2MB')
                            ->downloadable()
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar')
                ->label('Thumbnail')
                ->disk('public')
                ->height(50)
                ->width(80)
                ->extraAttributes(['class' => 'rounded-lg']),
            

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->weight('medium'),

                Tables\Columns\BadgeColumn::make('kategori')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'Teknologi',
                        'success' => 'Kesehatan',
                        'warning' => 'Pendidikan',
                        'danger' => 'Politik',
                        'secondary' => 'Olahraga',
                        'info' => 'Ekonomi',
                        'gray' => 'Lainnya',
                    ])
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('penulis')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('tanggal_publish')
                    ->label('Tanggal Publish')
                    ->date('d M Y')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Teknologi' => 'Teknologi',
                        'Pendidikan' => 'Pendidikan',
                        'Kesehatan' => 'Kesehatan',
                        'Politik' => 'Politik',
                        'Olahraga' => 'Olahraga',
                        'Ekonomi' => 'Ekonomi',
                        'Hiburan' => 'Hiburan',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->multiple()
                    ->preload(),

                Filter::make('tanggal_publish')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_publish', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_publish', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d M Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d M Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('tanggal_publish', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListBeritas::route('/'),
            'create' => Pages\CreateBerita::route('/create'),
            'edit' => Pages\EditBerita::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['judul', 'isi', 'penulis', 'kategori'];
    }
}