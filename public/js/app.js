'use strict';

const STORAGE = { bookings: 'rm-eventos.reservas.v1', cart: 'rm-eventos.seleccion.v1' };
const PRODUCTS = new Map(CATALOGO.map(product => [product.id, product]));
const CATEGORIES = ['Todos', ...new Set(CATALOGO.map(product => product.categoria))];
const currency = new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' });
const calendarDay = new Intl.DateTimeFormat('sv-SE', { timeZone: 'America/Lima', year: 'numeric', month: '2-digit', day: '2-digit' });
const displayDay = new Intl.DateTimeFormat('es-PE', { dateStyle: 'long', timeZone: 'UTC' });
const eventTypes = ['Boda', 'Cumpleaños', 'Evento corporativo', 'Otro'];
const today = () => calendarDay.format(new Date());
const dateLabel = date => displayDay.format(new Date(date + 'T12:00:00Z'));
const money = amount => currency.format(amount);
const icon = name => '<svg class="icon" aria-hidden="true"><use href="#' + name + '"/></svg>';
const escapeHTML = value => String(value ?? '').replace(/[&<>"']/g, char => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
})[char]);
const normalize = value => value.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();
const $ = selector => document.querySelector(selector);

const dateInput = $('#event-date');
const searchInput = $('#catalog-search');
const filters = $('#filters');
const grid = $('#product-grid');
const dialog = $('#panel');
const panelBody = $('#panel-content');
const notice = $('#notice');
const initial = readStorage(STORAGE.cart, {});
let bookings = readStorage(STORAGE.bookings, []);
let cart = (initial.items ?? []).filter(item => PRODUCTS.has(item.id) && Number.isInteger(item.qty) && item.qty > 0);
let date = initial.date >= today() ? initial.date : today();
let filter = 'Todos';
let panel = '';
let draft = {};
let noticeTimer;
let busy = false;

function readStorage(key, fallback) {
  try {
    return JSON.parse(localStorage.getItem(key)) ?? fallback;
  } catch {
    return fallback;
  }
}

function saveCart() {
  try {
    localStorage.setItem(STORAGE.cart, JSON.stringify({ date, items: cart }));
  } catch {
    // La selección sigue disponible en esta pestaña.
  }
}

function available(product) {
  const reserved = bookings.reduce((count, booking) => {
    if (booking.date !== date || booking.cancelled) return count;
    return count + booking.items.reduce((sum, item) => sum + (item.id === product.id ? item.qty : 0), 0);
  }, 0);
  return Math.max(0, product.stock - reserved);
}

function quantity(id) {
  return cart.find(item => item.id === id)?.qty ?? 0;
}

function totalPrice(items) {
  return items.reduce((sum, item) => sum + item.qty * item.price, 0);
}

function alertUser(message) {
  notice.textContent = message;
  clearTimeout(noticeTimer);
  noticeTimer = setTimeout(() => { notice.textContent = ''; }, 2700);
}

function card(product) {
  const remaining = available(product);
  const units = remaining === 1 ? product.unidad + ' disponible' : product.unidad + (product.unidad === 'unidad' ? 'es' : 's') + ' disponibles';
  const selected = quantity(product.id);
  const stockMessage = remaining ? remaining + ' ' + units : 'Agotado para la fecha';
  const addLabel = selected ? 'Añadir (' + selected + ')' : 'Añadir';
  return `<article class="product">
    <div class="product-photo"><img src="assets/${escapeHTML(product.foto)}" alt="${escapeHTML(product.nombre)}" width="500" height="300" loading="lazy"><span class="stock ${remaining ? '' : 'sold-out'}">${stockMessage}</span></div>
    <div class="product-body">
      <p class="eyebrow accent">${escapeHTML(product.categoria)}</p><h3>${escapeHTML(product.nombre)}</h3><p class="product-description">${escapeHTML(product.descripcion)}</p>
      <div class="product-bottom"><p class="price">${money(product.precio)} <small>/ ${escapeHTML(product.unidad)}</small></p>
      <button type="button" class="button add-piece" data-add="${escapeHTML(product.id)}" aria-label="${escapeHTML(addLabel + ' ' + product.nombre)}"${selected >= remaining ? ' disabled' : ''}>${icon('plus')}${addLabel}</button></div>
    </div></article>`;
}

