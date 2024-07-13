<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;

class SetPanelColors
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('admin')) {
            $colors = [
                'primary' => Color::Red,
                'secondary' => Color::Orange,
                'gray' => Color::Slate,
                'sidebar' => '#FFE4E1', // Misty Rose
                'header' => '#F8F0ED', // Lighter shade of Misty Rose
            ];
        } elseif ($user && $user->hasRole('manager')) {
            $colors = [
                'primary' => Color::Blue,
                'secondary' => Color::Indigo,
                'gray' => Color::Gray,
                'sidebar' => '#E6E6FA', // Lavender
                'header' => '#F0F0FF', // Lighter shade of Lavender
            ];
        } else {
            $colors = [
                'primary' => Color::Violet,
                'secondary' => Color::Emerald,
                'gray' => Color::Zinc,
                'sidebar' => 'thistle', // Honeydew
                'header' => 'antiquewhite', // Lighter shade of Honeydew
            ];
        }

        Filament::registerRenderHook(
            'panels::body.start',
            fn () => view('filament.custom-styles', ['colors' => $colors])
        );

        return $next($request);
    }
}