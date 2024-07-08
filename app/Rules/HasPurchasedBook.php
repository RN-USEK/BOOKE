<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\OrderItem;

class HasPurchasedBook implements ValidationRule
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasPurchased = OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', $this->userId)->where('status', 'completed');
        })->where('book_id', $value)->exists();

        if (!$hasPurchased) {
            $fail('You can only review books you have purchased.');
        }
    }
}