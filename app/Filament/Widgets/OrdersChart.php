<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Order Amounts Over Time';

    protected function getData(): array
    {
        $data = $this->getOrdersData();

        return [
            'datasets' => [
                [
                    'label' => 'Order Amount',
                    'data' => $data->pluck('total_amount')->toArray(),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('order_number')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getOrdersData()
    {
        return Order::select('id', 'total_amount', 'created_at')
            ->orderBy('created_at')
            ->get()
            ->map(function ($order, $index) {
                return [
                    'order_number' => $index + 1,
                    'total_amount' => $order->total_amount,
                ];
            });
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Amount Paid (€)',
                    ],
                    'ticks' => [
                        'callback' => '(value) => "€" + value.toLocaleString()',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Order Number (Chronological)',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => '(context) => "Amount: €" + context.parsed.y.toLocaleString()',
                    ],
                ],
            ],
        ];
    }
}