# Changelog

All notable changes to GENTA Pangan Banten will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-10-26 - MVP to Final Upgrade

### 🎨 Layout System Overhaul
- **Added**: Unified shell layout adapted from `pengawas_mbg_demo.html`
  - Topbar (72px) with brand, mode indicator, user avatar
  - Sidebar (230px) with navigation and active states
  - CSS Grid shell layout (sidebar + main content)
  - Responsive breakpoints: 900px (tablet), 520px (mobile)
- **Applied to**: Pengawas, Admin, Manajerial panels
- **Excluded**: Kepala Dapur (legacy), Public pages
- **Design System**: Red (#E50000) + White, Inter + Space Grotesk fonts

### 🔐 Authentication Enhancements
- **Added**: Password reset flow (forgot/reset)
- **Added**: Eye icon toggle for password visibility (Alpine.js)
- **Added**: Button loading states with spinner
- **Fixed**: Alpine.js loading in main layout (was missing)
- **Fixed**: Route names in views/controllers to use proper prefixes

### 🛡️ Authorization & Security
- **Added**: 4 Laravel Policies (Dapur, CCTV, Absensi, CCTV Anomaly)
- **Added**: Policy registration in AppServiceProvider
- **Added**: Role-based route protection middleware
- **Added**: Unique constraint on absensi table (prevent duplicate check-ins)
- **Fixed**: CCTV & Dapur controllers to use admin-prefixed routes
- **Fixed**: CCTV Anomaly Report controller to use AuthorizesRequests trait

### 📍 Geolocation Service
- **Added**: `GeolocationService` with Haversine formula
- **Added**: Geofence validation (100m default radius)
- **Added**: Coordinate validation and distance formatting
- **Added**: Bearing calculation for direction display

### 🔔 Notification System
- **Enhanced**: `FcmService` with retry logic (3 attempts, exponential backoff)
- **Added**: Circuit breaker pattern (5 failures → 60s timeout)
- **Added**: Token management (canonical IDs, invalid token cleanup)
- **Added**: Multi-platform support (Android/iOS payloads)

### 🧪 Testing
- **Fixed**: All 58 tests passing
- **Fixed**: Route names in tests to use proper prefixes
- **Fixed**: Unique constraint handling in AbsensiTest
- **Fixed**: Mobile API test route for dapur store

### 🧹 Code Quality
- **Cleaned**: Temporary files, updated .gitignore
- **Formatted**: All PHP files with Laravel Pint
- **Committed**: All changes pushed to GitHub

### 📚 Documentation
- **Created**: 19 comprehensive documentation files
- **Updated**: All docs reflect current implementation

---

## [1.0.0] - 2026-10-25 - MVP Release

### Core Features
- **Authentication**: Login, logout, role-based redirect
- **User Roles**: Admin, Manajerial, Pengawas, Kepala Dapur
- **Dapur Management**: CRUD with status tracking
- **CCTV Management**: Device registration, status monitoring
- **Absensi System**: GPS-based check-in/out with geofencing
- **SP Warning System**: Multi-level (SP1/SP2/SP3) warning letters
- **CCTV Anomaly Reports**: Reporting and resolution workflow
- **Mobile API**: RESTful endpoints for field operations
- **Notifications**: FCM push notifications

### Technical Stack
- Laravel 12.x, PHP 8.5
- TailwindCSS + Alpine.js
- MySQL/PostgreSQL
- Redis for queue/cache
- Sanctum for API auth

---

## Version History

| Version | Date | Type | Description |
|---------|------|------|-------------|
| 2.0.0 | 2026-10-26 | Major | MVP → Final upgrade: Layout, Auth, AuthZ, Geo, FCM, Tests, Docs |
| 1.0.0 | 2026-10-25 | Major | Initial MVP release |

---

## Upgrade Guide: 1.0.0 → 2.0.0

### Breaking Changes
1. **Layout Structure**: All panel layouts redesigned (Pengawas, Admin, Manajerial)
2. **Route Prefixes**: All routes now use proper prefixes (`admin.`, `manajerial.`, `pengawas.`)
3. **Policy Checks**: Controllers now use `$this->authorize()` extensively
4. **Absensi Unique Constraint**: Duplicate daily check-ins now prevented at DB level

### Migration Steps
```bash
# 1. Pull latest changes
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Run migrations (includes unique constraint)
php artisan migrate --force

# 4. Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart queue workers
php artisan queue:restart
```

### Configuration Updates
```env
# New optional config
GEOFENCE_DEFAULT_RADIUS=100
GPS_MAX_ACCURACY=50
```

---

## Contributors
- Development Team
- QA Team
- DevOps Team

## Links
- [GitHub Repository](https://github.com/your-org/genta-banten)
- [Issue Tracker](https://github.com/your-org/genta-banten/issues)
- [Documentation](./docs/)