<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\StockReservation;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->with('error', 'Please login to place an order 🌸');
        }

        $cart = $this->getCurrentCart($request);

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()
                ->route('front.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $items = $cart->items()
            ->with(['product.images', 'product.category'])
            ->get();

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item->price_at_added * $item->quantity;
        }

        $shipping = 0;
        $discount = 0;
        $total    = $subtotal + $shipping - $discount;

        $address = Address::where('user_id', Auth::id())
            ->where('is_default', true)
            ->first();

        $user = Auth::user();

        return view('Front.checkout.index', [
            'items'    => $items,
            'address'  => $address,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total'    => $total,
            'user'     => $user,
        ]);
    }

    public function process(Request $request)
    {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->with('error', 'Please login to place an order 🌸');
        }

        $cart = $this->getCurrentCart($request);

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()
                ->route('front.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // ✅ Validation (نفس الشروط المتفق عليها) + bail (خطأ واحد لكل حقل)
        $data = $request->validate([
            'full_name'     => ['bail', 'required', 'string', 'min:3', 'regex:/^\s*\S+\s+\S+.*$/'],
            'email'         => ['bail', 'required', 'email'],
            'phone'         => ['bail', 'required', 'digits_between:8,15'],
            'address'       => ['bail', 'required', 'string', 'min:5'],
            'city'          => ['bail', 'required', 'string', 'min:2'],
            'country'       => ['bail', 'required', 'string', 'min:2'],
            'postal_code'   => ['bail', 'required', 'digits:3'],
            'payment_method'=> ['bail', 'required', 'in:cod,stripe'],
        ]);

        $user = Auth::user();

        $items = $cart->items()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()
                ->route('front.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        foreach ($items as $item) {
            $product = $item->product;
            $stock = (int) ($product->stock ?? 0);

            if (!$product || $stock < $item->quantity) {
                return redirect()
                    ->route('front.cart.index')
                    ->with('error', 'One of the products is no longer available in the requested quantity. Please review your cart.');
            }
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item->price_at_added * $item->quantity;
        }

        $shipping = 0;
        $discount = 0;
        $total    = $subtotal + $shipping - $discount;

        $address = $user->addresses()
            ->where('is_default', true)
            ->first();

        if ($address) {
            $address->fill([
                'full_name'   => $data['full_name'],
                'phone'       => $data['phone'],
                'country'     => $data['country'],
                'city'        => $data['city'],
                'address'     => $data['address'],
                'postal_code' => $data['postal_code'] ?? null,
            ])->save();
        } else {
            $address = Address::create([
                'user_id'     => $user->id,
                'label'       => 'Default',
                'full_name'   => $data['full_name'],
                'phone'       => $data['phone'],
                'country'     => $data['country'],
                'city'        => $data['city'],
                'address'     => $data['address'],
                'postal_code' => $data['postal_code'] ?? null,
                'is_default'  => true,
            ]);
        }

        if (empty($user->phone)) {
            $user->phone = $data['phone'];
            $user->save();
        }

        $order = Order::create([
            'uuid'           => (string) Str::uuid(),
            'user_id'        => $user->id,
            'address_id'     => $address->id,
            'coupon_id'      => null,
            'subtotal'       => $subtotal,
            'shipping'       => $shipping,
            'discount'       => $discount,
            'total'          => $total,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'notes'          => null,
        ]);

        try {
            DB::transaction(function () use ($items, $order) {

                $expiresAt = now()->addMinutes(15);

                foreach ($items as $item) {
                    $productId = (int) $item->product_id;
                    $qtyNeeded = (int) $item->quantity;

                    $product = Product::where('id', $productId)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \Exception('A product in your cart no longer exists.');
                    }

                    $stock     = (int) ($product->stock ?? 0);
                    $reserved  = (int) ($product->reserved_stock ?? 0);
                    $available = $stock - $reserved;

                    if ($available < $qtyNeeded) {
                        throw new \Exception("Not enough stock for: {$product->name}");
                    }

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $productId,
                        'name'       => $product->name ?? 'Unknown product',
                        'sku'        => $product->sku ?? null,
                        'quantity'   => $qtyNeeded,
                        'price'      => $item->price_at_added,
                        'total'      => $item->price_at_added * $qtyNeeded,
                    ]);

                    StockReservation::updateOrCreate(
                        [
                            'order_id'   => $order->id,
                            'product_id' => $productId,
                        ],
                        [
                            'qty'        => $qtyNeeded,
                            'status'     => 'reserved',
                            'expires_at' => $expiresAt,
                        ]
                    );

                    $product->reserved_stock = $reserved + $qtyNeeded;
                    $product->save();
                }
            });
        } catch (\Throwable $e) {

            $order->delete();

            return redirect()
                ->route('front.cart.index')
                ->with('error', $e->getMessage() ?: 'Stock is not available. Please review your cart.');
        }

        if ($data['payment_method'] === 'cod') {

            Payment::create([
                'order_id'            => $order->id,
                'provider'            => 'cod',
                'provider_payment_id' => null,
                'provider_status'     => null,
                'amount'              => $total,
                'currency'            => 'USD',
                'status'              => 'pending',
                'metadata'            => [],
            ]);

            $this->commitReservationForOrder($order);

            $cart->items()->delete();

            return redirect()
                ->route('front.account.orders')
                ->with('success', 'Order placed successfully.');
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'provider'            => 'stripe',
                'provider_payment_id' => null,
                'provider_status'     => null,
                'amount'              => $total,
                'currency'            => 'USD',
                'status'              => 'pending',
                'metadata'            => [],
            ]
        );

        session(['checkout_order_id' => $order->id]);

        return redirect()->route('pay.form');
    }

    protected function getCurrentCart(Request $request): ?Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        }

        $sessionId = $request->session()->getId();

        return Cart::firstOrCreate([
            'session_id' => $sessionId,
        ]);
    }

    protected function commitReservationForOrder(Order $order): void
    {
        $reservations = StockReservation::where('order_id', $order->id)->get();
        if ($reservations->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($reservations) {
            foreach ($reservations as $r) {

                if ($r->status === 'committed') continue;
                if ($r->status === 'released') continue;

                $product = Product::where('id', $r->product_id)->lockForUpdate()->first();
                if (!$product) continue;

                $qty = (int) $r->qty;

                if ($product->stock !== null) {
                    $product->stock = max(0, (int)$product->stock - $qty);
                }

                $product->reserved_stock = max(0, (int)($product->reserved_stock ?? 0) - $qty);

                $product->save();

                $r->status = 'committed';
                $r->save();
            }
        });
    }

    protected function restockOrderItems(Order $order): void
    {
        if ($order->stock_reverted) return;

        $items = $order->items()->with('product')->get();

        foreach ($items as $item) {
            $product = $item->product;
            if ($product && $product->stock !== null) {
                $product->stock = (int)$product->stock + (int)$item->quantity;
                $product->save();
            }
        }

        $order->stock_reverted = true;
        $order->save();
    }
}
