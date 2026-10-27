# Security Audit Documentation

## Overview
Security measures implemented in GENTA Pangan Banten application following OWASP Top 10 and Laravel security best practices.

## Authentication Security

### Password Security
- **Hashing**: bcrypt (Laravel default, cost factor 12)
- **Minimum Length**: 8 characters (enforced in validation)
- **Rate Limiting**: 5 attempts/minute on login
- **Session Regeneration**: On login to prevent fixation
- **Remember Token**: Secure, encrypted cookie (128-bit random)

### Login Protection
```php
// Rate limiter in RouteServiceProvider
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

// Applied to login routes
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');
```

### Password Reset Security
- **Token Expiry**: 60 minutes (configurable)
- **Single Use**: Token invalidated after use
- **Email Verification**: Required before reset
- **Secure Generation**: `Str::random(64)`

## Authorization Security

### Policy-Based Access Control
All controllers use `$this->authorize()` with policies:
```php
// DapurController
public function update(Request $request, Dapur $dapur)
{
    $this->authorize('update', $dapur); // Checks DapurPolicy
    // ...
}
```

### Role-Based Route Protection
```php
// Middleware checks role
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin-only routes
});
```

### Mass Assignment Protection
```php
// All models use $fillable
protected $fillable = ['name', 'email', 'role', 'dapur_id'];
// NOT $guarded = ['id']; (avoid guarded)
```

## Input Validation & Sanitization

### Form Requests
```php
// DapurRequest
public function rules(): array
{
    return [
        'nama' => 'required|string|max:255',
        'lokasi' => 'nullable|string|max:1000',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'status' => 'required|in:operasional,perlu_perhatian,non_operasional',
        'progress' => 'nullable|integer|min:0|max:100',
    ];
}
```

### XSS Prevention
- **Blade Escaping**: `{{ $variable }}` auto-escapes
- **Raw Output**: Only `{!! $variable !!}` for trusted content
- **CSP Headers**: Configured in nginx

## SQL Injection Prevention

### Eloquent ORM (Primary)
```php
// Safe - parameterized queries
Dapur::where('nama', $request->nama)->get();
User::find($id);

// Avoid raw queries
// DB::select("SELECT * FROM dapurs WHERE nama = '$name'"); // UNSAFE
```

### When Raw Queries Needed
```php
// Use parameter binding
DB::select('SELECT * FROM dapurs WHERE nama = ?', [$name]);
DB::raw('COUNT(*) as total'); // In select()
```

## CSRF Protection

### Automatic on Forms
```blade
<form method="POST">
    @csrf
    <!-- Form fields -->
</form>
```

### API Routes (Sanctum)
```php
// Stateful domains for SPA
'sanctum' => [
    'stateful' => ['localhost:3000', 'genta-banten.go.id'],
],
```

## File Upload Security

### Validation Rules
```php
'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:max_width=1920,max_height=1080',
```

### Storage
- **Private Disk**: Files stored outside public directory
- **Signed URLs**: Temporary access for private files
- **Type Validation**: MIME type + extension check

## API Security

### Sanctum Token Authentication
```php
// Token abilities
$token = $user->createToken('mobile', ['check-in', 'report-anomaly'])->plainTextToken;

// Middleware checks abilities
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/absensi/check-in', [MobileController::class, 'checkIn'])
        ->middleware('ability:check-in');
});
```

### Rate Limiting API
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

## Data Protection

### Encryption at Rest
```php
// Encrypted model attributes
protected $casts = [
    'password' => 'hashed',
    'fcm_token' => 'encrypted', // If sensitive
];
```

### PII Handling
- **Email**: Only used for auth/notifications
- **GPS Coordinates**: Stored with 8 decimal precision (~1mm)
- **Photos**: Base64 in DB or S3 with private ACL
- **No SSN/ID Numbers**: Not collected

## Security Headers

### Nginx Configuration
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https://fcm.googleapis.com;" always;
add_header Permissions-Policy "geolocation=(), camera=(), microphone=()" always;
```

### HSTS
```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
```

## Logging & Monitoring

### Security Events Logged
```php
// Failed login attempts
Log::warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);

// Authorization failures
Log::warning('Authorization failed', [
    'user_id' => auth()->id(),
    'action' => $ability,
    'resource' => get_class($resource),
    'ip' => request()->ip(),
]);

// FCM failures
Log::error('FCM send failed', [
    'tokens_count' => count($tokens),
    'error' => $error,
]);
```

### Audit Trail
```php
// SpWarning model events
protected static function booted()
{
    static::created(function ($sp) {
        Log::info('SP Warning created', ['sp_id' => $sp->id, 'by' => auth()->id()]);
    });
    
    static::updated(function ($sp) {
        Log::info('SP Warning updated', ['sp_id' => $sp->id, 'by' => auth()->id(), 'changes' => $sp->getChanges()]);
    });
}
```

## Vulnerability Scanning

### Dependency Scanning
```bash
# Composer audit
composer audit

