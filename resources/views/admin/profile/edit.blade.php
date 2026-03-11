@extends('layouts.master')

@section('title', 'Edit Profil Toko - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2 class="mb-4"><i class="bi bi-shop"></i> Profil Toko Saya</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Store Name -->
                        <div class="mb-4">
                            <label for="store_name" class="form-label fw-bold">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $profile->store_name ?? '') }}" required>
                            @error('store_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Store Description -->
                        <div class="mb-4">
                            <label for="store_description" class="form-label fw-bold">Deskripsi Toko</label>
                            <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="4" placeholder="Jelaskan tentang toko Anda...">{{ old('store_description', $profile->store_description ?? '') }}</textarea>
                            @error('store_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maks. 1000 karakter</small>
                        </div>

                        <!-- Store Logo -->
                        <div class="mb-4">
                            <label for="store_logo" class="form-label fw-bold">Logo Toko</label>
                            <p class="text-muted small">Upload logo toko Anda yang akan ditampilkan di kartu toko</p>
                            
                            @if($profile && $profile->store_logo)
                                <div class="mb-3">
                                    <p class="mb-2"><strong>Logo Saat Ini:</strong></p>
                                    <img src="{{ asset('storage/' . $profile->store_logo) }}" alt="Logo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                                </div>
                            @endif

                            <input type="file" class="form-control @error('store_logo') is-invalid @enderror" id="store_logo" name="store_logo" accept="image/*" onchange="previewLogo(event)">
                            @error('store_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 2MB</small>
                            <div id="logo-preview" class="mt-3"></div>
                        </div>

                        <!-- Store Banner -->
                        <div class="mb-4">
                            <label for="store_banner" class="form-label fw-bold">Banner Toko</label>
                            <p class="text-muted small">Upload banner toko yang akan menjadi header kartu toko (Rekomendasi: 600x200px)</p>
                            
                            @if($profile && $profile->store_banner)
                                <div class="mb-3">
                                    <p class="mb-2"><strong>Banner Saat Ini:</strong></p>
                                    <img src="{{ asset('storage/' . $profile->store_banner) }}" alt="Banner" style="width: 100%; max-width: 400px; height: auto; border-radius: 4px; border: 1px solid #ddd; object-fit: cover;">
                                </div>
                            @endif

                            <input type="file" class="form-control @error('store_banner') is-invalid @enderror" id="store_banner" name="store_banner" accept="image/*" onchange="previewBanner(event)">
                            @error('store_banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 2MB</small>
                            <div id="banner-preview" class="mt-3"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logo-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <p class="mb-2"><strong>Preview Logo Baru:</strong></p>
                <img src="${e.target.result}" alt="Logo Preview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
            `;
        };
        reader.readAsDataURL(file);
    }
}

function previewBanner(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('banner-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <p class="mb-2"><strong>Preview Banner Baru:</strong></p>
                <img src="${e.target.result}" alt="Banner Preview" style="width: 100%; max-width: 400px; height: auto; border-radius: 4px; border: 1px solid #ddd; object-fit: cover;">
            `;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
