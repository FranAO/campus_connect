<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - Campus Connect</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <div class="login-brand-icon">CC</div>
            <h1 class="login-title">Campus Connect</h1>
            <p class="login-subtitle">Panel de Gestión para Personal Administrativo</p>
        </div>

        <div id="loginAlert" class="login-alert"></div>

        @if($errors->any())
            <div class="login-alert" style="display:block;">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Correo Institucional</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="usuario@campusconnect.com" 
                    required 
                    autocomplete="email"
                    autofocus
                    value="{{ old('email') }}"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="current-password"
                >
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" id="btnSubmit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    <span id="btnText">Iniciar sesión</span>
                </button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/campus.js') }}"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const alertBox = document.getElementById('loginAlert');
            const submitBtn = document.getElementById('btnSubmit');
            const btnText = document.getElementById('btnText');
            
            alertBox.style.display = 'none';
            alertBox.textContent = '';
            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner"></span> Ingresando...';
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            try {
                const response = await campus.fetch('{{ route('login') }}', {
                    method: 'POST',
                    body: JSON.stringify({ email, password })
                });

                if (response.success) {
                    window.location.href = response.data?.redirect || '{{ route('dashboard') }}';
                } else {
                    alertBox.textContent = response.message || 'Error al iniciar sesión.';
                    alertBox.style.display = 'block';
                    submitBtn.disabled = false;
                    btnText.textContent = 'Iniciar sesión';
                }
            } catch (err) {
                alertBox.textContent = err.message || 'Error de conexión con el servidor.';
                alertBox.style.display = 'block';
                submitBtn.disabled = false;
                btnText.textContent = 'Iniciar sesión';
            }
        });
    </script>
</body>
</html>
