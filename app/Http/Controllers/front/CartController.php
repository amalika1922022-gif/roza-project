<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * عرض محتويات الكارت
     * Route: front.cart.index  → GET /cart
     */
    public function index(Request $request)
    {
        // 🔹 سواء ضيف أو مسجّل دخول → جبلي الكارت المناسب
        $cart = $this->getActiveCart();

        $items = $cart->items()
            ->with(['product.images', 'product.category'])
            ->get();

        $totalQuantity = $items->sum('quantity');
        $totalAmount   = $items->sum(fn($item) => $item->price_at_added * $item->quantity);

        return view('Front.cart.index', compact('items', 'totalQuantity', 'totalAmount'));
    }

    /**
     * إضافة منتج إلى الكارت
     * Route: front.cart.add  → POST /cart/add
     */
    public function add(Request $request)
    {
        // ما عدنا نمنع الضيف، بس نتحقق من البيانات
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            $message = 'This product is out of stock.';

            // لو الطلب AJAX رجّع JSON
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        $cart = $this->getActiveCart();

        $requestedQty = (int) ($request->input('quantity', 1));

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        $currentQtyInCart = $existingItem ? $existingItem->quantity : 0;
        $totalRequested   = $currentQtyInCart + $requestedQty;

        if ($totalRequested > $product->stock) {
            $message = "Only {$product->stock} pieces available for this product.";

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $totalRequested,
            ]);
        } else {
            $cart->items()->create([
                'product_id'     => $product->id,
                'price_at_added' => $product->price,
                'quantity'       => $requestedQty,
            ]);
        }

        $successMessage = 'Product added to cart 💜';

        // ردّ خاص للـ AJAX (بدون redirect)
        if ($request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'message'    => $successMessage,
                'cart_count' => $cart->items()->sum('quantity'),
            ]);
        }

        // fallback للـ submit العادي
        return back()->with('success', $successMessage);
    }


    /**
     * حذف عنصر من الكارت
     * Route: front.cart.remove  → POST /cart/remove/{id}
     * {id} = cart_items.id
     */
    public function remove(Request $request, int $id)
    {
        // ✅ ما عدنا نشترط Auth، الضيف كمان يقدر يحذف من سلة الـ session تبعه
        $cart = $this->getActiveCart();

        $cart->items()->where('id', $id)->delete();

        return redirect()
            ->route('front.cart.index')
            ->with('success', 'Item removed from cart.');
    }

    /**
     * إرجاع / إنشاء الكارت الحالي (يوزر مسجّل أو ضيف)
     */


    private function getActiveCart(): Cart
    {
        // حالة يوزر مسجّل دخول
        if (Auth::check()) {
            return Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        }

        // ✅ حالة ضيف: نخزن cart_id داخل session (ثابت حتى لو session_id تغير)
        $guestCartId = session()->get('guest_cart_id');

        if ($guestCartId) {
            $cart = Cart::where('id', $guestCartId)->first();
            if ($cart) {
                // (اختياري) حدّث session_id للمتابعة
                $cart->update(['session_id' => session()->getId()]);
                return $cart;
            }
        }

        // لو ما في سلة مخزنة → أنشئ وحدة وخزن id تبعها بالسشن
        $cart = Cart::create([
            'session_id' => session()->getId(),
        ]);

        session()->put('guest_cart_id', $cart->id);

        return $cart;
    }
}
