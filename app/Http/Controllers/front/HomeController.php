<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\HomepageCarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * إظهار صفحة الهوم
     * GET /
     */
    public function index(Request $request)
    {
        // كاتيجوريز مميزة (مثلاً أول 3)
        $featuredCategories = Category::orderBy('created_at')->get();

        // أحدث المنتجات
        $latestProducts = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // ✅ منتجات الكورسل المختارة من الداشبورد
        $heroProducts = HomepageCarouselItem::with(['product.images'])
            ->orderBy('sort_order')
            ->get()
            ->pluck('product')
            ->filter(function ($product) {
                return $product && $product->is_active;
            });

        $cartCount = $this->getCartCount($request);

        return view('Front.home', [
            'featuredCategories' => $featuredCategories,
            'latestProducts'     => $latestProducts,
            'heroProducts'       => $heroProducts,
            'cartCount'          => $cartCount,
        ]);
    }

    /**
     * عدد منتجات الكارت الحالي (للهيدر)
     */
    protected function getCartCount(Request $request): int
    {
        $cart = $this->getCurrentCart($request);

        if (! $cart) {
            return 0;
        }

        return $cart->items()->sum('quantity');
    }

    /**
     * الحصول على كارت المستخدم الحالي (يوزر أو سيشن)
     */
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
}
