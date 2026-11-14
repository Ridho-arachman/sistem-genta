// Bootstrap global: service worker PWA + penanda request dari WebView Capacitor
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}
if (window.Capacitor) {
    document.cookie = 'capacitor_app=1; path=/; max-age=3600; SameSite=Lax';
}
