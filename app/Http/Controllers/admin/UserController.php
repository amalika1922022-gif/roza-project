<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * عرض قائمة كل المستخدمين.
     * GET /admin/users
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Admin.users.index', compact('users'));
    }

    /**
     * عرض تفاصيل مستخدم واحد + طلباته.
     * GET /admin/users/{user}
     */
    public function show(User $user)
    {
        $user->load(['orders' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        return view('Admin.users.show', compact('user'));
    }

    /**
     * تحديث الدور (admin / customer).
     * PUT /admin/users/{user}/role
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
        ]);

        // ✅ (إضافة) إذا الأدمن الوحيد حاول يغيّر حاله إلى customer → ممنوع (حتى ما يصير عندك 0 admin)
        if ($user->role === 'admin' && $data['role'] === 'customer' && auth()->id() === $user->id) {
            $adminsCount = User::where('role', 'admin')->count();

            if ($adminsCount <= 1) {
                return back()->withErrors([
                    'role' => 'You cannot change your role because you are the only admin.',
                ]);
            }
        }

        // ما نسمح نغيّر آخر أدمن إلى customer
        if ($user->role === 'admin' && $data['role'] === 'customer') {
            $adminsCount = User::where('role', 'admin')->count();

            if ($adminsCount <= 1) {
                return back()->withErrors([
                    'role' => 'You cannot change the role of the last admin.',
                ]);
            }
        }

        $user->update([
            'role' => $data['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User role updated successfully.');
    }

    /**
     * حظر مستخدم.
     * PUT /admin/users/{user}/block
     */
    public function block(User $user)
    {
        // ✅ (تعديل) ما نحظر نفسنا فقط إذا كنا الأدمن الوحيد
        if (auth()->id() === $user->id) {
            $adminsCount = User::where('role', 'admin')->count();

            if ($adminsCount <= 1) {
                return back()->withErrors([
                    'user' => 'You cannot block your own account because you are the only admin.',
                ]);
            }
        }

        // ما نحظر آخر أدمن
        if ($user->role === 'admin') {
            $adminsCount = User::where('role', 'admin')->count();

            if ($adminsCount <= 1) {
                return back()->withErrors([
                    'user' => 'You cannot block the last admin.',
                ]);
            }
        }

        $user->update([
            'is_blocked' => true,
        ]);

        return back()->with('success', 'User has been blocked.');
    }

    /**
     * إظهار كل المستخدمين المحظورين.
     * GET /admin/users/blocked
     */
    public function blocked()
    {
        $users = User::where('is_blocked', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Admin.users.blocked', compact('users'));
    }

    /**
     * فكّ الحظر عن مستخدم.
     * PUT /admin/users/{user}/unblock
     */
    public function unblock(User $user)
    {
        $user->update([
            'is_blocked' => false,
        ]);

        return back()->with('success', 'User has been unblocked.');
    }
}
