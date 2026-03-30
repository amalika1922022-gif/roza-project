<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    /**
     * GET /api/products
     * رجوع المنتجات، مع خيار فلترة حسب كاتيجوري
     * - كل المنتجات       → /api/products
     * - منتجات كاتيجوري 3 → /api/products?category_id=3
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // فلترة اختيارية حسب الكاتيجوري
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        return response()->json([
            'status'   => true,
            'count'    => $products->count(),
            'products' => $products,
        ]);
    }
    public function show(int $id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'product' => $product,
        ]);
    }
}


// http://127.0.0.1:8000/api/products?category_slug=flowers
// http://127.0.0.1:8000/api/products?category_id=1
