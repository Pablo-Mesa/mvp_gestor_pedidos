/* ── State ── */
let cart = [];
let currentView = 'home';
let loggedIn = false;
let userName = '';

/* ── Sample Data ── */
const menuItems = [
  { id: 1, name: 'Milanesa napolitana', desc: 'Con puré cremoso y ensalada fresca', price: 3200, emoji: '🍗', cat: 'Clásicos' },
  { id: 2, name: 'Pollo al verdeo', desc: 'Con arroz integral y vegetales salteados', price: 2900, emoji: '🍚', cat: 'Clásicos' },
  { id: 3, name: 'Tarta de zapallitos', desc: 'Con ensalada mixta de estación', price: 2600, emoji: '🥧', cat: 'Vegetariano' },
  { id: 4, name: 'Pasta con pesto', desc: 'Fusilli al pesto genovés con parmesano', price: 2800, emoji: '🍝', cat: 'Vegetariano' },
  { id: 5, name: 'Bowl de salmón', desc: 'Arroz, palta, edamame y salsa teriyaki', price: 3800, emoji: '🐟', cat: 'Premium' },
  { id: 6, name: 'Wrap integral', desc: 'Pollo, hummus, rúcula y tomates secos', price: 2700, emoji: '🌯', cat: 'Livianos' },
];

const sampleOrders = [
  { id: 1001, date: '2025-01-10', items: ['Milanesa napolitana', 'Pasta con pesto'], total: 6000, status: 'Entregado' },
  { id: 1002, date: '2025-01-12', items: ['Bowl de salmón'], total: 3800, status: 'Entregado' },
];

const sampleAddresses = [
  { id: 1, label: 'Casa', address: 'Av. Corrientes 1234, CABA', default: true },
  { id: 2, label: 'Oficina', address: 'Libertador 5678, piso 3, CABA', default: false },
];

/* ── Config ── */
const defaultConfig = {
  brand_name: 'Vianda Express',
  hero_title: 'Comida casera, a tu puerta',
  hero_subtitle: 'Viandas frescas preparadas con amor cada día. Elegí tu menú, nosotros nos encargamos del resto.',
  cta_text: 'Ver menú del día',
  background_color: '#FDF6EC',
  surface_color: '#FFFFFF',
  text_color: '#2C1810',
  primary_action_color: '#C45D2C',
  secondary_action_color: '#7A8B6F',
};

/* ── Functions ── */

function applyConfig(config) {
  const el = (id) => document.getElementById(id);
  const brand = config.brand_name || defaultConfig.brand_name;
  if (el('brand-name')) el('brand-name').textContent = brand;
  if (el('footer-brand')) el('footer-brand').textContent = brand;

  document.documentElement.style.setProperty('--bg', config.background_color || defaultConfig.background_color);
  document.documentElement.style.setProperty('--surface', config.surface_color || defaultConfig.surface_color);
  document.documentElement.style.setProperty('--text', config.text_color || defaultConfig.text_color);
  document.documentElement.style.setProperty('--primary', config.primary_action_color || defaultConfig.primary_action_color);
  document.documentElement.style.setProperty('--secondary', config.secondary_action_color || defaultConfig.secondary_action_color);

  document.body.style.backgroundColor = config.background_color || defaultConfig.background_color;
  document.body.style.color = config.text_color || defaultConfig.text_color;

  if (currentView === 'home') renderHome(config);
}

function navigateTo(view) {
  currentView = view;
  const main = document.getElementById('main-content');
  if (!main) return;
  main.innerHTML = '';
  closeMobileNav();

  document.querySelectorAll('#desktop-nav .nav-link').forEach(l => l.classList.remove('active'));
  const navMap = { home: 0, menu: 1, history: 2, addresses: 3, billing: 4 };
  const links = document.querySelectorAll('#desktop-nav .nav-link');
  if (links[navMap[view]]) links[navMap[view]].classList.add('active');

  const cfg = (window.elementSdk && window.elementSdk.config) || defaultConfig;
  const views = { home: renderHome, menu: renderMenu, history: renderHistory, addresses: renderAddresses, billing: renderBilling, checkout: renderCheckout };
  (views[view] || renderHome)(cfg);
  window.scrollTo({ top: 0, behavior: 'smooth' });
  if (view !== 'home') toggleCart(true);
  setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
}

