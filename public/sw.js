const CACHE_NAME = 'solver-v1';
const ASSETS = [
  './',
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
  // Ignorar peticiones que no sean HTTP/S (como extensiones de Chrome)
  if (!e.request.url.startsWith('http')) return;

  e.respondWith(
    caches.match(e.request).then(cachedResponse => {
      // 1. Si está en caché (archivos estáticos), lo devolvemos
      if (cachedResponse) return cachedResponse;

      // 2. Si no, intentamos ir a la red
      return fetch(e.request).catch(err => {
        // 3. Si la red falla (Offline) y es una navegación o una ruta de la App
        const isNavigation = e.request.mode === 'navigate';
        const isAppRoute = e.request.url.includes('?route=');

        if (isNavigation || isAppRoute) {
          return caches.match('index.php');
        }

        // Si es una imagen o recurso no crítico, simplemente lanzamos el error de forma controlada
        throw err;
      });
    })
  );
});