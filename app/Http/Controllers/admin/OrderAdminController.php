<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderAdminController extends Controller
{
    /**
     * عرض كل الطلبات مع إمكانية الفلترة حسب المستخدم أو الحالة
     * GET /admin/orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment'])
            ->orderBy('created_at', 'desc');

        // فلترة حسب المستخدم (user_id من الكويري سترينغ)
        $selectedUserId = $request->get('user_id');
        if (!empty($selectedUserId)) {
            $query->where('user_id', $selectedUserId);
        }

        // فلترة حسب حالة الطلب
        $selectedStatus = $request->get('status');
        if (!empty($selectedStatus)) {
            $query->where('status', $selectedStatus);
        }

        $orders = $query->paginate(20)->withQueryString();

        // لائحة المستخدمين اللي عندهم طلبات، للفلترة
        $users = User::has('orders')
            ->orderBy('name')
            ->get();

        $availableStatuses = [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'failed',
        ];

        return view('Admin.Orders.index', [
            'orders'           => $orders,
            'users'            => $users,
            'availableStatuses'=> $availableStatuses,
            'selectedUserId'   => $selectedUserId,
            'selectedStatus'   => $selectedStatus,
        ]);
    }

    /**
     * عرض تفاصيل طلب واحد
     * GET /admin/orders/{order}
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'address',
            'items.product',
            'payment',
            'statusHistory.admin',
        ]);

        $availableStatuses = [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'failed',
        ];

        return view('Admin.Orders.show', [
            'order'            => $order,
            'availableStatuses'=> $availableStatuses,
        ]);
    }

    /**
     * تحديث حالة الطلب (status)
     * PUT /admin/orders/{order}/status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled,failed'],
            'note'   => ['nullable', 'string'],
        ]);

        $previousStatus = $order->status;
        $newStatus      = $data['status'];

        if ($previousStatus === $newStatus) {
            return back()->with('success', 'Status is already set to this value.');
        }

        // تحديث حالة الطلب
        $order->update([
            'status' => $newStatus,
        ]);

        // حفظ سجل تغيير الحالة
        OrderStatusHistory::create([
            'order_id'           => $order->id,
            'previous_status'    => $previousStatus,
            'new_status'         => $newStatus,
            'changed_by_user_id' => Auth::id(),
            'note'               => $data['note'] ?? null,
        ]);

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated successfully.');
    }
}
