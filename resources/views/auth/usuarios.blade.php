<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Usuarios | EventStock AI</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { background: #f6f4ef; color: #17251e; padding: 2rem 1rem; }
        .box { background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid rgba(23,37,30,0.1); margin-bottom: 1.5rem; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end; }
        .form-row input, .form-row select { width: 100%; padding: 0.65rem; border: 1px solid #ccc; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .pill-on { background: #d4edda; color: #155724; padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.8rem; }
        .pill-off { background: #f8d7da; color: #721c24; padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="layout">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h1>Gestión de Usuarios y Roles</h1>
            <a href="{{ url('/dashboard') }}" class="button outline">← Volver al panel</a>
        </div>

        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:0.8rem; border-radius:8px; margin-bottom:1rem;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#f8d7da; color:#721c24; padding:0.8rem; border-radius:8px; margin-bottom:1rem;">{{ $errors->first() }}</div>
        @endif

        <div class="box">
            <h3 style="margin-top:0;">Registrar nuevo usuario</h3>
            <form method="POST" action="{{ url('/usuarios') }}" class="form-row">
                @csrf
                <div><label>Nombre completo</label><input type="text" name="name" required></div>
                <div><label>Correo</label><input type="email" name="email" required></div>
                <div><label>Contraseña</label><input type="password" name="password" required></div>
                <div>
                    <label>Rol</label>
                    <select name="rol">
                        <option value="encargado">Encargado de Almacén</option>
                        <option value="analista">Analista</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div><button type="submit" class="button hero-button" style="width:100%;">Crear cuenta</button></div>
            </form>
        </div>

        <div class="box">
            <h3 style="margin-top:0;">Cuentas registradas</h3>
            <table>
                <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acción</th></tr></thead>
                <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td style="text-transform:uppercase; font-weight:600;">{{ $u->rol }}</td>
                        <td>@if($u->activo)<span class="pill-on">Activo</span>@else<span class="pill-off">Inactivo</span>@endif</td>
                        <td>
                            @if($u->id !== Auth::id())
                            <form method="POST" action="{{ url('/usuarios/'.$u->id.'/toggle') }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="button outline" style="padding:0.3rem 0.7rem; font-size:0.8rem;">
                                    {{ $u->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            @else
                            <span style="font-size:0.8rem; opacity:0.6;">Sesión actual</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