function renderHome(cfg) {
  const main = document.getElementById('main-content');
  const title = cfg.hero_title || defaultConfig.hero_title;
  const subtitle = cfg.hero_subtitle || defaultConfig.hero_subtitle;
  const cta = cfg.cta_text || defaultConfig.cta_text;

  const html = `
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24">
      <div class="max-w-2xl slide-up" style="margin-bottom: 4rem;">
        <span class="bg-spice-light text-spice text-xs font-bold uppercase rounded-full px-4 py-1" style="display: inline-block; margin-bottom: 1.5rem; letter-spacing: 1px;">Menú del día disponible</span>
        <h1 class="font-display text-4xl sm:text-6xl leading-tight mb-6" id="hero-title">${title}</h1>
        <p class="text-bark text-lg sm:text-xl mb-8 leading-relaxed" style="opacity: 0.6;">${subtitle}</p>
        <div class="flex flex-wrap gap-3">
          <button onclick="navigateTo('menu')" class="btn btn-primary" id="hero-cta">
            <i data-lucide="utensils" class="w-4 h-4"></i>${cta}
          </button>
          <button onclick="navigateTo('history')" class="btn btn-secondary">Mis pedidos</button>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        ${[['🕐', 'Listo en 30 min', 'Preparamos todo fresco y lo enviamos rápido.'],['🥗', 'Ingredientes frescos', 'Trabajamos con productores locales de confianza.'],['🚴', 'Entrega a domicilio', 'Te llevamos el almuerzo donde estés.']].map((f, i) => `
          <div class="bg-white rounded-2xl p-6 shadow-sm slide-up stagger-${i+1}">
            <div class="text-3xl" style="margin-bottom: 1rem;">${f[0]}</div>
            <h3 class="font-display text-lg" style="margin-bottom: 0.5rem;">${f[1]}</h3>
            <p class="text-bark/50 text-sm">${f[2]}</p>
          </div>`).join('')}
      </div>
      <div class="mt-20 slide-up stagger-4">
        <h2 class="font-display text-2xl sm:text-3xl mb-8">Los más pedidos</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
          ${menuItems.slice(0, 3).map(item => menuCard(item)).join('')}
        </div>
      </div>
    </section>`;
  main.innerHTML = html;
  if (window.lucide) lucide.createIcons();
}

function menuCard(item) {
  return `
    <div class="menu-card bg-white rounded-2xl p-5 shadow-sm cursor-default">
      <span class="text-4xl block mb-3">${item.emoji}</span>
      <span class="text-xs font-bold uppercase tracking-wider text-sage">${item.cat}</span>
      <h3 class="font-display text-lg" style="margin: 0.5rem 0 0.25rem 0;">${item.name}</h3>
      <p class="text-bark/50 text-sm" style="margin-bottom: 1.5rem;">${item.desc}</p>
      <div class="flex items-center justify-between">
        <span class="font-bold text-lg">$${item.price.toLocaleString()}</span>
        <button onclick="addToCart(${item.id})" class="btn btn-primary btn-sm">
          <i data-lucide="plus" class="w-4 h-4"></i>Agregar
        </button>
      </div>
    </div>`;
}

