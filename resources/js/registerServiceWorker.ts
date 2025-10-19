// Register service worker for PWA functionality
export function registerServiceWorker(): void {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/service-worker.js')
        .then(registration => {
          console.log('Service Worker registered with scope:', registration.scope);
          
          // Check for updates
          registration.update();
          
          // Listen for updates
          registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            if (newWorker) {
              newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                  // New update available
                  console.log('New content is available; please refresh.');
                }
              });
            }
          });
        })
        .catch(error => {
          console.log('Service Worker registration failed:', error);
        });
    });
    
    // Listen for service worker controller change
    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      if (!refreshing) {
        window.location.reload();
        refreshing = true;
      }
    });
  } else {
    console.log('Service Workers are not supported in this browser.');
  }
}