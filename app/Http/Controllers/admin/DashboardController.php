<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageCarouselItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات سريعة للداشبورد
        $stats = [
            'orders_count'     => Order::count(),
            'products_count'   => Product::count(),
            'categories_count' => Category::count(),
            'customers_count'  => User::where('role', 'customer')->count(),
        ];

        // آخر 10 طلبات
        $latestOrders = Order::with('user')
            ->latest()   // ORDER BY created_at DESC
            ->take(10)
            ->get();

        // ✅ كل المنتجات لاختيارها داخل سكشن الكورسل
        $allProducts = Product::orderBy('name')
            ->get(['id', 'name']);

        // ✅ المنتجات المختارة حاليًا لكورسل الصفحة الرئيسية
        $homepageCarouselItems = HomepageCarouselItem::with('product.images')
            ->orderBy('sort_order')
            ->get();

        // تمرير البيانات لملف العرض:
        // resources/views/Admin/dashboard.blade.php
        return view('Admin.dashboard', [
            'stats'                 => $stats,
            'latestOrders'          => $latestOrders,
            'allProducts'           => $allProducts,
            'homepageCarouselItems' => $homepageCarouselItems,
        ]);
    }

    public function storeHomepageCarouselItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $exists = HomepageCarouselItem::where('product_id', $data['product_id'])->exists();

        if ($exists) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'This product is already added to the homepage carousel.');
        }

        HomepageCarouselItem::create([
            'product_id' => $data['product_id'],
            'sort_order' => $data['sort_order'] ?? ((HomepageCarouselItem::max('sort_order') ?? 0) + 1),
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Product added to homepage carousel successfully.');
    }

    public function updateHomepageCarouselItem(Request $request, HomepageCarouselItem $item)
{
    $data = $request->validate([
        'sort_order' => ['required', 'integer', 'min:1'],
    ]);

    $item->update([
        'sort_order' => $data['sort_order'],
    ]);

    return redirect()
        ->route('admin.dashboard')
        ->with('success', 'Carousel order updated successfully.');
}

    public function destroyHomepageCarouselItem(HomepageCarouselItem $item)
    {
        $item->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Product removed from homepage carousel successfully.');
    }
}