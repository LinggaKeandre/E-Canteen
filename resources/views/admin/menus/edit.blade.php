@extends('layouts.master')

@section('title', 'Edit Menu - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-pencil"></i> Edit Menu</h2>
    
    @if($errors->any())
        <div class="alert alert-error">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data" class="card">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Menu</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $menu->name }}" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Harga (Rp)</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ $menu->price }}" min="0" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Pilih Kategori</option>
                    @foreach(\App\Models\Menu::getCategories() as $category)
                        <option value="{{ $category }}" {{ $menu->category === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Foto Menu</label>
                @if($menu->photo_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $menu->photo_path) }}" alt="{{ $menu->name }}" style="max-width: 200px;" class="rounded">
                    </div>
                @endif
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="tersedia" {{ $menu->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ $menu->status === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            
            <!-- Menu Harian Section -->
            <div class="mb-3 form-check">
                <input type="hidden" name="is_daily" value="0">
                <input type="checkbox" class="form-check-input" id="is_daily" name="is_daily" value="1" onchange="toggleDailyMenu()" {{ $menu->is_daily ? 'checked' : '' }}>
                <label class="form-check-label" for="is_daily">
                    <strong>Menu Harian</strong>
                    <small class="text-muted d-block">Menu ini hanya tersedia pada hari-hari tertentu dalam seminggu</small>
                </label>
            </div>
            
            <div id="daily-days-section" style="display: {{ $menu->is_daily ? 'block' : 'none' }};">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Pilih Hari Tersedia</h6>
                        <div class="row">
                            @foreach(\App\Models\Menu::getWeekdays() as $dayNum => $dayName)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_days[]" id="day_{{ $dayNum }}" value="{{ $dayNum }}" {{ in_array($dayNum, $menu->available_days ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $dayNum }}">
                                            {{ $dayName }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Varian Section -->
            <div class="mb-3 form-check">
                <input type="hidden" name="has_variants" value="0">
                <input type="checkbox" class="form-check-input" id="has_variants" name="has_variants" value="1" onchange="toggleVariants()" {{ $menu->has_variants ? 'checked' : '' }}>
                <label class="form-check-label" for="has_variants">
                    <strong>Aktifkan Varian</strong>
                    <small class="text-muted">(Misalnya: Small, Medium, Large)</small>
                </label>
            </div>
            
            <div id="variants-section" style="display: {{ $menu->has_variants ? 'block' : 'none' }};">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Kelola Varian</h6>
                        <div id="variants-container">
                            @if($menu->has_variants && $menu->variants->count() > 0)
                                @foreach($menu->variants as $index => $variant)
                                    <div class="row mb-2 variant-row">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variant->name }}" placeholder="Nama Varian">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="variants[{{ $index }}][price_adjustment]" value="{{ $variant->price_adjustment }}" placeholder="Penyesuaian Harga">
                                        </div>
                                        <div class="col-md-2">
                                            <span class="text-muted small">+Rp</span>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.variant-row').remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2 variant-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="variants[0][name]" placeholder="Nama Varian (misal: Small)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="variants[0][price_adjustment]" placeholder="Penyesuaian Harga (0)" value="0">
                                    </div>
                                    <div class="col-md-2">
                                        <span class="text-muted small">+Rp</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariant()">
                            <i class="bi bi-plus"></i> Tambah Varian
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Addons Section -->
            <div class="mb-3 form-check">
                <input type="hidden" name="has_addons" value="0">
                <input type="checkbox" class="form-check-input" id="has_addons" name="has_addons" value="1" onchange="toggleAddons()" {{ $menu->has_addons ? 'checked' : '' }}>
                <label class="form-check-label" for="has_addons">
                    <strong>Aktifkan Add-On</strong>
                    <small class="text-muted">(Misalnya: Extra Keju, Extra Saus)</small>
                </label>
            </div>
            
            <div id="addons-section" style="display: {{ $menu->has_addons ? 'block' : 'none' }};">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Kelola Add-On</h6>
                        <div id="addons-container">
                            @if($menu->has_addons && $menu->addons->count() > 0)
                                @foreach($menu->addons as $index => $addon)
                                    <div class="row mb-2 addon-row">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="addons[{{ $index }}][name]" value="{{ $addon->name }}" placeholder="Nama Add-On">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="addons[{{ $index }}][price]" value="{{ $addon->price }}" placeholder="Harga">
                                        </div>
                                        <div class="col-md-2">
                                            <span class="text-muted small">Rp</span>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.addon-row').remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2 addon-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="addons[0][name]" placeholder="Nama Add-On (misal: Extra Keju)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="addons[0][price]" placeholder="Harga (Rp)" value="0">
                                    </div>
                                    <div class="col-md-2">
                                        <span class="text-muted small">Rp</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAddon()">
                            <i class="bi bi-plus"></i> Tambah Add-On
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let variantCount = {{ $menu->has_variants && $menu->variants->count() > 0 ? $menu->variants->count() : 1 }};
    let addonCount = {{ $menu->has_addons && $menu->addons->count() > 0 ? $menu->addons->count() : 1 }};
    
    function toggleDailyMenu() {
        const checkbox = document.getElementById('is_daily');
        const section = document.getElementById('daily-days-section');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
    
    function toggleVariants() {
        const checkbox = document.getElementById('has_variants');
        const section = document.getElementById('variants-section');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
    
    function toggleAddons() {
        const checkbox = document.getElementById('has_addons');
        const section = document.getElementById('addons-section');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }
    
    function addVariant() {
        const container = document.getElementById('variants-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 variant-row';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="variants[${variantCount}][name]" placeholder="Nama Varian (misal: Large)">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="variants[${variantCount}][price_adjustment]" placeholder="Penyesuaian Harga (0)" value="0">
            </div>
            <div class="col-md-2">
                <span class="text-muted small">+Rp</span>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.variant-row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        variantCount++;
    }
    
    function addAddon() {
        const container = document.getElementById('addons-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 addon-row';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="addons[${addonCount}][name]" placeholder="Nama Add-On (misal: Extra Telur)">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="addons[${addonCount}][price]" placeholder="Harga (Rp)" value="0">
            </div>
            <div class="col-md-2">
                <span class="text-muted small">Rp</span>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.addon-row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        addonCount++;
    }
</script>
@endpush
@endsection
