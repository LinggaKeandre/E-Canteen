<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Canteen')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-orange: #FF750F;
            --light-orange: #FFF3E6;
            --white: #FFFFFF;
            --light-bg: #F5F5F5;
            --text-dark: #1A1A1A;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.5;
        }
        
        .btn-primary {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: #E56A0D;
            border-color: #E56A0D;
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
        
        .btn-outline-primary {
            color: var(--primary-orange);
            border-color: var(--primary-orange);
            border-radius: 8px;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
            border-radius: 8px;
        }
        
        .btn-warning:hover {
            background-color: #E56A0D;
            border-color: #E56A0D;
            color: white;
        }
        
        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
            border-radius: 8px;
        }
        
        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
            border-radius: 8px;
        }
        
        .card {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            background-color: var(--white);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .badge-tersedia {
            background-color: var(--success);
        }
        
        .badge-habis {
            background-color: var(--danger);
        }
        
        .badge-clickable {
            cursor: pointer;
            border: none;
        }
        
        .badge-preparing {
            background-color: var(--primary-orange);
        }
        
        .badge-ready {
            background-color: var(--success);
        }
        
        .navbar {
            background-color: var(--white);
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            color: var(--primary-orange) !important;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .navbar-brand:hover {
            color: #E56A0D !important;
        }
        
        .nav-link {
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: var(--primary-orange);
        }
        
        .sidebar {
            background-color: var(--white);
            min-height: calc(100vh - 56px);
            border-right: 1px solid var(--border-color);
        }
        
        .sidebar a {
            color: var(--text-dark);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: var(--light-orange);
            border-left-color: var(--primary-orange);
            color: var(--primary-orange);
        }
        
        .menu-card {
            transition: transform 0.2s;
        }
        
        .menu-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .menu-img {
            height: 200px;
            object-fit: cover;
        }
        
        .price-tag {
            color: var(--primary-orange);
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .table th {
            background-color: var(--light-bg);
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-color);
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .alert-success {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .alert-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .alert-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .alert-info {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 117, 15, 0.15);
        }
        
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
        }
        
        .dropdown-item:hover {
            background-color: var(--light-orange);
            color: var(--primary-orange);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }
        
        .page-link {
            color: var(--text-dark);
        }
        
        .page-link:hover {
            color: var(--primary-orange);
        }
        
        .text-primary {
            color: var(--primary-orange) !important;
        }
        
        a.text-primary:hover {
            color: #E56A0D !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
        @if(Auth::user()->role === 'superadmin')
            @include('layouts.superadmin-navbar')
        @elseif(Auth::user()->role === 'admin')
            @include('layouts.admin-navbar')
        @else
            @include('layouts.user-navbar')
        @endauth
    @else
        @include('layouts.guest-navbar')
    @endauth
    
    <main>
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        });
        
        // Notification polling for admin navbar
        function checkNotifications() {
            fetch('/api/admin/notifications')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    const countEl = document.getElementById('notification-count');
                    if (badge && countEl) {
                        if (data.count > 0) {
                            countEl.textContent = data.count > 9 ? '9+' : data.count;
                            badge.style.display = 'inline-flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(err => console.log('Notification polling error:', err));
        }
        
        // Check notifications every 10 seconds
        checkNotifications();
        setInterval(checkNotifications, 10000);
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