function renderMenu() {
  const cats = [...new Set(menuItems.map(i => i.cat))];
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
      <h1 class="font-display text-3xl sm:text-4xl mb-2 slide-up">Nuestro menú</h1>
      <p class="text-bark/50 mb-8 slide-up stagger-1">Elegí tus viandas del día</p>
      <div class="flex flex-wrap gap-2 mb-8 slide-up stagger-2">
        <button onclick="filterMenu('all')" class="menu-filter bg-bark text-cream px-4 py-2 rounded-full text-sm font-medium transition">Todos</button>
        ${cats.map(c => `<button onclick="filterMenu('${c}')" class="menu-filter border border-bark/15 px-4 py-2 rounded-full text-sm font-medium hover:border-bark/30 transition">${c}</button>`).join('')}
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="menu-grid">${menuItems.map(i => menuCard(i)).join('')}</div>
    </section>`;
}

window.filterMenu = function(cat) {
  const grid = document.getElementById('menu-grid');
  if (!grid) return;
  const filtered = (cat === 'all' || cat === 'Todos') ? menuItems : menuItems.filter(i => i.cat === cat);
  grid.innerHTML = filtered.map(i => menuCard(i)).join('');
  document.querySelectorAll('.menu-filter').forEach(b => {
    b.className = b.textContent.trim() === cat || (cat === 'all' && b.textContent.trim() === 'Todos')
      ? 'menu-filter bg-bark text-cream px-4 py-2 rounded-full text-sm font-medium transition'
      : 'menu-filter border border-bark/15 px-4 py-2 rounded-full text-sm font-medium hover:border-bark/30 transition';
  });
  lucide.createIcons();
};

function renderHistory() {
  const main = document.getElementById('main-content');
  main.innerHTML = `<section class="max-w-4xl mx-auto px-4 sm:px-6 py-10"><h1 class="font-display text-3xl mb-8 slide-up">Historial de pedidos</h1>${sampleOrders.length ? sampleOrders.map((o, i) => `<div class="bg-white rounded-2xl p-5 mb-4 shadow-sm slide-up stagger-${i+1}"><div class="flex flex-wrap items-center justify-between gap-2 mb-3"><div><span class="font-bold">#${o.id}</span><span class="text-bark/40 text-sm ml-2">${o.date}</span></div><span class="bg-sage-light text-sage text-xs font-bold px-3 py-1 rounded-full">${o.status}</span></div><p class="text-bark/60 text-sm mb-2">${o.items.join(', ')}</p><p class="font-bold">$${o.total.toLocaleString()}</p></div>`).join('') : '<p class="text-bark/40">Aún no tenés pedidos. ¡Hacé tu primer pedido!</p>'}</section>`;
}

function renderAddresses() {
  const main = document.getElementById('main-content');
  main.innerHTML = `<section class="max-w-4xl mx-auto px-4 sm:px-6 py-10"><div class="flex items-center justify-between mb-8 slide-up"><h1 class="font-display text-3xl">Mis direcciones</h1><button onclick="showAddAddressForm()" class="btn btn-primary btn-sm"><i data-lucide="plus" class="w-4 h-4"></i>Agregar</button></div><div id="address-list">${sampleAddresses.map((a, i) => `<div class="bg-white rounded-2xl p-5 mb-4 shadow-sm slide-up stagger-${i+1} flex items-start justify-between gap-4"><div><div class="flex items-center gap-2 mb-1"><i data-lucide="map-pin" class="w-4 h-4 text-spice"></i><span class="font-bold">${a.label}</span>${a.default ? '<span class="bg-sage-light text-sage text-xs font-bold px-2 py-0.5 rounded-full">Principal</span>' : ''}</div><p class="text-bark/60 text-sm">${a.address}</p></div><button class="text-bark/30 hover:text-bark transition" style="background:none; border:none; cursor:pointer;"><i data-lucide="edit" class="w-4 h-4"></i></button></div>`).join('')}</div><div id="add-address-form" class="hidden bg-white rounded-2xl p-6 shadow-sm mt-4 fade-in"><h3 class="font-display text-lg mb-4">Nueva dirección</h3><form onsubmit="saveAddress(event)"><div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4"><div><label class="text-sm font-medium block mb-1">Etiqueta</label><input type="text" placeholder="Ej: Casa" class="input-std" required></div><div><label class="text-sm font-medium block mb-1">Dirección</label><input type="text" placeholder="Calle, número, piso" class="input-std" required></div></div><div class="flex gap-3"><button type="submit" class="btn btn-primary btn-sm">Guardar</button><button type="button" onclick="document.getElementById('add-address-form').classList.add('hidden')" class="btn btn-secondary btn-sm">Cancelar</button></div></form></div></section>`;
  lucide.createIcons();
}

