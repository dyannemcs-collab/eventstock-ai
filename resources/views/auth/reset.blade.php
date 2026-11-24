<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña | EventStock AI</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #17251e; color: #f5f2eb; padding: 1rem; }
        .auth-card { background: #1f3027; border-radius: 14px; padding: 2rem; width: 100%; max-width: 420px; }
        input { width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: #132019; color: #fff; margin: 0.4rem 0 1rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Establecer nueva contraseña</h2>
        @if($errors->any())<div style="color:#ff9aa2; margin-bottom:1rem;">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ url('/reset-password') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <label>Nueva contraseña (mínimo 6 caracteres)</label>
            <input type="password" name="password" required>
            <label>Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" required>
            <button type="submit" class="button hero-button" style="width:100%; justify-content:center;">Guardar contraseña</button>
        </form>
    </div>
</body>
</html>
