const CACHE_NAME = 'solver-v1';
const ASSETS = [
  'index.php',
  'css/client_layout.css',
  'js/tool-kit-v002.js',
  'assets/icono_solver_nobg.png'
];

// Instalación: Cachear archivos críticos
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
  );
});

// Activación: Limpiar caches viejos
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))))
  );
});

// Estrategia de carga: Cache First, luego Network
self.addEventListener('fetch', e => {
  e.respondWith(
    caches.match(e.request).then(res => res || fetch(e.request))
  );
});