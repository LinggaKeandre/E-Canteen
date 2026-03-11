@extends('layouts.master')

@section('title', 'Login - E-Canteen')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="bi bi-box-arrow-in-right"></i> Login</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <p class="mb-2">Belum punya akun?</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('register.user') }}" class="btn btn-outline-primary btn-sm">Daftar Siswa/Guru</a>
                            <a href="{{ route('register.seller') }}" class="btn btn-outline-primary btn-sm">Daftar Penjual</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
