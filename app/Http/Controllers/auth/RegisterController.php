<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:191',
                'regex:/^\s*\S+\s+\S+.*$/',
            ],
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:191',
                'unique:users,email',
            ],
            'phone' => [
                'bail',
                'required',
                'digits_between:8,15',
                'unique:users,phone',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/',
            ],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect()->route('front.home');
    }
}
