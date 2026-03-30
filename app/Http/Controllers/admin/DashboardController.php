<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
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

        // تمرير البيانات لملف العرض:
        // resources/views/Admin/dashboard.blade.php
        return view('Admin.dashboard', [
            'stats'        => $stats,
            'latestOrders' => $latestOrders,
        ]);
    }
}
