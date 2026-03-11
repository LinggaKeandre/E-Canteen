@extends('layouts.master')

@section('title', 'Komentar Rating - ' . $menu->name . ' - E-Canteen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-star"></i> Komentar Rating: {{ $menu->name }}</h2>
            <p class="text-muted mb-0">Harga: Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
        </div>
        <a href="{{ route('admin.ratings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h3 class="text-warning mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($menu->average_rating))
                                <i class="bi bi-star-fill"></i>
                            @elseif($i <= ceil($menu->average_rating) && $i > floor($menu->average_rating))
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </h3>
                    <p class="mb-0">Rating Rata-rata</p>
                    <small class="text-muted">{{ number_format($menu->average_rating, 1) }} dari 5</small>
                </div>
                <div class="col-md-4">
                    <h3>{{ $menu->rating_count }}</h3>
                    <p class="mb-0">Total Rating</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-0">Rp {{ number_format($menu->price, 0, ',', '.') }}</h5>
                    <p class="mb-0">Harga Menu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating breakdown -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Distribusi Rating</h5>
        </div>
        <div class="card-body">
            @php
                $ratings = \App\Models\Rating::where('menu_id', $menu->id)->get();
                $total = $ratings->count();
                $distribution = [
                    5 => $ratings->where('rating', 5)->count(),
                    4 => $ratings->where('rating', 4)->count(),
                    3 => $ratings->where('rating', 3)->count(),
                    2 => $ratings->where('rating', 2)->count(),
                    1 => $ratings->where('rating', 1)->count(),
                ];
            @endphp
            
            @for($i = 5; $i >= 1; $i--)
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 60px;">
                        {{ $i }} <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($distribution[$i] / $total) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div style="width: 50px; text-align: right;">
                        {{ $distribution[$i] }}
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Comments List -->
    <h5 class="mb-3">Komentar ({{ $menu->ratings->count() }})</h5>
    
    @if($menu->ratings->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-chat-dots display-1 text-muted"></i>
            <p class="mt-3">Belum ada komentar untuk menu ini.</p>
        </div>
    @else
        @foreach($menu->ratings->sortByDesc('created_at') as $rating)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $rating->user->name ?? 'User' }}</strong>
                            <span class="text-muted small ms-2">{{ $rating->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating->rating)
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    @if($rating->comment)
                        <p class="mt-2 mb-0">{{ $rating->comment }}</p>
                    @else
                        <p class="mt-2 mb-0 text-muted"><em>Tidak ada komentar</em></p>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
