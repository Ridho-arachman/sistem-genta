# Documentation Index

## GENTA Pangan Banten - Complete Documentation Suite

This directory contains 19 comprehensive documentation files covering all aspects of the GENTA Pangan Banten system.

## Documentation Files

| # | File | Description |
|---|------|-------------|
| 01 | [Project Overview](./01-project-overview.md) | System architecture, tech stack, features, implementation status |
| 02 | [Authentication System](./02-authentication-system.md) | Login, password reset, role-based redirect, security features |
| 03 | [Authorization Policies](./03-authorization-policies.md) | 4 Laravel Policies, role permission matrix, usage examples |
| 04 | [Layout System](./04-layout-system.md) | Shell grid, topbar, sidebar, components, responsive design |
| 05 | [Dapur Management](./05-dapur-management.md) | Kitchen CRUD, status system, dashboard integration, API |
| 06 | [CCTV Monitoring](./06-cctv-monitoring.md) | Device management, anomaly detection, monitoring, API |
| 07 | [Absensi System](./07-absensi-system.md) | GPS attendance, geofencing, check-in/out, mobile API |
| 08 | [SP Warning System](./08-sp-warning-system.md) | Multi-level warnings (SP1/2/3), escalation, workflow |
| 09 | [Mobile API](./09-mobile-api.md) | RESTful endpoints, auth, sync, Flutter/React Native examples |
| 10 | [Geolocation Service](./10-geolocation-service.md) | Haversine formula, geofence validation, bearing calculation |
| 11 | [FCM Notification Service](./11-fcm-notification-service.md) | Retry logic, circuit breaker, multi-platform, token management |
| 12 | [User Roles & Permissions](./12-user-roles-permissions.md) | 4 roles, middleware, route protection, permission matrix |
| 13 | [Testing Strategy](./13-testing-strategy.md) | Pest PHP, feature/unit tests, patterns, CI/CD integration |
| 14 | [Database Schema](./14-database-schema.md) | ERD, tables, indexes, relationships, seeders, queries |
| 15 | [Deployment Guide](./15-deployment-guide.md) | Laravel Cloud, Docker, VPS, SSL, monitoring, rollback |
| 16 | [Security Audit](./16-security-audit.md) | OWASP Top 10, authZ, input validation, headers, compliance |
| 17 | [Performance Optimization](./17-performance-optimization.md) | Frontend/backend optimization, caching, queue, load testing |
| 18 | [Changelog](./18-changelog.md) | Version history, upgrade guide, breaking changes |
| 19 | [Architecture Decision Records](./19-architecture-decisions.md) | Key technical decisions, rationale, alternatives considered |

---

## Quick Navigation by Topic

### 🏗️ Architecture & Design
- [Project Overview](./01-project-overview.md) - Start here
- [Architecture Decisions](./19-architecture-decisions.md) - Key decisions
- [Layout System](./04-layout-system.md) - UI/UX implementation

### 🔐 Security & Access Control
- [Authentication](./02-authentication-system.md) - Login, reset, security
- [Authorization](./03-authorization-policies.md) - Policies, roles, permissions
- [Security Audit](./16-security-audit.md) - OWASP, compliance, headers
- [User Roles](./12-user-roles-permissions.md) - Role definitions, middleware

### 🎯 Core Features
- [Dapur Management](./05-dapur-management.md) - Kitchen operations
- [CCTV Monitoring](./06-cctv-monitoring.md) - Camera system
- [Absensi System](./07-absensi-system.md) - Attendance with GPS
- [SP Warnings](./08-sp-warning-system.md) - Compliance warnings

### 📱 Integration & API
- [Mobile API](./09-mobile-api.md) - Field operations API
- [Geolocation](./10-geolocation-service.md) - Distance, geofencing
- [FCM Notifications](./11-fcm-notification-service.md) - Push notifications

### 🧪 Quality Assurance
- [Testing Strategy](./13-testing-strategy.md) - Test patterns, coverage
- [Performance](./17-performance-optimization.md) - Optimization techniques
- [Database Schema](./14-database-schema.md) - Data model reference

### 🚀 Operations
- [Deployment Guide](./15-deployment-guide.md) - Production deployment
- [Changelog](./18-changelog.md) - Version history

---

## System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    GENTA Pangan Banten                       │
│              MBG Kitchen Supervision System                  │
├─────────────────────────────────────────────────────────────┤
│  Laravel 12.x  │  PHP 8.5  │  MySQL  │  Redis  │  Tailwind  │
├─────────────────────────────────────────────────────────────┤
│  Panels: Admin  │  Manajerial  │  Pengawas  │  Kepala Dapur │
├─────────────────────────────────────────────────────────────┤
│  Features: Auth │  Dapur  │  CCTV  │  Absensi  │  SP Warn  │
│              Anomaly  │  Mobile API  │  Notifications        │
└─────────────────────────────────────────────────────────────┘
```

### Key Metrics
- **Tests**: 58 passing
- **Documentation**: 19 files
- **Lines of Code**: ~15,000 (PHP + Blade + JS)
- **Database Tables**: 10 (6 core + 4 Laravel)
- **API Endpoints**: 25+

---

## Getting Started

### For Developers
1. Read [Project Overview](./01-project-overview.md)
2. Review [Architecture Decisions](./19-architecture-decisions.md)
3. Check [Testing Strategy](./13-testing-strategy.md)
4. Set up local environment per [Deployment Guide](./15-deployment-guide.md)

### For DevOps
1. Review [Deployment Guide](./15-deployment-guide.md)
2. Check [Security Audit](./16-security-audit.md)
3. Monitor via [Performance](./17-performance-optimization.md)

### For Mobile Team
1. Read [Mobile API](./09-mobile-api.md)
2. Check [Geolocation](./10-geolocation-service.md)
3. Integrate [FCM](./11-fcm-notification-service.md)

---

## Design System Reference

### Colors
```css
--color-primary: #E50000;      /* Brand red */
--color-primary-dark: #B20000; /* Hover */
--color-secondary: #000000;    /* Headings */
--color-background: #FFFFFF;   /* Background */
--color-surface: #F5F5F5;      /* Cards */
```

### Typography
- **UI Text**: Inter (400, 500, 600, 700)
- **Headings**: Space Grotesk (500, 600, 700)
- **Code**: JetBrains Mono (400, 500)

### Layout Dimensions
- **Topbar**: 72px (64px mobile)
- **Sidebar**: 230px (hidden < 900px)
- **Content Padding**: 28px (16px mobile)
- **Breakpoints**: 900px, 520px

---

## Support & Resources

### Internal Links
- [GitHub Repository](https://github.com/your-org/genta-banten)
- [Issue Tracker](https://github.com/your-org/genta-banten/issues)
- [CI/CD Pipeline](https://github.com/your-org/genta-banten/actions)
- [Staging Environment](https://staging.genta-banten.go.id)

### External Resources
- [Laravel Documentation](https://laravel.com/docs)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Pest PHP Documentation](https://pestphp.com/docs)
- [Firebase FCM Documentation](https://firebase.google.com/docs/cloud-messaging)

---

## Document Maintenance

### Update Process
1. Edit relevant `.md` file in `docs/`
2. Update [Changelog](./18-changelog.md) if significant
3. Ensure cross-references are valid
4. Commit with descriptive message

### Review Schedule
- **Monthly**: Check for outdated information
- **Per Release**: Update changelog, version references
- **Quarterly**: Full documentation audit

### Contributing
See [Architecture Decisions](./19-architecture-decisions.md) for decision-making process.

---

*Last Updated: 2026-10-26 | Version: 2.0.0*