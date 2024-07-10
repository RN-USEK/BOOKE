<?php

namespace App\Services;

use App\Models\BookInteraction;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class BookInteractionService
{
    const INTERACTION_SCORES = [
        'view' => 1,
        'wishlist' => 2,
        'purchase' => 2,
    ];

    public function recordInteraction($bookId, $interactionType)
    {
        $userId = Auth::id();
        $score = self::INTERACTION_SCORES[$interactionType] ?? 0;

        BookInteraction::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $bookId, 'interaction_type' => $interactionType],
            ['score' => $score]
        );
    }

    public function recordReview($bookId, $rating)
    {
        $userId = Auth::id();
        // Convert 1-5 rating to -2 to +2 score
        $score = $rating - 3;

        BookInteraction::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $bookId, 'interaction_type' => 'review'],
            ['score' => $score]
        );
    }

    public function calculateBookScore($bookId)
    {
        $userId = Auth::id();
        return BookInteraction::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->sum('score');
    }
}