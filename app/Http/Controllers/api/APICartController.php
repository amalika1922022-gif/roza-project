<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiCartController extends Controller
{
    /**
     * POST /api/cart/add
     * إضافة منتج إلى كارت اليوزر المسجل (auth:sanctum)
     */
    public function add(Request $request)
    {
        // لازم يكون عامل لوج إن (sanctum)
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Please login to manage your cart 💛',
            ], 401);
        }

        // نفس الفاليديشن تبع الويب
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        // لو مافي ستوك
        if ($product->stock <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'This product is out of stock.',
            ], 422);
        }

        // كارت اليوزر (نفس getActiveCart بالويب)
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $requestedQty = (int) ($data['quantity'] ?? 1);

        // العنصر الموجود من قبل
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        $currentQtyInCart = $existingItem ? $existingItem->quantity : 0;
        $totalRequested   = $currentQtyInCart + $requestedQty;

        // ما يتجاوز الستوك
        if ($totalRequested > $product->stock) {
            return response()->json([
                'status'  => false,
                'message' => "Only {$product->stock} pieces available for this product.",
            ], 422);
        }

        // تحديث أو إنشاء
        if ($existingItem) {
            $existingItem->update([
                'quantity' => $totalRequested,
            ]);
            $cartItem = $existingItem;
        } else {
            $cartItem = $cart->items()->create([
                'product_id'     => $product->id,
                'price_at_added' => $product->price,
                'quantity'       => $requestedQty,
            ]);
        }

        return response()->json([
            'status'   => true,
            'message'  => 'Product added to cart 💜',
            'cartItem' => $cartItem->load('product'),
        ]);
    }
}
