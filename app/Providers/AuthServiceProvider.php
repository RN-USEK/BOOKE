<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Order;
use App\Policies\BookPolicy;
use App\Policies\UserPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\WishlistPolicy;
use App\Policies\OrderPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    
    protected $policies = [
        Book::class => BookPolicy::class,
        User::class => UserPolicy::class,
        Category::class => CategoryPolicy::class,
        Review::class => ReviewPolicy::class,
        Wishlist::class => WishlistPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // If you have any custom Gates, you can define them here
    }
}