<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña | EventStock AI</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #17251e; color: #f5f2eb; padding: 1rem; }
        .auth-card { background: #1f3027; border-radius: 14px; padding: 2rem; width: 100%; max-width: 420px; }
        input { width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: #132019; color: #fff; margin: 0.5rem 0 1rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Recuperar contraseña</h2>
        <p style="font-size:0.9rem; opacity:0.8;">Ingresa tu correo para generar un enlace temporal de restablecimiento.</p>
        @if(session('reset_link'))
            <div style="background:rgba(40,167,69,0.2); border:1px solid #28a745; padding:0.8rem; border-radius:8px; margin-bottom:1rem; font-size:0.85rem; word-break:break-all;">
                <strong>Enlace temporal generado:</strong><br>
                <a href="{{ session('reset_link') }}" style="color:#98f5b1;">Haz clic aquí para cambiar tu clave</a>
            </div>
        @endif
        @if(session('status'))
            <div style="background:rgba(255,255,255,0.1); padding:0.8rem; border-radius:8px; margin-bottom:1rem;">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf
            <label>Correo electrónico</label>
            <input type="email" name="email" required>
            <button type="submit" class="button hero-button" style="width:100%; justify-content:center;">Generar enlace</button>
        </form>
        <p style="margin-top:1rem; font-size:0.85rem;"><a href="{{ url('/login') }}" style="color:#d4b886;">← Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
