<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SellerProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->sellerProfile;

        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->sellerProfile ?? new SellerProfile(['user_id' => $user->id]);

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'store_logo' => 'nullable|image|max:2048',
            'store_banner' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            if ($profile->store_logo) {
                \Storage::disk('public')->delete($profile->store_logo);
            }
            $validated['store_logo'] = $request->file('store_logo')->store('seller_logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('store_banner')) {
            if ($profile->store_banner) {
                \Storage::disk('public')->delete($profile->store_banner);
            }
            $validated['store_banner'] = $request->file('store_banner')->store('seller_banners', 'public');
        }

        $profile->fill($validated)->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profil toko berhasil diperbarui!');
    }
}
