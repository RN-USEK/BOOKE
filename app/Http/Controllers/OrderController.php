<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session()->get('cart');
        
        if(!$cart) {
            return redirect()->route('cart')->with('error', 'Your cart is empty!');
        }

        $totalAmount = 0;
        foreach($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ]);

        foreach($cart as $id => $details) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price']
            ]);
        }

        // Clear the cart after successful checkout
        session()->forget('cart');

        return redirect()->route('order.confirmation', $order)->with('success', 'Order placed successfully!');
    }

    public function confirmation(Order $order)
    {
        return view('order.confirmation', compact('order'));
    }

    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return view('order.show', compact('order'));
    }
}