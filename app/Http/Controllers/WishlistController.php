<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class WishlistController extends Controller
{
    public function add(Book $book)
    {
        auth()->user()->wishlist()->attach($book->id);
        return back()->with('success', 'Book added to wishlist');
    }

    public function remove(Book $book)
    {
        auth()->user()->wishlist()->detach($book->id);
        return back()->with('success', 'Book removed from wishlist');
    }

    public function index()
    {
        $wishlist = auth()->user()->wishlist;
        return view('wishlist.index', compact('wishlist'));
    }
}