# NPM audit
npm audit
npm audit fix
```

### Laravel Security
```bash
# Laravel security checker (if available)
php artisan security:check
```

## Penetration Testing Checklist

### OWASP Top 10 (2021) Coverage

| Category | Status | Implementation |
|----------|--------|----------------|
| **A01: Broken Access Control** | ✅ Mitigated | Policies, middleware, route protection |
| **A02: Cryptographic Failures** | ✅ Mitigated | HTTPS, bcrypt, encrypted attributes |
| **A03: Injection** | ✅ Mitigated | Eloquent ORM, validation, parameter binding |
| **A04: Insecure Design** | ✅ Mitigated | Policy-based auth, rate limiting, secure defaults |
| **A05: Security Misconfiguration** | ✅ Mitigated | `.env` not committed, debug=false, headers |
| **A06: Vulnerable Components** | ✅ Monitored | `composer audit`, `npm audit`, Dependabot |
| **A07: Authentication Failures** | ✅ Mitigated | Rate limiting, bcrypt, session regeneration |
| **A08: Software Integrity Failures** | ✅ Mitigated | Signed commits, CI/CD verification |
| **A09: Logging Failures** | ✅ Mitigated | Structured logging, security events |
| **A10: SSRF** | ✅ Mitigated | No user-controlled URLs, validation |

### API Security Top 10 (2023)
| Category | Status |
|----------|--------|
| API1: Broken Object Level Authorization | ✅ Policies |
| API2: Broken Authentication | ✅ Sanctum + rate limiting |
| API3: Broken Object Property Level Authorization | ✅ $fillable, form requests |
| API4: Unrestricted Resource Consumption | ✅ Rate limiting, pagination |
| API5: Broken Function Level Authorization | ✅ Role middleware |
| API6: Unrestricted Access to Sensitive Business Flows | ✅ Policies |
| API7: Server Side Request Forgery | ✅ No external requests from user input |
| API8: Security Misconfiguration | ✅ Headers, debug=false |
| API9: Improper Inventory Management | ✅ Versioned API, docs |
| API10: Unsafe Consumption of APIs | ✅ FCM server key in env |

## Incident Response

### Security Incident Types
1. **Data Breach**: Unauthorized data access
2. **Account Compromise**: Credential theft
3. **Injection Attack**: SQL/XSS attempt
4. **DoS/Brute Force**: Excessive requests
5. **Malicious Upload**: Dangerous file upload

### Response Procedures
```php
// 1. Detect - Automated alerts
// 2. Contain - Disable account, block IP
// 3. Eradicate - Remove threat, patch vulnerability
// 4. Recover - Restore from backup if needed
// 5. Post-Incident - Document, improve defenses
```

### Emergency Contacts
- **Security Team**: security@genta-banten.go.id
- **Infrastructure**: infra@genta-banten.go.id
- **Management**: management@genta-banten.go.id

## Compliance

### Data Retention
| Data Type | Retention | Deletion Method |
|-----------|-----------|-----------------|
| Absensi Records | 5 years | Soft delete + scheduled purge |
| SP Warnings | 10 years | Archive (never delete) |
| CCTV Anomaly Reports | 3 years | Soft delete |
| User Sessions | 2 hours | Auto-expire |
| FCM Tokens | 30 days inactive | Auto-cleanup |

### GDPR/PDPA Considerations
- **Right to Access**: User can export their data
- **Right to Deletion**: Admin can delete user (cascades)
- **Data Portability**: JSON export available
- **Consent**: Explicit for notifications

## Security Testing

### Automated Tests
```bash
# Run security-focused tests
php artisan test --group=security

# Test specific vulnerabilities
php artisan test --filter="sql_injection"
php artisan test --filter="xss"
php artisan test --filter="authorization"
```

### Manual Testing
```bash
# Test rate limiting
for i in {1..10}; do curl -X POST /login -d "email=test@test.com&password=wrong"; done

# Test authorization
curl -H "Authorization: Bearer user_token" /admin/users

# Test file upload
curl -F "photo=@malicious.php" /api/upload
```

## Security Updates

### Dependency Updates
```bash
# Weekly
composer update --dry-run
npm outdated

# Monthly
composer update
npm update
```

### Laravel Security Releases
- Subscribe to Laravel security announcements
- Apply patches within 24 hours for critical
- Test in staging before production

## Security Configuration Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` (32 chars)
- [ ] HTTPS enforced (HSTS)
- [ ] Secure cookies (`SESSION_SECURE_COOKIE=true`)
- [ ] CSRF enabled on all forms
- [ ] Rate limiting on auth endpoints
- [ ] SQL injection prevention (Eloquent)
- [ ] XSS prevention (Blade escaping)
- [ ] File upload validation
- [ ] API token abilities scoped
- [ ] Security headers configured
- [ ] Logging for security events
- [ ] Dependency scanning enabled
- [ ] Backup encryption enabled
- [ ] Database user least privilege
- [ ] SSH key-only access
- [ ] Firewall configured
- [ ] Fail2Ban active