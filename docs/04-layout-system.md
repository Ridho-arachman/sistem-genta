# Layout System Documentation

## Overview
Unified layout system adapted from `pengawas_mbg_demo.html` with consistent design across all three main panels (Pengawas, Admin, Manajerial).

## Design System

### Color Palette
```css
:root {
    --color-primary: #E50000;      /* Main brand red */
    --color-primary-dark: #B20000; /* Hover states */
    --color-primary-light: #FF3333;/* Active/focus */
    --color-secondary: #000000;    /* Headings */
    --color-background: #FFFFFF;   /* Page background */
    --color-surface: #F5F5F5;      /* Card backgrounds */
    --color-surface-hover: #EEEEEE;/* Hover states */
    --color-border: #E0E0E0;       /* Borders */
    --color-text: #000000;         /* Body text */
    --color-text-muted: #666666;   /* Secondary text */
}
```

### Typography
```css
/* Body text */
font-family: 'Inter', system-ui, sans-serif;

/* Headings */
font-family: 'Space Grotesk', system-ui, sans-serif;

/* Code/monospace */
font-family: 'JetBrains Mono', ui-monospace, monospace;
```

### Spacing Scale
- Base unit: 8px
- xs: 4px (0.5rem)
- sm: 8px (1rem)
- md: 16px (2rem)
- lg: 24px (3rem)
- xl: 32px (4rem)

## Layout Structure

### Shell Grid (Desktop ≥ 900px)
```css
.shell {
    display: grid;
    grid-template-columns: 230px minmax(0, 1fr);
    min-height: calc(100vh - 72px);
}
```

### Topbar (72px height)
```css
.topbar {
    height: 72px; /* 18 * 4px */
    background: var(--color-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px; /* 1.5rem */
    gap: 16px;
}
```

**Components:**
- Brand mark (G logo) + "GENTA [PANEL NAME]"
- Mode indicator pill ("MODE PRODUKSI")
- User avatar (initial)

### Sidebar (230px width)
```css
.sidebar {
    width: 230px;
    background: white;
    border-right: 1px solid var(--color-border);
    padding: 16px;
    overflow-y: auto;
}
```

**Navigation Items:**
- Dashboard
- Data Dapur / Absensi
- Monitoring CCTV
- Laporan / Reports
- Settings (System section)

**Active State:**
```css
.sidebar-nav-active {
    background: var(--color-primary)/10;
    color: var(--color-primary);
    font-weight: 500;
}
```

### Main Content Area
```css
.main-content {
    padding: 28px; /* 1.75rem */
    min-width: 0; /* Prevents overflow */
}
```

## Responsive Breakpoints

### Tablet (≤ 900px)
```css
@media (max-width: 900px) {
    .shell { grid-template-columns: 1fr; }
    .sidebar { display: none; }
    .main-content { padding: 20px; }
    .topbar { padding: 0 18px; }
}
```

### Mobile (≤ 520px)
```css
@media (max-width: 520px) {
    .topbar { height: 64px; }
    .main-content { padding: 16px; }
}
```

## Component Library

### Buttons
```css
.btn-primary {
    @apply bg-[var(--color-primary)] 
           hover:bg-[var(--color-primary-dark)] 
           text-white px-4 py-2 rounded-lg 
           font-medium transition-colors;
}

.btn-secondary {
    @apply bg-[var(--color-surface)] 
           hover:bg-[var(--color-surface-hover)] 
           text-[var(--color-text)] 
           border border-[var(--color-border)] 
           px-4 py-2 rounded-lg 
           font-medium transition-colors;
}

.btn-light {
    @apply bg-white 
           hover:bg-[var(--color-surface)] 
           text-[var(--color-text)] 
           border border-[var(--color-border)] 
           px-4 py-2 rounded-lg 
           font-medium transition-colors;
}
```

### Input Fields
```css
.input-field {
    @apply w-full bg-white 
           border border-[var(--color-border)] 
           rounded-lg px-4 py-3 
           text-[var(--color-text)] 
           placeholder:text-[var(--color-text-muted)]/60
           focus:outline-none 
           focus:ring-2 focus:ring-[var(--color-primary)] 
           focus:ring-offset-2 focus:border-transparent 
           transition-all duration-200 
           min-h-[44px]; /* Touch target */
}
```

### Cards
```css
.card {
    @apply bg-white 
           border border-[var(--color-border)] 
           rounded-xl shadow-sm;
}
```

### Stat Cards
```css
.stat-card {
    @apply card p-6;
}

.stat-top {
    @apply flex items-center justify-between mb-2;
}

.stat-icon {
    @apply w-8 h-8 rounded-xl flex items-center justify-center;
}

.stat-value {
    @apply text-3xl font-bold text-[var(--color-secondary)];
}

.stat-foot {
    @apply text-sm text-[var(--color-text-muted)] mt-2;
}
```

