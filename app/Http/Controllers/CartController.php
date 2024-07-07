<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request, Book $book)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$book->id])) {
            $cart[$book->id]['quantity']++;
        } else {
            $cart[$book->id] = [
                "name" => $book->title,
                "quantity" => 1,
                "price" => $book->price,
                "image" => $book->cover_image
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Book added to cart successfully!');
    }

    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully');
        }
    }

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Book removed from cart successfully');
        }
    }

    public function viewCart()
    {
        return view('cart');
    }
}