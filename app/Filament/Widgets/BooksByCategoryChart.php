<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BooksByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Books by Category';

    protected function getData(): array
    {
        $data = $this->getBooksByCategoryData();

        return [
            'datasets' => [
                [
                    'label' => 'Books',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $this->getColors($data->count()),
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    private function getBooksByCategoryData()
    {
        return Category::select('categories.name', DB::raw('COUNT(books.id) as count'))
            ->leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getColors(int $count): array
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#C9CBCF', '#FF9F80', '#36A1EB', '#FFCF56',
            '#4CC0C0', '#9967FF', '#FF9F41', '#C9CCCC', '#FF6484',
            '#36A3EB', '#FFCC56', '#4BC1C0', '#9968FF', '#FF9F42',
            '#C9CDEF', '#FF6584', '#36A4EB', '#FFCD56', '#4BC2C0',
            '#9969FF', '#FF9F43', '#C9CDDF', '#FF6684', '#36A5EB'
        ];
        
        return array_slice($colors, 0, $count);
    }
}