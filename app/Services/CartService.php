<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    public static function add($book)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$book->id])) {
            $cart[$book->id]['quantity']++;
        } else {
            $cart[$book->id] = [
                "title" => $book->title,
                "quantity" => 1,
                "price" => $book->price,
                "cover_image" => $book->cover_image
            ];
        }
        
        Session::put('cart', $cart);
    }

    public static function remove($bookId)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            Session::put('cart', $cart);
        }
    }

    public static function update($bookId, $quantity)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] = $quantity;
            Session::put('cart', $cart);
        }
    }

    public static function clear()
    {
        Session::forget('cart');
    }

    public static function getContent()
    {
        return Session::get('cart', []);
    }

    public static function getTotal()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }
}