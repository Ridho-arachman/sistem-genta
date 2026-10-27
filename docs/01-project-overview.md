# GENTA Pangan Banten - Project Overview

## Project Information
- **Name**: GENTA Pangan Banten (Genta Pengawasan Dapur MBG)
- **Type**: Laravel Web Application
- **PHP Version**: 8.5
- **Framework**: Laravel 12.x
- **Status**: Production Ready (Upgraded from MVP)

## Architecture Overview

### Technology Stack
- **Backend**: Laravel 12.x, PHP 8.5
- **Frontend**: TailwindCSS, Alpine.js 3.x
- **Database**: MySQL/PostgreSQL
- **Queue**: Redis (for background jobs)
- **Notifications**: Firebase Cloud Messaging (FCM)
- **Maps/Geolocation**: Custom Haversine implementation

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           # Authentication controllers
│   │   ├── Admin/          # Admin panel controllers
│   │   ├── Manajerial/     # Management panel controllers
│   │   ├── Pengawas/       # Supervisor panel controllers
│   │   └── Api/            # Mobile API controllers
│   ├── Middleware/         # Custom middleware
│   └── Requests/           # Form requests
├── Models/                 # Eloquent models
├── Policies/               # Authorization policies
├── Providers/              # Service providers
├── Services/               # Business logic services
└── Enums/                  # PHP Enums

resources/views/
├── layouts/                # Main layouts
├── auth/                   # Authentication views
├── admin/                  # Admin panel views
├── manajerial/             # Management panel views
├── pengawas/               # Supervisor panel views
├── kepala-dapur/           # Kitchen head views (legacy layout)
└── components/             # Reusable Blade components

routes/
├── web.php                 # Web routes
└── api.php                 # API routes

tests/
├── Feature/                # Feature tests
└── Unit/                   # Unit tests
```

## User Roles & Permissions

| Role | Description | Access Level |
|------|-------------|--------------|
| **Admin** | System administrator | Full access to all modules |
| **Manajerial** | Management/Leadership | Read-only dashboard, reports |
| **Pengawas** | Field supervisor | Dashboard, absensi, CCTV monitoring, reports |
| **Kepala Dapur** | Kitchen head | Legacy dashboard (excluded from layout upgrade) |

## Key Features

### 1. Authentication System
- Login with email/password
- Password reset (forgot/reset flow)
- Remember me functionality
- Role-based redirection after login

### 2. Dashboard System (Upgraded Layout)
- **Topbar**: 72px height, brand, user avatar, mode indicator
- **Sidebar**: 230px width, navigation with active states
- **Shell Grid**: CSS Grid layout (sidebar + main content)
- **Responsive**: Mobile-first breakpoints (900px, 520px)
- **Stats Cards**: 4-column grid with icons and trends
- **Kitchen Cards**: Progress bars, status badges, camera counts
- **Camera Panel**: Live preview placeholder, camera list
- **Activity Feed**: Timestamped activity log

### 3. Dapur (Kitchen) Management
- CRUD operations for kitchen locations
- Status tracking (operasional, perlu_perhatian, non_operasional)
- Progress tracking (0-100%)
- CCTV association

### 4. CCTV Monitoring
- Device registration and management
- Status tracking (online, offline, maintenance)
- Anomaly detection and reporting
- Live preview integration (placeholder)

### 5. Absensi (Attendance)
- GPS-based check-in/check-out
- Geofencing with Haversine formula
- Photo capture support
- Status tracking (hadir, izin, cuti, alpha)

### 6. Surat Peringatan (SP Warning)
- Warning letter generation
- Multi-level escalation (SP1, SP2, SP3)
- Status tracking

### 7. Mobile API
- RESTful API for mobile app
- Authentication via Sanctum
- Offline sync support
- Push notifications via FCM

### 8. Geolocation Service
- Haversine distance calculation
- Geofence validation
- Coordinate validation

### 9. Notification System
- FCM integration with retry logic
- Circuit breaker pattern
- Multi-platform support (Android/iOS)

## Layout Upgrade (MVP → Final)

### Design System
- **Colors**: Red (#E50000) + White (#FFFFFF)
- **Typography**: Inter (UI) + Space Grotesk (Headings) + JetBrains Mono (Code)
- **Spacing**: 8px base unit
- **Border Radius**: 8px (cards), 12px (panels)
- **Shadows**: Subtle, elevation-based

### Applied To
- ✅ `pengawas/layouts/app.blade.php` - Supervisor layout
- ✅ `pengawas/dashboard.blade.php` - Supervisor dashboard
- ✅ `admin/layouts/app.blade.php` - Admin layout
- ✅ `admin/dashboard.blade.php` - Admin dashboard
- ✅ `manajerial/layouts/app.blade.php` - Management layout
- ✅ `manajerial/dashboard.blade.php` - Management dashboard

### Excluded (Per Requirements)
- ❌ `kepala-dapur/*` - Kitchen head dashboard (legacy)
- ❌ Public pages (`/`, `/login`, landing pages)

## Implementation Status

| Feature | Status | Tests |
|---------|--------|-------|
| Authentication | ✅ Complete | ✅ Passing |
| Password Reset | ✅ Complete | ✅ Passing |
| Admin Dashboard | ✅ Complete | ✅ Passing |
| Manajerial Dashboard | ✅ Complete | ✅ Passing |
| Pengawas Dashboard | ✅ Complete | ✅ Passing |
| Dapur Management | ✅ Complete | ✅ Passing |
| CCTV Management | ✅ Complete | ✅ Passing |
| Absensi System | ✅ Complete | ✅ Passing |
| SP Warning System | ✅ Complete | ✅ Passing |
| CCTV Anomaly Reports | ✅ Complete | ✅ Passing |
| Mobile API | ✅ Complete | ✅ Passing |
| Geolocation Service | ✅ Complete | ✅ Passing |
| FCM Notifications | ✅ Complete | ✅ Passing |
| Authorization Policies | ✅ Complete | ✅ Passing |
| Database Migrations | ✅ Complete | ✅ Passing |

## Test Coverage
- **Total Tests**: 58
- **Passing**: 58
- **Coverage**: Feature tests for all major controllers

## Deployment
- Laravel Cloud compatible
- Docker support available
- CI/CD via GitHub Actions
- Environment-based configuration

## Security
- Laravel Policies for authorization
- CSRF protection
- XSS protection (Blade escaping)
- SQL injection prevention (Eloquent)
- Rate limiting on auth routes
- Secure password hashing (bcrypt)