### Kitchen/Dapur Cards
```css
.kitchen-card {
    @apply card p-4 flex items-center justify-between gap-4;
}

.kitchen-card-content {
    @apply flex-1 min-w-0;
}

.kitchen-name {
    @apply font-medium text-[var(--color-secondary)] truncate;
}

.kitchen-meta {
    @apply text-sm text-[var(--color-text-muted)] mt-1;
}

.progress-bar {
    @apply h-2 bg-[var(--color-border)] rounded-full overflow-hidden;
}

.progress-fill {
    @apply h-full rounded-full transition-all duration-500;
}

.progress-ok { @apply bg-green-500; }
.progress-warn { @apply bg-yellow-500; }
.progress-off { @apply bg-red-500; }
```

### Status Badges
```css
.status-badge {
    @apply inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium;
}

.status-ok { @apply bg-green-100 text-green-700; }
.status-warn { @apply bg-yellow-100 text-yellow-700; }
.status-off { @apply bg-red-100 text-red-700; }
.status-info { @apply bg-blue-100 text-blue-700; }
```

### Panels
```css
.panel {
    @apply card;
}

.panel-head {
    @apply flex items-center justify-between p-4 border-b border-[var(--color-border)];
}

.panel-head h2 {
    @apply text-lg font-semibold;
}
```

## Applied Layouts

### 1. Pengawas Layout (`resources/views/pengawas/layouts/app.blade.php`)
- **Brand**: "GENTA PENGAWASAN DAPUR MBG"
- **Avatar**: "P" (Pengawas)
- **Navigation**: Dashboard, Data Dapur, Monitoring CCTV, Laporan Pengawasan, Pengaturan
- **Demo**: Client-side page switching with Alpine.js

### 2. Admin Layout (`resources/views/admin/layouts/app.blade.php`)
- **Brand**: "GENTA ADMIN PANEL"
- **Avatar**: "A" (Admin)
- **Navigation**: Dashboard, Manajemen User, Data Dapur, Data CCTV, Monitoring CCTV, Anomali CCTV, Data Dapur, Surat Peringatan (SP), Absensi, Pengaturan

### 3. Manajerial Layout (`resources/views/manajerial/layouts/app.blade.php`)
- **Brand**: "GENTA MANAJERIAL PANEL"
- **Avatar**: "M" (Manajerial)
- **Navigation**: Dashboard, Data Dapur, Data CCTV, Monitoring CCTV, Anomali CCTV, Data Dapur, Surat Peringatan (SP), Absensi, Pengaturan

### 4. Main Layout (`resources/views/layouts/app.blade.php`)
- **Used by**: Auth pages, public pages
- **Alpine.js**: Loaded here for global access
- **No shell**: Simple centered container

## Excluded Layouts

### Kepala Dapur (`resources/views/kepala-dapur/layouts/app.blade.php`)
- **Status**: Legacy layout (not upgraded)
- **Reason**: Per requirements, excluded from MVP→Final upgrade
- **Future**: Separate redesign planned

### Public Pages
- **Landing** (`/`): Marketing page, separate design
- **Login** (`/login`): Uses main layout with centered card
- **Password Reset**: Uses main layout

## Implementation Files

| File | Purpose |
|------|---------|
| `resources/views/pengawas/layouts/app.blade.php` | Pengawas shell layout |
| `resources/views/pengawas/dashboard.blade.php` | Pengawas dashboard content |
| `resources/views/admin/layouts/app.blade.php` | Admin shell layout |
| `resources/views/admin/dashboard.blade.php` | Admin dashboard content |
| `resources/views/manajerial/layouts/app.blade.php` | Manajerial shell layout |
| `resources/views/manajerial/dashboard.blade.php` | Manajerial dashboard content |
| `resources/views/layouts/app.blade.php` | Base layout (auth, public) |

## JavaScript Interactions

### Page Navigation (Pengawas Demo)
```javascript
// Client-side page switching for demo
function setPage(page) {
    currentPage = page;
    render();
}

function render() {
    document.querySelectorAll('.sidebar-nav').forEach(btn => 
        btn.classList.toggle('sidebar-nav-active', btn.dataset.page === currentPage)
    );
    // Render content based on page
}
```

### Kitchen Search Filter
```javascript
function filterKitchens(query) {
    const list = document.getElementById('kitchenList');
    if (!list) return;
    list.innerHTML = kitchens
        .filter(k => (k.name + ' ' + k.loc).toLowerCase().includes(query.toLowerCase()))
        .map(k => /* kitchen card HTML */)
        .join('') || '<p class="muted">Tidak ada dapur yang cocok.</p>';
}
```

### Toast Notifications
```javascript
function toast(msg) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.style.display = 'none', 2800);
}
```

## Migration from MVP

### Changes Made
1. **Replaced** old sidebar/header structure with shell grid
2. **Added** Topbar (72px) with brand and user avatar
3. **Standardized** Sidebar width (230px) and navigation pattern
4. **Unified** Color system using CSS custom properties
5. **Added** Responsive breakpoints (900px, 520px)
6. **Created** Reusable component classes (stat-card, kitchen-card, etc.)
7. **Integrated** Demo JavaScript for client-side navigation (Pengawas)

### Backward Compatibility
- All existing routes preserved
- Controller logic unchanged
- Blade `@yield('content')` pattern maintained
- Section-based content injection works as before