function renderCatalog() {
  const query = normalize(searchInput.value.trim());
  const matches = CATALOGO.filter(product => (
    (filter === 'Todos' || product.categoria === filter) &&
    normalize([product.nombre, product.categoria, product.descripcion].join(' ')).includes(query)
  ));
  grid.innerHTML = matches.length ? matches.map(card).join('') : '<div class="empty-state"><p>No encontramos piezas. Prueba con otra búsqueda o categoría.</p></div>';
  $('#results-count').textContent = matches.length === 1 ? '1 pieza' : matches.length + ' piezas';
  $('#cart-count').textContent = cart.reduce((total, item) => total + item.qty, 0);
}

function setDate(value) {
  dateInput.min = today();
  date = value && value >= dateInput.min ? value : dateInput.min;
  dateInput.value = date;
  saveCart();
  renderCatalog();
}

function changeQuantity(id, delta) {
  const product = PRODUCTS.get(id);
  if (!product) return;
  const next = quantity(id) + delta;
  if (delta > 0 && next > available(product)) return;
  cart = cart.filter(item => item.id !== id);
  if (next > 0) {
    // Conserva el orden del catálogo en el resumen.
    cart.push({ id, qty: next });
    cart.sort((a, b) => CATALOGO.indexOf(PRODUCTS.get(a.id)) - CATALOGO.indexOf(PRODUCTS.get(b.id)));
  }
  saveCart();
  renderCatalog();
}

function control(id, delta, label, disabled = false) {
  return `<button type="button" class="square" data-product="${escapeHTML(id)}" data-delta="${delta}" aria-label="${escapeHTML(label)}"${disabled ? ' disabled' : ''}>${icon(delta > 0 ? 'plus' : 'minus')}</button>`;
}

function renderCart() {
  if (!cart.length) {
    panelBody.innerHTML = '<div class="empty-state"><p>Tu selección está vacía.</p><button class="button primary" type="button" data-action="catalogo">Explorar catálogo</button></div>';
    return;
  }
  const overstock = cart.some(item => item.qty > available(PRODUCTS.get(item.id)));
  const items = cart.map(({ id, qty }) => {
    const product = PRODUCTS.get(id);
    const left = available(product);
    return `<li class="cart-line"><img src="assets/${escapeHTML(product.foto)}" alt=""><div><strong>${escapeHTML(product.nombre)}</strong>
      <small>${money(qty * product.precio)}${qty > left ? ' · Stock para la fecha: ' + left : ''}</small></div>
      <div class="qty">${control(id, -1, 'Quitar una unidad de ' + product.nombre)}<span>${qty}</span>${control(id, 1, 'Añadir una unidad de ' + product.nombre, qty >= left)}</div></li>`;
  }).join('');
  panelBody.innerHTML = `<ul class="cart-list" aria-label="Piezas de la selección">${items}</ul>
    <div class="total"><span>Total referencial</span><strong>${money(totalPrice(cart.map(item => ({ ...item, price: PRODUCTS.get(item.id).precio }))))}</strong></div>
    <form class="booking-form" id="booking-form">
      <div class="split-fields">
        <label>Fecha del evento<input name="date" type="date" required min="${today()}" value="${date}"></label>
        <label>Tipo de evento<select name="type" required><option value="">Seleccionar</option>
        ${eventTypes.map(type => `<option${type === draft.type ? ' selected' : ''}>${type}</option>`).join('')}</select></label>
      </div>
      <label>Nombre<input name="name" required maxlength="80" autocomplete="name" placeholder="Nombre y apellido" value="${escapeHTML(draft.name)}"></label>
      <label>Teléfono (opcional)<input name="phone" type="tel" maxlength="30" autocomplete="tel" placeholder="Número de contacto" value="${escapeHTML(draft.phone)}"></label>
      <label>Lugar del evento (opcional)<input name="place" maxlength="100" placeholder="Salón o distrito" value="${escapeHTML(draft.place)}"></label>
      <p class="form-error" id="booking-error" role="alert">${overstock ? 'Ajusta las cantidades que superan el stock de la fecha para continuar.' : ''}</p>
      <button type="submit" class="button primary"${overstock ? ' disabled' : ''}>Guardar reserva ${icon('arrow')}</button>
      <p class="form-note">Reserva de demostración. Se guarda en este navegador.</p>
    </form>`;
}

