<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // لو مو عامل Log in → نرجعو على صفحة تسجيل الدخول
        if (!Auth::check()) {
            return redirect()
                ->route('auth.login')
                ->with('error', 'You must be logged in as admin to access the admin panel.');
        }

        $user = Auth::user();

        // لو الأكاونت محظور ما لازم يدخل لا على الأدمن ولا أصلاً يكمل سيشن
        if (!empty($user->is_blocked) && $user->is_blocked === true) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('auth.login')
                ->with('error', 'Your account is blocked.');
        }

        // لو مو أدمن (role ≠ admin) → منرجعو على الفرونت
        if ($user->role !== 'admin') {
            return redirect()
                ->route('front.home')
                ->with('error', 'You are not authorized to access the admin panel.');
        }

        return $next($request);
    }
}
