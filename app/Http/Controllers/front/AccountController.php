<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * صفحة البروفايل
     * GET /account/profile
     */
    public function profile(Request $request)
    {
        $user = Auth::user();

        $cartCount = $this->getCartCount($request);

        return view('Front.account.profile', [
            'user'      => $user,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * صفحة الطلبات
     * GET /account/orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $orders = $user->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $cartCount = $this->getCartCount($request);

        return view('Front.account.orders', [
            'orders'    => $orders,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * صفحة العنوان
     * GET /account/address
     */
    public function address(Request $request)
    {
        $user = Auth::user();

        $address = $user->addresses()
            ->where('is_default', true)
            ->first();

        $cartCount = $this->getCartCount($request);

        return view('Front.account.address', [
            'user'      => $user,
            'address'   => $address,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * تحديث / إنشاء العنوان الافتراضي
     * POST /account/address
     */
    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            // Full name
            'full_name' => [
                'bail',
                'required',
                'string',
                'min:3',
                'regex:/^\s*\S+\s+\S+.*$/',
            ],

            // Phone: أرقام فقط 8–15
            'phone' => [
                'bail',
                'required',
                'digits_between:8,15',
            ],

            // Country / City
            'country' => ['bail', 'required', 'string', 'min:2', 'max:191'],
            'city'    => ['bail', 'required', 'string', 'min:2', 'max:191'],

            // Address
            'address' => ['bail', 'required', 'string', 'min:5', 'max:255'],

            // Postal code
            'postal_code' => ['bail', 'required', 'digits:3'],
        ]);

        $address = $user->addresses()->where('is_default', true)->first();

        if ($address) {
            $address->update($data);
        } else {
            Address::create(array_merge($data, [
                'user_id'    => $user->id,
                'label'      => 'Default',
                'is_default' => true,
            ]));
        }

        if (empty($user->phone)) {
            $user->update(['phone' => $data['phone']]);
        }

        return redirect()
            ->route('front.account.address')
            ->with('success', 'Address updated successfully.');
    }


    /**
     * عدد عناصر السلة الحالية (للهيدر)
     */
    protected function getCartCount(Request $request): int
    {
        $cart = $this->getCurrentCart($request);

        return $cart ? $cart->items()->sum('quantity') : 0;
    }

    /**
     * الحصول على الكارت الحالي (لمستخدم مسجّل أو ضيف)
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