window.showAddAddressForm = () => document.getElementById('add-address-form')?.classList.remove('hidden');
window.saveAddress = (e) => { e.preventDefault(); document.getElementById('add-address-form').classList.add('hidden'); showToast('Dirección guardada'); };

function renderBilling() {
  const main = document.getElementById('main-content');
  main.innerHTML = `<section class="max-w-4xl mx-auto px-4 sm:px-6 py-10"><h1 class="font-display text-3xl mb-8 slide-up">Datos de facturación</h1><form onsubmit="saveBilling(event)" class="bg-white rounded-2xl p-6 shadow-sm slide-up stagger-1"><div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6"><div><label class="text-sm font-medium block mb-1">Nombre / Razón social</label><input type="text" value="Juan Pérez" class="input-std"></div><div><label class="text-sm font-medium block mb-1">CUIT / DNI</label><input type="text" value="20-12345678-9" class="input-std"></div><div><label class="text-sm font-medium block mb-1">Dirección fiscal</label><input type="text" value="Av. Corrientes 1234, CABA" class="input-std"></div><div><label class="text-sm font-medium block mb-1">Condición IVA</label><select class="input-std bg-white"><option>Consumidor Final</option><option>Responsable Inscripto</option><option>Monotributista</option><option>Exento</option></select></div><div class="sm:col-span-2"><label class="text-sm font-medium block mb-1">Email para facturas</label><input type="email" value="juan@email.com" class="input-std"></div></div><button type="submit" class="btn btn-primary">Guardar cambios</button></form></section>`;
}

window.saveBilling = (e) => { e.preventDefault(); showToast('Datos de facturación actualizados'); };

function renderCheckout() {
  if (!cart.length) { navigateTo('menu'); return; }
  const total = cart.reduce((s, c) => s + c.item.price * c.qty, 0);
  const main = document.getElementById('main-content');
  main.innerHTML = `<section class="max-w-4xl mx-auto px-4 sm:px-6 py-10"><h1 class="font-display text-3xl mb-8 slide-up">Confirmar pedido</h1><div class="grid grid-cols-1 lg:grid-cols-5 gap-6"><div class="lg:col-span-3 space-y-4 slide-up stagger-1"><div class="bg-white rounded-2xl p-6 shadow-sm"><h2 class="font-display text-lg mb-4">Dirección de entrega</h2>${sampleAddresses.map(a => `<label class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream/50 cursor-pointer transition"><input type="radio" name="addr" ${a.default ? 'checked' : ''} class="accent-spice"><div><span class="font-medium text-sm">${a.label}</span><p class="text-bark/50 text-xs">${a.address}</p></div></label>`).join('')}</div><div class="bg-white rounded-2xl p-6 shadow-sm"><h2 class="font-display text-lg mb-4">Método de pago</h2>${['Efectivo','Transferencia','Mercado Pago'].map((m, i) => `<label class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream/50 cursor-pointer transition"><input type="radio" name="pay" ${i === 0 ? 'checked' : ''} class="accent-spice"><span class="text-sm font-medium">${m}</span></label>`).join('')}</div></div><div class="lg:col-span-2 slide-up stagger-2"><div class="bg-white rounded-2xl p-6 shadow-sm sticky top-20"><h2 class="font-display text-lg mb-4">Resumen</h2>${cart.map(c => `<div class="flex justify-between text-sm mb-2"><span>${c.qty}× ${c.item.name}</span><span class="font-medium">$${(c.item.price * c.qty).toLocaleString()}</span></div>`).join('')}<hr class="my-3 border-sand"><div class="flex justify-between font-bold text-lg"><span>Total</span><span>$${total.toLocaleString()}</span></div><button onclick="placeOrder()" class="btn btn-primary w-full mt-4">Realizar pedido</button></div></div></div></section>`;
  if (window.lucide) lucide.createIcons();
}

window.placeOrder = () => {
  cart = []; updateCartBadge();
  const main = document.getElementById('main-content');
  main.innerHTML = `<section class="max-w-lg mx-auto px-4 py-20 text-center slide-up"><span class="text-6xl block mb-4">🎉</span><h1 class="font-display text-3xl mb-3">¡Pedido confirmado!</h1><p class="text-bark/50 mb-8">Tu vianda está en preparación. Te avisaremos cuando salga a camino.</p><button onclick="navigateTo('home')" class="btn btn-primary">Volver al inicio</button></section>`;
};

