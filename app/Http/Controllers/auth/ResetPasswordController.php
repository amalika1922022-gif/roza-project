<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function show(Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('email'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'bail',
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/',
            ],
        ], [
            'email.exists' => 'This email is not registered.',
        ]);

        // ✅ 1) جيب المستخدم
        $user = User::where('email', $data['email'])->firstOrFail();

        // ✅ 2) خزّن باسورد جديد (hash) بدل القديم
        $user->password = Hash::make($data['password']);
        $user->save();

        // ✅ 3) إذا كان عامل login حالياً، طلّعو (أمان)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ 4) رجّعو على login مع تنبيه نجاح
        return redirect()
            ->route('auth.login')
            ->with('success', 'Password reset successfully. You can now login with your new password.');
    }
}
