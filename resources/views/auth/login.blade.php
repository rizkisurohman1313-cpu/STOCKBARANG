<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Stok Barang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .login-header h2 {
            margin: 0 0 10px 0;
            font-weight: 700;
            font-size: 28px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .login-form {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        .demo-credentials {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
        }

        .demo-credentials h6 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .demo-credentials p {
            margin: 5px 0;
            color: #666;
        }

        .demo-credentials code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .loading-spinner {
            display: none;
        }

        .btn-login.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn-login.loading .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h2>Stok Barang</h2>
                <p>Sistem Manajemen Inventori</p>
            </div>

            <div class="login-form">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Login Gagal!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="username">
                            <i class="bi bi-person"></i> Username
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('username') is-invalid @enderror" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            required 
                            autofocus
                        >
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <span class="loading-spinner">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </span>
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <div class="demo-credentials">
                    <h6><i class="bi bi-info-circle"></i> Demo Credentials</h6>
                    <p><strong>Admin:</strong> <code>admin</code> / <code>admin123</code></p>
                    <p><strong>Manajer Stok:</strong> <code>manager</code> / <code>manager123</code></p>
                    <p><strong>Operator:</strong> <code>operator</code> / <code>operator123</code></p>
                    <p><strong>Viewer:</strong> <code>viewer</code> / <code>viewer123</code></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = this.querySelector('.btn-login');
            btn.classList.add('loading');
            btn.querySelector('.loading-spinner').style.display = 'inline-block';
            btn.disabled = true;
        });
    </script>
</body>
</html>