function addToCart(id) {
  const item = menuItems.find(i => i.id === id);
  if (!item) return;
  const existing = cart.find(c => c.item.id === id);
  if (existing) existing.qty++; else cart.push({ item, qty: 1 });
  updateCartBadge(); showToast(`${item.name} agregado`);
}

function updateCartBadge() {
  const badge = document.getElementById('cart-badge');
  const count = cart.reduce((s, c) => s + c.qty, 0);
  if (count > 0) { badge.textContent = count; badge.classList.remove('hidden'); badge.classList.add('flex'); }
  else { badge.classList.add('hidden'); badge.classList.remove('flex'); }
}

function renderCartDrawer() {
  const container = document.getElementById('cart-items');
  const footer = document.getElementById('cart-footer');
  if (!cart.length) {
    container.innerHTML = '<div class="h-full flex flex-col items-center justify-center text-bark/30"><i data-lucide="shopping-bag" class="w-12 h-12 mb-3"></i><p>Tu carrito está vacío</p></div>';
    footer.classList.add('hidden');
    if (window.lucide) lucide.createIcons(); return;
  }
  container.innerHTML = cart.map(c => `<div class="flex items-center gap-4 mb-4 pb-4 border-b border-sand/60 last:border-0"><span class="text-3xl">${c.item.emoji}</span><div class="flex-1 min-w-0"><p class="font-medium text-sm truncate">${c.item.name}</p><p class="text-bark/50 text-xs">$${c.item.price.toLocaleString()} c/u</p></div><div class="flex items-center gap-2"><button onclick="changeQty(${c.item.id},-1)" class="w-7 h-7 rounded-lg border border-bark/15 flex items-center justify-center text-sm hover:bg-sand/50 transition">−</button><span class="text-sm font-medium w-5 text-center">${c.qty}</span><button onclick="changeQty(${c.item.id},1)" class="w-7 h-7 rounded-lg border border-bark/15 flex items-center justify-center text-sm hover:bg-sand/50 transition">+</button></div></div>`).join('');
  const total = cart.reduce((s, c) => s + c.item.price * c.qty, 0);
  document.getElementById('cart-total').textContent = `$${total.toLocaleString()}`;
  footer.classList.remove('hidden');
}

window.changeQty = (id, delta) => {
  const c = cart.find(c => c.item.id === id);
  if (!c) return;
  c.qty += delta; if (c.qty <= 0) cart = cart.filter(c => c.item.id !== id);
  updateCartBadge(); renderCartDrawer();
};

function toggleCart(forceClose) {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  const isOpen = drawer.classList.contains('translate-x-0');
  if (forceClose === true && !isOpen) return;
  if (isOpen || forceClose === true) { drawer.classList.remove('translate-x-0'); overlay.classList.add('hidden'); }
  else { renderCartDrawer(); drawer.classList.add('translate-x-0'); overlay.classList.remove('hidden'); }
}

function buildMobileNav() {
  const links = [['home', 'Inicio', 'home'], ['menu', 'Menú', 'utensils'], ['history', 'Pedidos', 'clipboard-list'], ['addresses', 'Direcciones', 'map-pin'], ['billing', 'Facturación', 'receipt']];
  document.getElementById('mobile-nav-links').innerHTML = links.map(l => `<a href="#" onclick="navigateTo('${l[0]}');return false" class="flex items-center gap-3 py-3 text-sm font-medium hover:text-spice transition"><i data-lucide="${l[2]}" class="w-5 h-5"></i>${l[1]}</a>`).join('') + `<hr class="border-sand my-2"><a href="#" onclick="openAuth('login');closeMobileNav();return false" class="flex items-center gap-3 py-3 text-sm font-medium hover:text-spice transition"><i data-lucide="user" class="w-5 h-5"></i>Ingresar</a>`;
  if (window.lucide) lucide.createIcons();
}

