<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User; // ✅ (إضافة) لفحص الحظر
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['bail', 'required', 'email'],
            'password' => ['bail', 'required', 'string'],
        ]);

        // ✅ (إضافة) منع تسجيل الدخول للمستخدم المحظور قبل Auth::attempt
        $u = User::where('email', $credentials['email'])->first();
        if ($u && (bool) $u->is_blocked === true) {
            return back()->withErrors([
                'email' => 'Your account is blocked. Please contact support.',
            ])->onlyInput('email');
        }

        // ✅ خزن guest_cart_id قبل تسجيل الدخول
        $guestCartId = $request->session()->get('guest_cart_id');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // ✅ (إضافة احتياطية) إذا صار logged in بأي سبب وهو محظور → طلّعو فوراً
            if ((bool) ($user->is_blocked ?? false) === true) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('auth.login')->withErrors([
                    'email' => 'Your account is blocked. Please contact support.',
                ]);
            }

            // ✅ دمج سلة الضيف (إن وجدت)
            if ($guestCartId) {
                $guestCart = Cart::with('items')->where('id', $guestCartId)->first();

                if ($guestCart) {
                    $userCart = Cart::firstOrCreate([
                        'user_id' => $user->id,
                    ]);

                    if ($guestCart->id !== $userCart->id) {
                        foreach ($guestCart->items as $item) {
                            $existingItem = $userCart->items()
                                ->where('product_id', $item->product_id)
                                ->first();

                            if ($existingItem) {
                                $existingItem->update([
                                    'quantity' => $existingItem->quantity + $item->quantity,
                                ]);
                            } else {
                                $userCart->items()->create([
                                    'product_id'     => $item->product_id,
                                    'quantity'       => $item->quantity,
                                    'price_at_added' => $item->price_at_added,
                                ]);
                            }
                        }

                        $guestCart->items()->delete();
                        $guestCart->delete();
                    }
                }

                // ✅ امسح ربط سلة الضيف من السشن
                $request->session()->forget('guest_cart_id');
            }

            // ✅ regenerate بعد الدمج
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('front.home');
        }

        return back()->withErrors([
            'email' => 'The email or password is incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('front.home')
            ->with('success', 'Logged out successfully 💜');
    }
}
