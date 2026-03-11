@extends('layouts.master')

@section('title', 'Tambah Menu - E-Canteen')

@section('content')
<div class="container py-4">
    <h2><i class="bi bi-plus-circle"></i> Tambah Menu</h2>
    
    @if($errors->any())
        <div class="alert alert-error">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Menu</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Harga (Rp)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Pilih Kategori</option>
                    @foreach(\App\Models\Menu::getCategories() as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Foto Menu</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Habis</option>
                </select>
            </div>
            
            <!-- Menu Harian Section -->
            <div class="mb-3 form-check">
                <input type="hidden" name="is_daily" value="0">
                <input type="checkbox" class="form-check-input" id="is_daily" name="is_daily" value="1" onchange="toggleDailyMenu()">
                <label class="form-check-label" for="is_daily">
                    <strong>Menu Harian</strong>
                    <small class="text-muted d-block">Menu ini hanya tersedia pada hari-hari tertentu dalam seminggu</small>
                </label>
            </div>
            
            <div id="daily-days-section" style="display: none;">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Pilih Hari Tersedia</h6>
                        <div class="row">
                            @foreach(\App\Models\Menu::getWeekdays() as $dayNum => $dayName)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_days[]" id="day_{{ $dayNum }}" value="{{ $dayNum }}">
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
                <input type="checkbox" class="form-check-input" id="has_variants" name="has_variants" value="1" onchange="toggleVariants()">
                <label class="form-check-label" for="has_variants">
                    <strong>Aktifkan Varian</strong>
                    <small class="text-muted">(Misalnya: Small, Medium, Large)</small>
                </label>
            </div>
            
            <div id="variants-section" style="display: none;">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Kelola Varian</h6>
                        <div id="variants-container">
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
                <input type="checkbox" class="form-check-input" id="has_addons" name="has_addons" value="1" onchange="toggleAddons()">
                <label class="form-check-label" for="has_addons">
                    <strong>Aktifkan Add-On</strong>
                    <small class="text-muted">(Misalnya: Extra Keju, Extra Saus)</small>
                </label>
            </div>
            
            <div id="addons-section" style="display: none;">
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">Kelola Add-On</h6>
                        <div id="addons-container">
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
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAddon()">
                            <i class="bi bi-plus"></i> Tambah Add-On
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let variantCount = 1;
    let addonCount = 1;
    
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
                <input type="text" class="form-control" name="variants[${variantCount}][name]" placeholder="Nama Varian (misal: Medium)">
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
                <input type="text" class="form-control" name="addons[${addonCount}][name]" placeholder="Nama Add-On (misal: Extra Saus)">
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