function toggleMobileNav() {
  const nav = document.getElementById('mobile-nav');
  const overlay = document.getElementById('mobile-nav-overlay');
  const isOpen = nav.classList.contains('translate-x-0');
  if (isOpen) { nav.classList.remove('translate-x-0'); overlay.classList.add('hidden'); }
  else { nav.classList.add('translate-x-0'); overlay.classList.remove('hidden'); }
}
function closeMobileNav() {
  document.getElementById('mobile-nav').classList.remove('translate-x-0');
  document.getElementById('mobile-nav-overlay').classList.add('hidden');
}

function openAuth(type) {
  const modal = document.getElementById('auth-modal');
  const content = document.getElementById('auth-content');
  if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
  if (type === 'login') {
    content.innerHTML = `<h2 class="font-display text-2xl mb-6">Bienvenido</h2><form onsubmit="handleLogin(event)"><div style="margin-bottom: 1rem;"><label class="text-sm font-medium block mb-1">Email</label><input type="email" placeholder="tu@email.com" class="input-std" required></div><div style="margin-bottom: 1.5rem;"><label class="text-sm font-medium block mb-1">Contraseña</label><input type="password" placeholder="••••••••" class="input-std" required></div><button type="submit" class="btn btn-primary w-full" style="margin-bottom: 1rem;">Ingresar</button><p class="text-center text-sm text-bark/50">¿No tenés cuenta? <a href="#" onclick="openAuth('register');return false" class="text-spice font-medium">Registrate</a></p></form>`;
  } else {
    content.innerHTML = `<h2 class="font-display text-2xl mb-6">Crear cuenta</h2><form onsubmit="handleRegister(event)"><div style="margin-bottom: 1rem;"><label class="text-sm font-medium block mb-1">Nombre</label><input type="text" placeholder="Tu nombre" class="input-std" required></div><div style="margin-bottom: 1rem;"><label class="text-sm font-medium block mb-1">Email</label><input type="email" placeholder="tu@email.com" class="input-std" required></div><div style="margin-bottom: 1rem;"><label class="text-sm font-medium block mb-1">Teléfono</label><input type="tel" placeholder="+54 11 2345-6789" class="input-std"></div><div style="margin-bottom: 1.5rem;"><label class="text-sm font-medium block mb-1">Contraseña</label><input type="password" placeholder="Mínimo 8 caracteres" class="input-std" required></div><button type="submit" class="btn btn-primary w-full" style="margin-bottom: 1rem;">Crear cuenta</button><p class="text-center text-sm text-bark/50">¿Ya tenés cuenta? <a href="#" onclick="openAuth('login');return false" class="text-spice font-medium">Ingresá</a></p></form>`;
  }
}

function closeAuth() {
  const modal = document.getElementById('auth-modal');
  modal.classList.add('hidden'); modal.classList.remove('flex');
}

window.handleLogin = (e) => { e.preventDefault(); loggedIn = true; userName = 'Juan'; closeAuth(); showToast('¡Bienvenido, Juan!'); updateAuthUI(); };
window.handleRegister = (e) => { e.preventDefault(); loggedIn = true; userName = e.target.querySelector('input').value || 'Usuario'; closeAuth(); showToast(`¡Cuenta creada! Bienvenido, ${userName}`); updateAuthUI(); };

function updateAuthUI() {
  const btn = document.getElementById('auth-btn');
  if (loggedIn) btn.innerHTML = `<i data-lucide="user" class="w-4 h-4"></i><span>${userName}</span>`;
  else btn.innerHTML = `<i data-lucide="user" class="w-4 h-4"></i><span>Ingresar</span>`;
  lucide.createIcons();
}

function showToast(msg) {
  const toast = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  toast.classList.remove('hidden');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
}

/* ── Initializers ── */
document.addEventListener('DOMContentLoaded', () => {
  buildMobileNav();
  navigateTo('home');
  
  try {
    if (window.elementSdk && typeof window.elementSdk.init === 'function') {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange: async (config) => applyConfig(config),
      });
    }
  } catch (e) { console.warn("SDK no disponible"); }
});