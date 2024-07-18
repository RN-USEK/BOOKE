<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BookInteractionService;
class Checkout extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.checkout';

    protected static bool $shouldRegisterNavigation = false;


    public $shippingAddress;
    public $paymentMethod;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('shippingAddress')
                    ->required()
                    ->label('Shipping Address'),
                Select::make('paymentMethod')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                        'bank_transfer' => 'Bank Transfer',
                    ])
                    ->required()
                    ->label('Payment Method'),
            ]);
    }

    public function submit()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            Notification::make()
                ->title('Cart is empty')
                ->body('Please add items to your cart before checking out.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $this->getCartTotal(),
                'status' => 'placed',
                'shipping_address' => $data['shippingAddress'],
                'payment_method' => $data['paymentMethod'],
            ]);

            foreach ($cart as $bookId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $bookId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                app(BookInteractionService::class)->recordInteraction($bookId, 'purchase');

            }

            DB::commit();

            // Clear the cart
            Session::forget('cart');

            Notification::make()
                ->title('Order placed successfully')
                ->success()
                ->send();

            return redirect()->route('filament.app.pages.dashboard.index');

        } catch (\Exception $e) {
            DB::rollback();
        // Log the detailed error
            Log::error('Error placing order: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            Notification::make()
                ->title('Error placing order')
                ->body('An error occurred while placing your order. Please try again.')
                ->danger();
                // ->send();
        }
    }

    public function getCartContent()
    {
        $cart = Session::get('cart', []);
        
        // Fetch book details including cover image
        foreach ($cart as $bookId => &$item) {
            $book = \App\Models\Book::find($bookId);
            if ($book) {
                $item['cover_image'] = $book->cover_image;
            }
        }
        
        return $cart;
    }

    public function getCartTotal()
    {
        $cart = Session::get('cart', []);
        return array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
    }
}