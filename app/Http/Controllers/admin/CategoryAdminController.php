<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    /**
     * عرض قائمة التصنيفات
     * GET /admin/categories
     */
    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Admin.Categories.index', compact('categories'));
    }

    /**
     * صفحة إنشاء تصنيف جديد
     * GET /admin/categories/create
     */
    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('Admin.Categories.create', compact('parents'));
    }

    /**
     * حفظ تصنيف جديد
     * POST /admin/categories
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['bail', 'required', 'string', 'max:191'],
            'slug'        => ['bail', 'nullable', 'string', 'max:191', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
        ]);


        // إنشاء slug تلقائي لو فاضي
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * عرض تفاصيل تصنيف واحد + المنتجات التابعة له
     * GET /admin/categories/{category}
     */
    public function show(Category $category)
    {
        $category->load([
            'parent',
            'children',
            'products' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
        ]);

        return view('Admin.Categories.show', compact('category'));
    }

    /**
     * صفحة تعديل تصنيف
     * GET /admin/categories/{category}/edit
     */
    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('Admin.Categories.edit', compact('category', 'parents'));
    }

    /**
     * تحديث تصنيف
     * PUT /admin/categories/{category}
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['bail', 'required', 'string', 'max:191'],
            'slug'        => ['bail', 'nullable', 'string', 'max:191', 'unique:categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'exists:categories,id'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * حذف تصنيف
     * DELETE /admin/categories/{category}
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
