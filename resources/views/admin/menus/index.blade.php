@extends('layouts.master')

@section('title', 'Kelola Menu - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-menu-button-wide"></i> Kelola Menu</h2>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Menu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($menus->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-emoji-frown display-1 text-muted"></i>
            <p class="mt-3">Belum ada menu. Tambahkan menu pertama Anda!</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                        <tr>
                            <td>
                                @if($menu->photo_path)
                                    <img src="{{ asset('storage/' . $menu->photo_path) }}" alt="{{ $menu->name }}" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $menu->name }}</td>
                            <td>
                                @if($menu->category)
                                    <span class="badge bg-info">{{ $menu->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                @if($menu->is_daily)
                                    <span class="badge bg-warning text-dark ms-1" title="Tersedia: {{ $menu->available_days_display }}">
                                        <i class="bi bi-calendar-event"></i> Harian
                                    </span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>
                                @if($menu->rating_count > 0)
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menu->average_rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i <= ceil($menu->average_rating) && $i > floor($menu->average_rating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-muted small">
                                        {{ number_format($menu->average_rating, 1) }} ({{ $menu->rating_count }})
                                    </span>
                                    <a href="{{ route('admin.ratings.show', $menu->id) }}" class="btn btn-sm btn-outline-secondary ms-1" title="Lihat Komentar">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Belum ada rating</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.menus.toggleStatus', $menu) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($menu->status === 'tersedia')
                                        <button type="submit" class="badge badge-tersedia badge-clickable" title="Klik untuk mengubah menjadi Habis">
                                            Tersedia
                                        </button>
                                    @else
                                        <button type="submit" class="badge badge-habis badge-clickable" title="Klik untuk mengubah menjadi Tersedia">
                                            Habis
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus menu ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
