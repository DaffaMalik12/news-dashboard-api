<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisiMisiResource\Pages;
use App\Filament\Resources\VisiMisiResource\RelationManagers;
use App\Models\VisiMisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class VisiMisiResource extends Resource
{
    protected static ?string $model = VisiMisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Visi & Misi';

    protected static ?string $modelLabel = 'Visi & Misi';

    protected static ?string $pluralModelLabel = 'Visi & Misi';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Visi & Misi Organisasi')
                    ->description('Masukkan visi dan misi organisasi Anda')
                    ->icon('heroicon-o-light-bulb')
                    ->schema([
                        Forms\Components\Textarea::make('visi')
                            ->label('Visi')
                            ->placeholder('Masukkan visi organisasi...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Visi adalah gambaran masa depan yang ingin dicapai organisasi')
                            ->maxLength(1000),

                        Forms\Components\RichEditor::make('misi')
                            ->label('Misi')
                            ->placeholder('Masukkan misi organisasi...')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Misi adalah langkah-langkah strategis untuk mencapai visi')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'undo',
                                'redo',
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('visi')
                    ->label('Visi')
                    ->limit(100)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap()
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-o-eye')
                    ->searchable(),

                Tables\Columns\TextColumn::make('misi')
                    ->label('Misi')
                    ->html()
                    ->limit(150)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = strip_tags($column->getState());
                        if (strlen($state) <= 150) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap()
                    ->icon('heroicon-o-clipboard-document-list')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dibuat dari'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Dibuat sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dibuat dari ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Dibuat sampai ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalWidth('4xl'),
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('4xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->modalWidth('4xl'),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListVisiMisis::route('/'),
            'create' => Pages\CreateVisiMisi::route('/create'),
            'edit' => Pages\EditVisiMisi::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        // Mengubah warna badge navigation dari hijau (success) menjadi orange (warning)
        return static::getModel()::count() > 0 ? 'warning' : 'gray';
    }
}
