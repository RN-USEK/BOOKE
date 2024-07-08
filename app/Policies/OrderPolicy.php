<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see the orders list
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) || $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}