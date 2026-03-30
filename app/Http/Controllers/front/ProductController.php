<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات في الفرونت
     * Route: front.products.index → GET /products
     */
    public function index(Request $request)
    {
        // ✅ عدد كل المنتجات النشطة (All) — ثابت
        $allProductsCount = Product::where('is_active', true)->count();

        /**
         * ✅ نجيب فقط الـ Parents (parent_id = null)
         * ونجيب أطفالهم + count لكل واحد (فقط المنتجات النشطة)
         */
        $categories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->with([
                'children' => function ($q) {
                    $q->orderBy('name')
                        ->withCount([
                            'products as products_count' => function ($qq) {
                                $qq->where('is_active', true);
                            }
                        ]);
                }
            ])
            ->withCount([
                'products as products_count' => function ($q) {
                    $q->where('is_active', true);
                }
            ])
            ->get();

        $query = Product::with(['images', 'category'])
            ->where('is_active', true);

        // ✅ البحث دائماً على كل المنتجات (مثل ما بدك)
        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search . '%')
                    ->orWhere('name', 'like', '% ' . $search . '%');
            });
        }

        // ✅ فلترة حسب الفئة (تشتغل إذا ما في بحث) — مثل منطقك الحالي
        if (!$request->filled('q')) {
            if ($categorySlug = $request->get('category')) {

                $selectedCategory = Category::where('slug', $categorySlug)->first();

                if ($selectedCategory) {

                    // ✅ إذا Parent: جيب منتجات كل الأبناء + منتجات الأب نفسه (لو موجودة)
                    if (is_null($selectedCategory->parent_id)) {

                        $childrenIds = Category::where('parent_id', $selectedCategory->id)->pluck('id')->toArray();

                        $ids = array_merge([$selectedCategory->id], $childrenIds);

                        $query->whereIn('category_id', $ids);
                    } else {
                        // ✅ إذا Child: جيب منتجات هذا الطفل فقط
                        $query->where('category_id', $selectedCategory->id);
                    }
                }
            }
        }

        // ✅ الترتيب
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('Front.products.index', compact('products', 'categories', 'allProductsCount'));
    }


    /**
     * عرض تفاصيل منتج واحد
     * Route: front.products.show → GET /products/{slug}
     */
    public function show(string $slug)
    {
        $product = Product::with(['images', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('Front.products.show', compact('product'));
    }
}
