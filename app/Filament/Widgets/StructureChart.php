<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class StructureChart extends ChartWidget
{
    protected static ?string $heading = 'Structure Members Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Structure Members',
                    'data' => [
                        ['x' => 1, 'y' => 10, 'r' => 12], // Ketua
                        ['x' => 2, 'y' => 9, 'r' => 10],  // Wakil
                        ['x' => 3, 'y' => 7, 'r' => 9],   // Sekretaris
                        ['x' => 4, 'y' => 7, 'r' => 9],   // Bendahara
                        ['x' => 5, 'y' => 5, 'r' => 7],   // Koordinator A
                        ['x' => 6, 'y' => 5, 'r' => 7],   // Koordinator B
                        ['x' => 7, 'y' => 3, 'r' => 5],   // Anggota 1
                        ['x' => 8, 'y' => 3, 'r' => 5],   // Anggota 2
                        ['x' => 9, 'y' => 3, 'r' => 5],   // Anggota 3
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
