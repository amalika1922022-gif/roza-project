<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class ApiCategoryController extends Controller
{
    /**
     * GET /api/categories
     * لازم ترجع لستة الفئات كـ JSON
     */
    public function categories()
    {
        $categories = Category::select('id', 'name', 'slug', 'parent_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }
}
