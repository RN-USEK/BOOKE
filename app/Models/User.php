<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Log;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public static string $policyName = UserPolicy::class;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'manager']);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
        public function bookInteractions()
    {
        return $this->hasMany(BookInteraction::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }  
    public function purchasedBooks()
    {
        $query = Book::join('order_items', 'books.id', '=', 'order_items.book_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $this->id)
            // Temporarily remove the status condition
            // ->where('orders.status', 'completed')
            ->select('books.*', 'orders.status as order_status')
            ->distinct();
    
        // Log the SQL query
        Log::info('Purchased Books Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
    
        $results = $query->get();
    
        // Log the results including the order status
        Log::info('Purchased Books Results:', [
            'count' => $results->count(),
            'books' => $results->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'order_status' => $book->order_status
                ];
            })->toArray()
        ]);
    
        return $results;
    }
}
