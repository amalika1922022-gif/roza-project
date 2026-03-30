<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ForgetPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forget-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['bail', 'required', 'email'],
        ]);

        // ✅ تحقق أن الإيميل موجود بالداتا بيز
        $exists = User::where('email', $request->email)->exists();

        if (!$exists) {
            // نفس أسلوب Laravel errors تحت input
            return back()
                ->withErrors(['email' => 'This email is not registered.'])
                ->withInput();
        }

        // ✅ Demo message + انتقال لصفحة reset
        $msg = "If the email exists, a reset link has been sent.";

        return redirect()
            ->route('auth.password.reset.demo', ['email' => $request->email])
            ->with('status', $msg);
    }
}