function renderBookings(highlight = '') {
  if (!bookings.length) {
    panelBody.innerHTML = '<div class="empty-state"><p>Todavía no tienes reservas guardadas.</p><button class="button primary" type="button" data-action="catalogo">Explorar catálogo</button></div>';
    return;
  }
  panelBody.innerHTML = `${highlight ? '<p class="panel-note" style="margin-bottom: 12px;" role="status">Reserva guardada. Puedes descargar el detalle.</p>' : ''}
    <ul class="booking-list">${bookings.map(booking => `<li class="booking-card">
      <div class="booking-meta"><span class="booking-status ${booking.cancelled ? 'cancelled' : ''}">${booking.cancelled ? 'Cancelada' : 'Guardada'}</span><span>${escapeHTML(booking.id)}</span></div>
      <h3>${escapeHTML(booking.type)}</h3><p>${escapeHTML(dateLabel(booking.date))} · ${money(totalPrice(booking.items))}</p><p>${escapeHTML(booking.name)}${booking.place ? ' · ' + escapeHTML(booking.place) : ''}</p>
      <p class="panel-note">${booking.items.map(item => item.qty + ' × ' + escapeHTML(item.name)).join(' · ')}</p>
      <div class="booking-actions"><button type="button" class="button outline" data-action="download" data-id="${escapeHTML(booking.id)}">${icon('download')} Descargar detalle</button>
      ${booking.cancelled ? '' : `<button type="button" class="button outline" data-action="cancel" data-id="${escapeHTML(booking.id)}">Cancelar reserva</button>`}</div>
    </li>`).join('')}</ul><p id="history-message" class="form-error" role="alert"></p>`;
}

function openPanel(view, highlight = '') {
  panel = view;
  $('#panel-title').textContent = view === 'reservas' ? 'Mis reservas' : 'Mi selección';
  if (view === 'reservas') renderBookings(highlight);
  else renderCart();
  if (!dialog.open) dialog.showModal();
  dialog.scrollTop = 0;
  $('#close-panel').focus();
}

function writeBookings(operation) {
  const update = () => {
    const latest = JSON.parse(localStorage.getItem(STORAGE.bookings)) ?? [];
    bookings = latest;
    const updated = operation(latest);
    localStorage.setItem(STORAGE.bookings, JSON.stringify(updated));
    bookings = updated;
  };
  // Coordina las escrituras entre pestañas cuando el navegador admite Web Locks.
  return navigator.locks ? navigator.locks.request(STORAGE.bookings, update) : Promise.resolve().then(update);
}

async function book(form) {
  if (busy) return;
  const data = Object.fromEntries(new FormData(form));
  const name = data.name.trim();
  const error = $('#booking-error');
  if (!name) {
    error.textContent = 'Escribe el nombre de la persona que realiza la reserva.';
    form.elements.name.focus();
    return;
  }
  if (data.date < today() || !eventTypes.includes(data.type)) {
    error.textContent = 'Selecciona una fecha vigente y un tipo de evento.';
    return;
  }
  if (date !== data.date || !cart.length) {
    error.textContent = 'Revisa la fecha y las piezas antes de guardar.';
    return;
  }
  const bytes = crypto.getRandomValues(new Uint8Array(5));
  const id = 'RM-' + Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('').toUpperCase();
  const booking = {
    id, date, name, phone: data.phone.trim(), place: data.place.trim(), type: data.type, cancelled: false,
    items: cart.map(item => ({ ...item, name: PRODUCTS.get(item.id).nombre, price: PRODUCTS.get(item.id).precio }))
  };
  busy = true;
  const submit = form.querySelector('[type="submit"]');
  submit.disabled = true;
  try {
    await writeBookings(latest => {
      if (booking.date !== date || booking.items.some(item => item.qty > available(PRODUCTS.get(item.id)))) {
        throw new RangeError('Stock');
      }
      return [booking, ...latest];
    });
    cart = [];
    draft = {};
    saveCart();
    renderCatalog();
    if (dialog.open && panel === 'seleccion') openPanel('reservas', booking.id);
    else alertUser('Reserva guardada · ' + booking.id);
  } catch (err) {
    if (err instanceof RangeError) {
      renderCatalog();
      if (dialog.open && panel === 'seleccion') renderCart();
    } else {
      error.textContent = 'El navegador no pudo guardar la reserva. Revisa si permite almacenamiento local.';
      submit.disabled = false;
    }
  } finally {
    busy = false;
  }
}

