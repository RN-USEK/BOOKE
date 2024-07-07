<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Auth\Access\HandlesAuthorization;

class WishlistPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Everyone can view the wishlist page
    }

    public function view(User $user, Wishlist $wishlist): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) || $user->id === $wishlist->user_id;
    }

    public function create(User $user): bool
    {
        return false; // Disable manual creation
    }

    public function update(User $user, Wishlist $wishlist): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) || $user->id === $wishlist->user_id;
    }

    public function delete(User $user, Wishlist $wishlist): bool
    {
        return $user->hasAnyRole(['admin', 'manager']) || $user->id === $wishlist->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function restore(User $user, Wishlist $wishlist): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function forceDelete(User $user, Wishlist $wishlist): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}