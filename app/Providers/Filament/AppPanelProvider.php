<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\ViewBook;
use App\Filament\Pages\Checkout;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\CategoryBooks;
use App\Filament\Pages\AdminDashboard;
use App\Services\BookInteractionService;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\SetPanelColors;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;


class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->emailverification()
            ->profile()
            ->default()
            ->sidebarWidth('12rem')
            // ->colors($this->getPanelColors())

            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Blue, 
            ])
            ->brandLogo(asset('/booke.png'))
            ->favicon(asset('/logo.png'))
            ->brandLogoHeight('4rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                // Pages\Dashboard::class,
                ViewBook::class,
                Checkout::class,
                CategoryBooks::class,
                AdminDashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\RedirectToAppLogin::class,
                SetPanelColors::class,

            ])
            ->authMiddleware([
                Authenticate::class,
            ])
                        ->renderHook(
                'panels::body.end',
                fn (): View => view('filament.custom.footer')
            );
    }
    // protected function getPanelColors(): array
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return [
    //             'primary' => Color::Amber,
    //         ];
    //     }

    //     if ($user->hasRole('admin')) {
    //         return [
    //             'primary' => Color::Red,
    //         ];
    //     } elseif ($user->hasRole('manager')) {
    //         return [
    //             'primary' => Color::Blue,
    //         ];
    //     } else {
    //         return [
    //             'primary' => Color::Green,
    //         ];
    //     }
    // }
}
