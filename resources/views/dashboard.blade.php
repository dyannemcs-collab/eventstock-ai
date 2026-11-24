<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Gestión | EventStock AI</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { background: #f6f4ef; color: #17251e; }
        .topbar { background: #17251e; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .badge { background: #d4b886; color: #17251e; padding: 0.25rem 0.65rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .dash-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .module-card { background: #fff; border: 1px solid rgba(23,37,30,0.1); border-radius: 12px; padding: 1.5rem; }
        .module-card h3 { margin: 0 0 0.5rem; }
        .module-card p { font-size: 0.9rem; opacity: 0.75; margin-bottom: 1.2rem; }
    </style>
</head>
<body>
    <header class="topbar">
        <div>
            <strong>EventStock AI</strong> · <span style="opacity:0.8;">{{ Auth::user()->name }}</span>
            <span class="badge">{{ Auth::user()->rol }}</span>
        </div>
        <div style="display:flex; gap:1rem; align-items:center;">
            <a href="{{ url('/') }}" style="color:#d4b886; text-decoration:none; font-size:0.9rem;">Ver web pública</a>
            <form method="POST" action="{{ url('/logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="button outline" style="color:#fff; border-color:rgba(255,255,255,0.4); padding:0.4rem 0.9rem;">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="layout" style="padding: 2.5rem 1.5rem;">
        <h1>Panel de Control Operativo</h1>
        <p>Bienvenido al sistema de gestión de inventario y reservas de R&amp;M Eventos &amp; Catering.</p>

        <div class="dash-grid">
            @if(Auth::user()->rol === 'admin')
            <article class="module-card">
                <h3>Usuarios y Roles (Sprint 1)</h3>
                <p>Alta, consulta, asignación de roles y desactivación de cuentas internas.</p>
                <a href="{{ url('/usuarios') }}" class="button hero-button">Gestionar usuarios</a>
            </article>
            @endif

            <article class="module-card">
                <h3>Catálogo y Stock (Sprint 2)</h3>
                <p>Control de mobiliario, decoración, entradas y salidas de almacén.</p>
                <span style="font-size:0.8rem; color:#888;">Próximo módulo (HU-09 / HU-02)</span>
            </article>

            <article class="module-card">
                <h3>Reservas y Bloqueos (Sprint 3)</h3>
                <p>Retención temporal de 10 minutos y confirmación de eventos.</p>
                <span style="font-size:0.8rem; color:#888;">Próximo módulo (HU-01 / HU-10)</span>
            </article>

            <article class="module-card">
                <h3>Mermas y Analítica IA (Sprints 4-5)</h3>
                <p>Registro de pérdidas, alertas mínimas y proyección de consumo.</p>
                <span style="font-size:0.8rem; color:#888;">Próximo módulo (HU-11 / HU-12)</span>
            </article>
        </div>
    </main>
</body>
</html>
