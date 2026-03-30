<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiCheckoutController extends Controller
{
    public function checkout(Request $request)
{
    $user = $request->user();

    if (! $user) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }

    $cart = Cart::with(['items.product'])
        ->where('user_id', $user->id)
        ->first();

    if (! $cart || $cart->items->isEmpty()) {
        return response()->json([
            'status'  => false,
            'message' => 'Your cart is empty.',
        ], 422);
    }

    $data = $request->validate([
        'name'           => ['required', 'string', 'max:191'],
        'email'          => ['required', 'email'],
        'phone'          => ['required', 'string', 'max:50'],
        'address'        => ['required', 'string', 'max:255'],
        'city'           => ['required', 'string', 'max:191'],
        'postal_code'    => ['required', 'string', 'max:50'],
        'country'        => ['required', 'string', 'max:191'],
        'payment_method' => ['required', 'in:cod'],
    ]);

    $items    = $cart->items;
    $subtotal = 0;

    foreach ($items as $item) {
        $subtotal += $item->price_at_added * $item->quantity;
    }

    $shipping = 0;
    $discount = 0;
    $total    = $subtotal + $shipping - $discount;

    $order = DB::transaction(function () use ($user, $cart, $items, $data, $subtotal, $shipping, $discount, $total) {

        $address = Address::create([
            'user_id'     => $user->id,
            'label'       => 'API Checkout',
            'full_name'   => $data['name'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
            'city'        => $data['city'],
            'country'     => $data['country'],
            'postal_code' => $data['postal_code'],
            'is_default'  => true,
        ]);

        $order = Order::create([
            'uuid'           => (string) Str::uuid(),
            'user_id'        => $user->id,
            'address_id'     => $address->id,
            'status'         => 'pending',
            'payment_method' => $data['payment_method'],
            'subtotal'       => $subtotal,
            'shipping'       => $shipping,
            'discount'       => $discount,
            'total'          => $total,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'name'       => $item->product->name ?? 'Unknown product',
                'price'      => $item->price_at_added,
                'quantity'   => $item->quantity,
                'total'      => $item->price_at_added * $item->quantity,
            ]);

            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $cart->items()->delete();

        return $order->load(['items', 'address']);
    });

    return response()->json([
        'status'  => true,
        'message' => 'Order created successfully.',
        'order'   => $order,
    ]);
}

}
