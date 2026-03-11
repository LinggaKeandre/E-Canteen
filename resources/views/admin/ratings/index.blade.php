@extends('layouts.master')

@section('title', 'Kelola Rating Menu - E-Canteen')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-star"></i> Kelola Rating Menu</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($menus->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-emoji-frown display-1 text-muted"></i>
            <p class="mt-3">Belum ada menu.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Total Rating</th>
                        <th>Rating Rata-rata</th>
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
                            <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>
                                @if($menu->total_ratings > 0)
                                    <span class="badge bg-primary">{{ $menu->total_ratings }} rating</span>
                                @else
                                    <span class="text-muted">Belum ada rating</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->avg_rating > 0)
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($menu->avg_rating))
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($i <= ceil($menu->avg_rating) && $i > floor($menu->avg_rating))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-muted">{{ number_format($menu->avg_rating, 1) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.ratings.show', $menu) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Lihat Komentar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
