<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterSellerController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-seller');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'store_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'balance' => 0,
        ]);

        SellerProfile::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'store_description' => $request->store_description ?? null,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi seller berhasil! Silakan login.');
    }
}
