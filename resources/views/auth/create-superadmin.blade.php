<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Superadmin - E-Canteen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #FF9E1B 0%, #FFB84D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background-color: #FF750F;
            border-color: #FF750F;
        }
        .btn-primary:hover {
            background-color: #e56a0d;
            border-color: #e56a0d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Create Superadmin</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                                <hr>
                                <strong>Login Credentials:</strong><br>
                                Email: {{ session('email') }}<br>
                                Password: {{ session('password') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('create.superadmin') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required placeholder="Nama superadmin">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required placeholder="superadmin@ecanteen.sch.id">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required placeholder="Min 8 karakter">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Secret Key</label>
                                <input type="password" name="secret_key" class="form-control" required placeholder="Masukkan kunci rahasia">
                                <small class="text-muted">Hubungi developer untuk mendapatkan secret key</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Buat Superadmin
                            </button>
                        </form>
                    </div>
            </div>
    </div>
</body>
</html>
