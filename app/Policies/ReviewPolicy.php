<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;


class ReviewPolicy {

    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Anyone can view the list of reviews
    }

    public function view(User $user, Review $review): bool
    {
        return true; // Anyone can view individual reviews
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a review
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id; // Only the review author can update
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id; // Only the review author can delete
    }
}
