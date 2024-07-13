<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Cumulative Revenue by Order';

    protected function getData(): array
    {
        $data = $this->getRevenueData();

        $cumulativeRevenue = $this->calculateCumulativeRevenue($data);

        return [
            'datasets' => [
                [
                    'label' => 'Cumulative Revenue',
                    'data' => $cumulativeRevenue,
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => range(1, count($cumulativeRevenue)),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getRevenueData()
    {
        return Order::select('id', 'total_amount')
            ->orderBy('created_at')
            ->get();
    }

    private function calculateCumulativeRevenue($orders)
    {
        $cumulative = [];
        $total = 0;

        foreach ($orders as $order) {
            $total += $order->total_amount;
            $cumulative[] = $total;
        }

        return $cumulative;
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => '(value) => "€" + value.toLocaleString()',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Order Number',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => '(context) => "Revenue: €" + context.parsed.y.toLocaleString()',
                    ],
                ],
            ],
        ];
    }
}