function download(booking) {
  const contents = [
    'R&M Eventos y Catering', 'Detalle de reserva local', '',
    'Código: ' + booking.id, 'Estado: ' + (booking.cancelled ? 'Cancelada' : 'Guardada'),
    'Fecha: ' + dateLabel(booking.date), 'Evento: ' + booking.type, 'Nombre: ' + booking.name,
    booking.phone && 'Teléfono: ' + booking.phone, booking.place && 'Lugar: ' + booking.place,
    '\nPiezas',
    ...booking.items.map(item => item.qty + ' × ' + item.name + ' — ' + money(item.price * item.qty)),
    '\nTotal referencial: ' + money(totalPrice(booking.items)), '',
    'Documento de demostración. Reserva guardada en este navegador.'
  ].filter(line => line !== false).join('\r\n');
  const url = URL.createObjectURL(new Blob(['\uFEFF', contents], { type: 'text/plain;charset=utf-8' }));
  const link = document.createElement('a');
  link.href = url;
  link.download = booking.id + '.txt';
  document.body.append(link);
  link.click();
  link.remove();
  setTimeout(() => URL.revokeObjectURL(url), 1000);
}

async function cancelBooking(booking) {
  if (!window.confirm('¿Cancelar la reserva ' + booking.id + ' y liberar sus piezas?')) return;
  try {
    await writeBookings(latest => latest.map(item => item.id === booking.id ? { ...item, cancelled: true } : item));
    renderCatalog();
    if (dialog.open && panel === 'reservas') {
      renderBookings();
      $('#close-panel').focus();
    }
  } catch {
    const error = $('#history-message');
    if (error) error.textContent = 'No pudimos guardar la cancelación. Inténtalo de nuevo.';
  }
}

filters.innerHTML = CATEGORIES.map((category, index) => `<button type="button" class="filter" data-filter="${index}" aria-pressed="${index === 0}">${escapeHTML(category)}</button>`).join('');
filters.addEventListener('click', event => {
  const button = event.target.closest('[data-filter]');
  if (!button) return;
  filter = CATEGORIES[Number(button.dataset.filter)];
  filters.querySelectorAll('button').forEach(item => item.setAttribute('aria-pressed', item === button));
  renderCatalog();
});
searchInput.addEventListener('input', renderCatalog);
dateInput.addEventListener('change', () => setDate(dateInput.value));
grid.addEventListener('click', event => {
  const button = event.target.closest('[data-add]');
  if (!button) return;
  const id = button.dataset.add;
  changeQuantity(id, 1);
  const replacement = grid.querySelector('[data-add="' + CSS.escape(id) + '"]');
  if (replacement.disabled) $('.cart-trigger').focus({ preventScroll: true });
  else replacement.focus({ preventScroll: true });
  alertUser(PRODUCTS.get(id).nombre + ' en tu selección.');
});

document.addEventListener('click', event => {
  const trigger = event.target.closest('[data-open]');
  if (trigger) openPanel(trigger.dataset.open);
});
$('#close-panel').addEventListener('click', () => dialog.close());
dialog.addEventListener('click', event => {
  if (event.target === dialog) dialog.close();
});
panelBody.addEventListener('input', event => {
  if (event.target.form?.id === 'booking-form' && event.target.name !== 'date') {
    draft[event.target.name] = event.target.value;
  }
});
panelBody.addEventListener('change', event => {
  if (event.target.name === 'date' && event.target.validity.valid) {
    setDate(event.target.value);
    renderCart();
    panelBody.querySelector('[name="date"]')?.focus({ preventScroll: true });
  }
});
panelBody.addEventListener('submit', event => {
  if (event.target.id !== 'booking-form') return;
  event.preventDefault();
  book(event.target);
});
panelBody.addEventListener('click', event => {
  const button = event.target.closest('button');
  if (!button) return;
  const { action, id, product, delta } = button.dataset;
  if (product && !busy) {
    changeQuantity(product, Number(delta));
    renderCart();
    const matching = panelBody.querySelector(`[data-product="${CSS.escape(product)}"][data-delta="${delta}"]:enabled`);
    (matching ?? panelBody.querySelector('.qty button:enabled') ?? panelBody.querySelector('[data-action="catalogo"]'))?.focus({ preventScroll: true });
  }
  if (action === 'catalogo') {
    dialog.close();
    location.hash = 'catalogo';
    $('#catalogo').scrollIntoView();
  }
  const booking = id && bookings.find(item => item.id === id);
  if (booking && action === 'download') download(booking);
  if (booking && action === 'cancel' && !busy) cancelBooking(booking);
});

window.addEventListener('storage', event => {
  if (event.key !== STORAGE.bookings && event.key !== null) return;
  bookings = readStorage(STORAGE.bookings, []);
  renderCatalog();
  if (!dialog.open) return;
  if (panel === 'reservas') renderBookings();
  if (panel === 'seleccion' && !busy) renderCart();
});

setDate(date);
