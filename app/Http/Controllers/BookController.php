<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        $categories = Category::all();

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('isbn', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Sort functionality
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $books = $query->paginate(12)->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'books' => view('partials.book-grid', compact('books'))->render(),
                'pagination' => view('partials.pagination', compact('books'))->render(),
            ]);
        }

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $relatedBooks = Book::where('category_id', $book->category_id)
                            ->where('id', '!=', $book->id)
                            ->inRandomOrder()
                            ->limit(4)
                            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }
}