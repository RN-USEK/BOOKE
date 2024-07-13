<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Book;
use App\Models\Order;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Clients', User::count())
                ->description('Total number of registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Books', Book::count())
                ->description('Total number of books in inventory')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),
            Stat::make('Total Orders', Order::count())
                ->description('Total number of orders placed')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
            Stat::make('Total Revenue', $this->getTotalRevenue())
                ->description('Total cash in from orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }

    private function getTotalRevenue(): string
    {
        $totalRevenue = Order::sum('total_amount');
        return '$' . number_format($totalRevenue, 2);
    }
}