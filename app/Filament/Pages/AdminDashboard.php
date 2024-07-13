<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\BooksByCategoryChart;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string $view = 'filament.pages.admin-dashboard';

    public static function getColor(): ?string
    {
        return 'danger'; // You can change this to any color you prefer
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            OrdersChart::class,
            RevenueChart::class,
            BooksByCategoryChart::class,
        ];
    }
}