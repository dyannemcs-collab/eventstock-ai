<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar | EventStock AI</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #17251e; color: #f5f2eb; padding: 1rem; }
        .auth-card { background: #1f3027; border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 2.2rem; width: 100%; max-width: 410px; box-shadow: 0 20px 40px rgba(0,0,0,0.35); }
        .auth-card h1 { font-size: 1.6rem; margin: 0.4rem 0 0.5rem; }
        .field { display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.1rem; }
        .field label { font-size: 0.85rem; opacity: 0.85; }
        .field input { padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: #132019; color: #fff; font-size: 0.95rem; }
        .alert-error { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff9aa2; padding: 0.7rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; }
        .alert-ok { background: rgba(40, 167, 69, 0.2); border: 1px solid #28a745; color: #98f5b1; padding: 0.7rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; }
        .links { display: flex; justify-content: space-between; margin-top: 1.2rem; font-size: 0.85rem; }
        .links a { color: #d4b886; text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-card">
        <p class="eyebrow accent" style="color:#d4b886; font-size:0.75rem; letter-spacing:1.5px;">R&amp;M EVENTOS · GESTIÓN INTERNA</p>
        <h1>Iniciar sesión</h1>
        <p style="font-size:0.9rem; opacity:0.75; margin-bottom:1.5rem;">Accede al panel de inventario, reservas y analítica.</p>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
            <div class="alert-ok">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@eventstock.pe" required autofocus>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="button hero-button" style="width:100%; justify-content:center; margin-top:0.5rem;">Ingresar al sistema</button>
        </form>

        <div class="links">
            <a href="{{ url('/') }}">← Volver al catálogo</a>
            <a href="{{ url('/forgot-password') }}">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</body>
</html>
