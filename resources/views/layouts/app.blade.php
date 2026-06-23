<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Manajemen Stok Barang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }

        body {
            background-color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            min-height: 100vh;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            overflow-y: auto;
            padding: 20px 0;
        }

        .sidebar .brand {
            padding: 20px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar .brand h5 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar .nav-menu li {
            margin: 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar .nav-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar .nav-menu a:hover,
        .sidebar .nav-menu a.active {
            background-color: var(--secondary-color);
            color: white;
            padding-left: 25px;
        }

        .sidebar .nav-menu .submenu {
            display: none;
            background-color: rgba(0,0,0,0.1);
            padding-left: 20px;
        }

        .sidebar .nav-menu .submenu.show {
            display: block;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .top-navbar {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .page-header h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 600;
        }

        .page-header p {
            margin: 5px 0 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .stat-card {
            text-align: center;
            padding: 20px;
        }

        .stat-card i {
            font-size: 32px;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .stat-card h6 {
            color: #7f8c8d;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .alert {
            border-radius: 5px;
            border: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transition: width 0.3s ease;
            }

            .sidebar.show {
                width: 250px;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h5><i class="bi bi-box2"></i> Stok Manager</h5>
            <small>v1.0</small>
        </div>

        <ul class="nav-menu">
            <li><a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif"><i class="bi bi-house-door"></i> Dashboard</a></li>

            @if(auth()->user()->hasAnyRole(['admin', 'manajer_stok']))
            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-people"></i> Pengguna <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('users.index') }}">Daftar Pengguna</a></li>
                    @if(auth()->user()->hasRole('admin'))
                    <li><a href="{{ route('users.create') }}">+ Tambah Pengguna</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(auth()->user()->hasAnyRole(['admin', 'manajer_stok', 'operator']))
            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-tag"></i> Kategori <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('categories.index') }}">Daftar Kategori</a></li>
                    <li><a href="{{ route('categories.create') }}">+ Tambah Kategori</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-shop"></i> Supplier <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('suppliers.index') }}">Daftar Supplier</a></li>
                    <li><a href="{{ route('suppliers.create') }}">+ Tambah Supplier</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-box"></i> Produk <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('products.index') }}">Daftar Produk</a></li>
                    <li><a href="{{ route('products.create') }}">+ Tambah Produk</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-bar-chart"></i> Stok <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('stocks.index') }}">Daftar Stok</a></li>
                    <li><a href="{{ route('stocks.lowStock') }}">Stok Rendah</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-file-earmark-arrow-down"></i> Pembelian <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('purchase-orders.index') }}">Daftar PO</a></li>
                    <li><a href="{{ route('purchase-orders.create') }}">+ Buat PO</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-file-earmark-check"></i> Penerimaan <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('receivings.index') }}">Daftar Penerimaan</a></li>
                    <li><a href="{{ route('receivings.create') }}">+ Terima Barang</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-file-earmark-arrow-up"></i> Penjualan <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('sales-orders.index') }}">Daftar SO</a></li>
                    <li><a href="{{ route('sales-orders.create') }}">+ Buat SO</a></li>
                </ul>
            </li>

            <li>
                <a href="#" onclick="toggleSubmenu(event)"><i class="bi bi-file-text"></i> Laporan <i class="bi bi-chevron-down float-end"></i></a>
                <ul class="submenu">
                    <li><a href="{{ route('reports.stock') }}">Laporan Stok</a></li>
                    <li><a href="{{ route('reports.purchase-orders') }}">Laporan Pembelian</a></li>
                    <li><a href="{{ route('reports.sales-orders') }}">Laporan Penjualan</a></li>
                </ul>
            </li>
            @endif
        </ul>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div>
                <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="text-end">
                <span class="me-3">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->nama_lengkap }}
                    <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ada Kesalahan!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        function toggleSubmenu(event) {
            event.preventDefault();
            const submenu = event.target.parentElement.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('show');
            }
        }

        // Set active menu item
        document.querySelectorAll('.nav-menu a').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
                const submenu = link.parentElement.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    submenu.classList.add('show');
                }
            }
        });
    </script>
    @yield('extra-js')
</body>
</html>
