<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\BooksByCategoryChart;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.admin-dashboard';

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