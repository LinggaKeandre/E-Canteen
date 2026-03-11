<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::where('user_id', auth()->id())->latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:tersedia,habis',
            'has_variants' => 'nullable|boolean',
            'has_addons' => 'nullable|boolean',
            'is_daily' => 'nullable|boolean',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|min:1|max:5',
            // Variants and addons are optional
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.price_adjustment' => 'nullable|integer',
            'addons.*.name' => 'nullable|string|max:255',
            'addons.*.price' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['name', 'price', 'category', 'status']);
        $data['user_id'] = auth()->id();
        $data['has_variants'] = $request->boolean('has_variants');
        $data['has_addons'] = $request->boolean('has_addons');
        $data['is_daily'] = $request->boolean('is_daily');
        
        // Only set available_days if is_daily is true
        if ($request->boolean('is_daily') && $request->has('available_days')) {
            $data['available_days'] = $request->available_days;
        } else {
            $data['available_days'] = null;
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('menus', 'public');
            $data['photo_path'] = $path;
        }

        $menu = Menu::create($data);

        // Save variants
        if ($request->has('has_variants') && $request->variants) {
            foreach ($request->variants as $variant) {
                if (!empty($variant['name'])) {
                    MenuVariant::create([
                        'menu_id' => $menu->id,
                        'name' => $variant['name'],
                        'price_adjustment' => $variant['price_adjustment'] ?? 0,
                    ]);
                }
            }
        }

        // Save addons
        if ($request->has('has_addons') && $request->addons) {
            foreach ($request->addons as $addon) {
                if (!empty($addon['name'])) {
                    MenuAddon::create([
                        'menu_id' => $menu->id,
                        'name' => $addon['name'],
                        'price' => $addon['price'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        // Ensure seller can only edit their own menu
        if ($menu->user_id !== auth()->id()) {
            return redirect()->route('admin.menus.index')->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }
        
        // Load variants and addons
        $menu->load('variants', 'addons');
        
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        // Ensure seller can only update their own menu
        if ($menu->user_id !== auth()->id()) {
            return redirect()->route('admin.menus.index')->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:tersedia,habis',
            'has_variants' => 'nullable|boolean',
            'has_addons' => 'nullable|boolean',
            'is_daily' => 'nullable|boolean',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|min:1|max:5',
            // Variants and addons are optional - only validate if has_variants/has_addons is true AND variants/addons array is not empty
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.price_adjustment' => 'nullable|integer',
            'addons.*.name' => 'nullable|string|max:255',
            'addons.*.price' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['name', 'price', 'category', 'status']);
        $data['has_variants'] = $request->boolean('has_variants');
        $data['has_addons'] = $request->boolean('has_addons');
        $data['is_daily'] = $request->boolean('is_daily');
        
        // Only set available_days if is_daily is true
        if ($request->boolean('is_daily') && $request->has('available_days')) {
            $data['available_days'] = $request->available_days;
        } else {
            $data['available_days'] = null;
        }

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($menu->photo_path) {
                Storage::disk('public')->delete($menu->photo_path);
            }
            $path = $request->file('photo')->store('menus', 'public');
            $data['photo_path'] = $path;
        }

        $menu->update($data);

        // Update variants - delete old and create new
        if ($request->has('has_variants')) {
            $menu->variants()->delete();
            if ($request->variants) {
                foreach ($request->variants as $variant) {
                    if (!empty($variant['name'])) {
                        MenuVariant::create([
                            'menu_id' => $menu->id,
                            'name' => $variant['name'],
                            'price_adjustment' => $variant['price_adjustment'] ?? 0,
                        ]);
                    }
                }
            }
        } else {
            $menu->variants()->delete();
        }

        // Update addons - delete old and create new
        if ($request->has('has_addons')) {
            $menu->addons()->delete();
            if ($request->addons) {
                foreach ($request->addons as $addon) {
                    if (!empty($addon['name'])) {
                        MenuAddon::create([
                            'menu_id' => $menu->id,
                            'name' => $addon['name'],
                            'price' => $addon['price'] ?? 0,
                        ]);
                    }
                }
            }
        } else {
            $menu->addons()->delete();
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        // Ensure seller can only delete their own menu
        if ($menu->user_id !== auth()->id()) {
            return redirect()->route('admin.menus.index')->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }

        if ($menu->photo_path) {
            Storage::disk('public')->delete($menu->photo_path);
        }
        
        // Delete variants and addons first
        $menu->variants()->delete();
        $menu->addons()->delete();
        
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
    
    public function toggleStatus(Menu $menu)
    {
        // Ensure seller can only toggle their own menu
        if ($menu->user_id !== auth()->id()) {
            return redirect()->route('admin.menus.index')->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }

        // Toggle status: tersedia <-> habis
        $newStatus = $menu->status === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status' => $newStatus]);

        return redirect()->route('admin.menus.index')->with('success', 'Status menu berhasil diubah menjadi ' . ($newStatus === 'tersedia' ? 'Tersedia' : 'Habis') . '.');
    }
}
