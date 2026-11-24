<!DOCTYPE html>
<html lang="es-PE">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Explora el mobiliario y la decoración de R&M Eventos y Catering. Consulta disponibilidad y arma la selección para tu celebración.">
  <meta name="theme-color" content="#17251e">
  <title>EventStock AI | R&amp;M Eventos &amp; Catering</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <script src="{{ asset('js/catalogo.js') }}" defer></script>
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body id="inicio">
<svg class="icon-set" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <symbol id="calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18m-13 5h2m4 0h2"/></symbol>
  <symbol id="bag" viewBox="0 0 24 24"><path d="M5 8h14l2 13H3L5 8Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></symbol>
  <symbol id="arrow" viewBox="0 0 24 24"><path d="M4 12h15m-6-6 6 6-6 6"/></symbol>
  <symbol id="arrow-up" viewBox="0 0 24 24"><path d="M6 18 18 6M7 6h11v11"/></symbol>
  <symbol id="layers" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5M3 16l9 5 9-5"/></symbol>
  <symbol id="spark" viewBox="0 0 24 24"><path d="M12 2c0 6-4 10-10 10 6 0 10 4 10 10 0-6 4-10 10-10-6 0-10-4-10-10Z"/></symbol>
  <symbol id="search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></symbol>
  <symbol id="plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
  <symbol id="minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
  <symbol id="close" viewBox="0 0 24 24"><path d="m6 6 12 12M6 18 18 6"/></symbol>
  <symbol id="check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
  <symbol id="download" viewBox="0 0 24 24"><path d="M12 3v12m-4-4 4 4 4-4M4 17v4h16v-4"/></symbol>
</svg>
<header class="site-header">
  <div class="layout header-inner">
    <a class="brand" href="#inicio" aria-label="R&M Eventos y Catering, inicio"><span class="brand-mark">R<span>&amp;</span>M</span><span class="brand-text">EVENTOS<br>&amp; CATERING</span></a>
    <nav aria-label="Principal">
      <a href="#catalogo">Catálogo</a>
      <a href="#como-funciona">Cómo reservar</a>
      <button type="button" data-open="reservas">Mis reservas</button>
      @auth
        <a href="{{ url('/dashboard') }}">Panel de gestión</a>
      @else
        <a href="{{ url('/login') }}">Ingresar</a>
      @endauth
    </nav>
    <button class="button outline cart-trigger" type="button" data-open="seleccion"><svg class="icon"><use href="#bag"/></svg><span>Mi selección</span><span id="cart-count" class="count">0</span></button>
  </div>
</header>
<main>
  <section class="hero" aria-labelledby="hero-title">
    <div class="layout hero-content">
      <p class="eyebrow hero-kicker"><span></span> ALQUILER PARA EVENTOS</p>
      <h1 id="hero-title">Cada detalle<br><em>hace el evento.</em></h1>
      <p class="hero-description">Mobiliario y decoración para bodas, celebraciones y eventos corporativos. Elige tus piezas y reserva para tu fecha.</p>
      <a class="button hero-button" href="#catalogo">Explorar catálogo <svg class="icon"><use href="#arrow"/></svg></a>
    </div>
    <div class="hero-caption"><span></span> MONTAJES DE INSPIRACIÓN</div>
  </section>
  <section class="features" id="como-funciona" aria-label="Cómo reservar">
    <div class="layout feature-grid">
      <article><svg class="icon"><use href="#calendar"/></svg><div><h2>Elige la fecha</h2><p>Revisa qué piezas están disponibles.</p></div></article>
      <article><svg class="icon"><use href="#layers"/></svg><div><h2>Arma tu selección</h2><p>Combina piezas y ajusta las cantidades.</p></div></article>
      <article><svg class="icon"><use href="#spark"/></svg><div><h2>Guarda tu reserva</h2><p>Descarga el detalle de tu evento.</p></div></article>
    </div>
  </section>
  <section class="layout catalog" id="catalogo" aria-labelledby="catalog-title">
    <div class="catalog-heading">
      <div><p class="eyebrow accent">MOBILIARIO Y DECORACIÓN</p><h2 id="catalog-title">La colección</h2><p>Encuentra las piezas para tu próximo evento.</p></div>
      <label class="date-field" for="event-date"><svg class="icon"><use href="#calendar"/></svg><span>Fecha del evento <input type="date" id="event-date" required></span></label>
    </div>
    <div class="catalog-controls">
      <div id="filters" class="filters" role="group" aria-label="Filtrar por tipo"></div>
      <label class="search"><svg class="icon"><use href="#search"/></svg><input type="search" id="catalog-search" placeholder="Buscar una pieza" aria-label="Buscar una pieza"></label>
    </div>
    <p id="results-count" class="results-count" aria-live="polite"></p>
    <div id="product-grid" class="product-grid"></div>
    <noscript><p>Activa JavaScript para ver el catálogo y registrar reservas.</p></noscript>
  </section>
  <section class="closing">
    <div class="layout closing-inner"><div><h2>¿Ya tienes fecha?</h2><p>Reúne tus piezas favoritas y prepara tu reserva.</p></div><button class="button hero-button" type="button" data-open="seleccion">Ver mi selección <svg class="icon"><use href="#arrow-up"/></svg></button></div>
  </section>
</main>
<footer><div class="layout footer-inner"><a class="brand" href="#inicio" aria-label="Volver al inicio"><span class="brand-mark">R<span>&amp;</span>M</span><span class="brand-text">EVENTOS<br>&amp; CATERING</span></a><p>EventStock AI · Gestión de inventario y reservas</p><button type="button" data-open="reservas">Mis reservas <svg class="icon"><use href="#arrow-up"/></svg></button></div></footer>
<dialog id="panel" aria-labelledby="panel-title">
  <div class="panel-shell">
    <header class="panel-header"><p class="eyebrow accent">R&amp;M EVENTOS</p><h2 id="panel-title"></h2><button id="close-panel" type="button" class="square" aria-label="Cerrar"><svg class="icon"><use href="#close"/></svg></button></header>
    <div id="panel-content" class="panel-content"></div>
  </div>
</dialog>
<div id="notice" class="notice" role="status" aria-live="polite"></div>
</body>
</html>
