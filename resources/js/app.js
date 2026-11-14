import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;
window.L = L;

// Global
import './components/bootstrap.js';
import './components/ui.js';

// Komponen Blade yang dipakai lintas halaman
import './components/kitchen-filter.js';
import './components/public-map.js';
import './components/dapur-autocomplete.js';
import './components/dapur-multi-select.js';
import './components/dapur-form.js';
import './components/hls-player.js';
import './components/confirm-submit.js';

// Satu file per halaman
import './pages/home.js';
import './pages/auth/login.js';
import './pages/auth/reset-password.js';
import './pages/admin/users-form.js';
import './pages/admin/settings.js';
import './pages/admin/dashboard.js';
import './pages/admin/laporan-index.js';
import './pages/admin/laporan-show.js';
import './pages/admin/dapurs-index.js';

Alpine.start();
