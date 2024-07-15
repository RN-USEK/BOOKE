<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookPriceRangeChart extends ChartWidget
{
    protected static ?string $heading = 'Books by Price Range';

    protected function getData(): array
    {
        $data = $this->getBookPriceRangeData();

        return [
            'datasets' => [
                [
                    'label' => 'Books',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $this->getColors(count($data)),
                ],
            ],
            'labels' => $data->pluck('range')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    private function getBookPriceRangeData()
    {
        return DB::table('books')
            ->select(DB::raw('
                CASE
                    WHEN price < 10 THEN "Under $10"
                    WHEN price BETWEEN 10 AND 19.99 THEN "$10 - $19.99"
                    WHEN price BETWEEN 20 AND 29.99 THEN "$20 - $29.99"
                    WHEN price BETWEEN 30 AND 39.99 THEN "$30 - $39.99"
                    ELSE "$40 and above"
                END AS `range`,
                COUNT(*) as count
            '))
            ->groupBy('range')
            ->orderBy('range')
            ->get();
    }

    private function getColors(int $count): array
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#C9CBCF', '#FF9F80', '#36A1EB', '#FFCF56',
        ];
        
        return array_slice($colors, 0, $count);
    }
}