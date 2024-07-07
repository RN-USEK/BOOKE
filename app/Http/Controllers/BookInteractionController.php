<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookInteraction;
use Illuminate\Http\Request;

class BookInteractionController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'interaction_type' => 'required|in:view,purchase',
        ]);

        $interaction = $book->interactions()->create([
            'user_id' => auth()->id(),
            'interaction_type' => $validated['interaction_type'],
        ]);

        return response()->json($interaction, 201);
    }
}