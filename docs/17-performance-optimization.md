# Performance Optimization Documentation

## Overview
Performance optimization strategies implemented in GENTA Pangan Banten for fast, responsive user experience.

## Frontend Performance

### Asset Optimization

#### Vite Build (`vite.config.js`)
```js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                    charts: ['chart.js'],
                },
            },
        },
    },
});
```

#### CSS Optimization
```css
/* TailwindCSS JIT - only used classes compiled */
/* resources/css/app.css */
@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

/* Custom properties for theming */
@layer base {
    :root {
        --color-primary: #E50000;
        /* ... */
    }
}
```

#### JavaScript Optimization
```js
// resources/js/app.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Lazy load heavy components
const HeavyComponent = () => import('./components/HeavyComponent.vue');
```

### Font Loading
```html
<!-- Preload critical fonts -->
<link rel="preload" href="https://fonts.gstatic.com/s/inter/v18/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hiA.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="https://fonts.gstatic.com/s/spacegrotesk/v19/VdGPAYm5i7AqOo1bVgXtFRs.woff2" as="font" type="font/woff2" crossorigin>

<!-- Font display swap -->
<style>
    @font-face {
        font-family: 'Inter';
        font-display: swap;
        src: url(...) format('woff2');
    }
</style>
```

### Image Optimization
```php
// Use responsive images
<img src="{{ asset('images/logo.png') }}" 
     srcset="{{ asset('images/logo@2x.png') }} 2x"
     alt="Logo"
     width="120" height="40"
     loading="lazy">

<!-- For user uploads: Intervention Image -->
Image::make($request->file('photo'))
    ->fit(1920, 1080, function ($constraint) {
        $constraint->upsize();
    })
    ->encode('jpg', 80)
    ->save($path);
```

### Caching Strategy

#### Browser Caching (Nginx)
```nginx
# Static assets - 1 year
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}

# HTML - no cache
location / {
    add_header Cache-Control "no-cache, no-store, must-revalidate";
}
```

#### Laravel Response Caching
```php
// Cache dashboard data for 5 minutes
$stats = Cache::remember('dashboard_stats', 300, function () {
    return [
        'total_pengawas' => User::role('pengawas')->count(),
        'total_dapur' => Dapur::count(),
        // ...
    ];
});
```

## Backend Performance

### Database Optimization

#### Query Optimization
```php
// ✅ Good - Eager loading
$dapurs = Dapur::with(['user', 'cctvs'])->get();

// ❌ Bad - N+1 problem
$dapurs = Dapur::all();
foreach ($dapurs as $dapur) {
    echo $dapur->user->name; // Triggers query per dapur
}

// ✅ Good - Select specific columns
$dapurs = Dapur::select('id', 'nama', 'status', 'progress')->get();

// ✅ Good - Chunk large results
Absensi::whereDate('check_in_at', today())
    ->chunkById(100, function ($absensis) {
        foreach ($absensis as $absensi) {
            // Process
        }
    });
```

#### Indexes
```php
// Migration indexes
$table->index(['status', 'dapur_id']); // Composite for filtered lists
$table->index('check_in_at'); // Date queries
$table->index(['user_id', 'dapur_id']); // Unique constraint support
```

#### Database Configuration
```php
// config/database.php
'mysql' => [
    // ...
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_EMULATE_PREPARES => false, // Native prepared statements
    ]) : [],
],
```

### Caching

#### Redis Configuration
```php
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],

// config/redis.php
'client' => 'phpredis', // Faster than predis
'clusters' => [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],
],
'cache' => [
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'password' => env('REDIS_PASSWORD'),
    'port' => env('REDIS_PORT', 6379),
    'database' => 1,
],
```

#### Cache Patterns
```php
// Dashboard stats - 5 min
Cache::remember('admin_dashboard_stats', 300, fn() => calculateStats());

// Dapur list - 10 min, invalidate on changes
Cache::remember('dapurs_list', 600, fn() => Dapur::with('user')->get());

// User permissions - 1 hour
Cache::remember("user_permissions_{$userId}", 3600, fn() => getPermissions($userId));

// Cache tags for bulk invalidation
Cache::tags(['dapurs', 'dashboard'])->put('key', $value, 300);
Cache::tags(['dapurs'])->flush(); // Invalidate all dapur caches
```

#### Model Observers for Cache Invalidation
```php
// app/Observers/DapurObserver.php
class DapurObserver
{
    public function saved(Dapur $dapur): void
    {
        Cache::tags(['dapurs'])->flush();
        Cache::forget('admin_dashboard_stats');
    }

    public function deleted(Dapur $dapur): void
    {
        Cache::tags(['dapurs'])->flush();
    }
}

// Register in AppServiceProvider
Dapur::observe(DapurObserver::class);
```

### Queue Optimization

#### Job Configuration
```php
// config/queue.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => 90,
    'block_for' => null,
],

// Multiple queues for priority
'connections' => [
    'redis' => [
        'driver' => 'redis',
        // ...
        'queue' => ['high', 'default', 'low'],
    ],
],
```

#### Worker Scaling
```bash
# Horizontal scaling - run on multiple servers
# High priority queue
php artisan queue:work redis --queue=high --sleep=1 --tries=3

# Default queue
php artisan queue:work redis --queue=default --sleep=3 --tries=3

# Low priority (reports, exports)
php artisan queue:work redis --queue=low --sleep=10 --tries=1
```

#### Job Optimization
```php
// Use ShouldQueue with specific queue
class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'high'; // Priority queue
    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60]; // Exponential backoff
    
    // Only serialize needed data
    public function __construct(
        public readonly array $tokens,
        public readonly string $title,
        public readonly string $body,
    ) {}
    
    // Avoid serializing entire models
    // ❌ public User $user;
    // ✅ public int $userId;
}
```

### Database Connection Pooling
```php
// config/database.php
'mysql' => [
    // ...
    'pool' => [
        'min_connections' => 5,
        'max_connections' => 20,
        'idle_timeout' => 300,
    ],
],
```

## API Performance

### Response Optimization
```php
// Transformers/Resources for consistent output
class DapurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'status' => $this->status,
            'progress' => $this->progress,
            // Only include relations when requested
            'cctvs' => $this->whenLoaded('cctvs'),
            'pengawas' => $this->whenLoaded('user'),
        ];
    }
}

// Controller
public function index()
{
    return DapurResource::collection(
        Dapur::with('cctvs')->paginate(15)
    );
}
```

### Pagination
```php
// Cursor pagination for large datasets (better performance)
Dapur::orderBy('id')->cursorPaginate(50);

// Standard pagination for smaller sets
Dapur::paginate(15);
```

### ETags & Conditional Requests
```php
// Middleware for ETag
public function handle(Request $request, Closure $next)
{
    $response = $next($request);
    
    if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
        $etag = md5($response->getContent());
        $response->headers->set('ETag', $etag);
        
        if ($request->getETags() && in_array($etag, $request->getETags())) {
            return response('', 304);
        }
    }
    
    return $response;
}
```

## Monitoring & Metrics

### Laravel Pulse (Production Monitoring)
```bash
composer require laravel/pulse
php artisan pulse:install
php artisan pulse:check
```

#### Key Metrics to Monitor
| Metric | Target | Alert Threshold |
|--------|--------|-----------------|
| Response Time (p95) | < 500ms | > 1000ms |
| Database Query Time | < 50ms | > 200ms |
| Queue Job Duration | < 30s | > 60s |
| Memory Usage | < 128MB | > 256MB |
| Cache Hit Rate | > 90% | < 80% |
| Error Rate | < 0.1% | > 1% |

### Custom Metrics
```php
// Track custom metrics
Pulse::record('absensi_checkin', 1, ['role' => 'pengawas']);
Pulse::record('cctv_anomaly_reported', 1, ['type' => $type]);
Pulse::record('sp_warning_issued', 1, ['level' => $level]);
```

### Slow Query Detection
```php
// Enable slow query log in MySQL
-- slow_query_log = 1
-- long_query_time = 1
-- log_output = FILE

// Or in Laravel
DB::listen(function ($query) {
    if ($query->time > 100) { // 100ms
        Log::warning('Slow query', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time,
        ]);
    }
});
```

## Load Testing

### Artillery Configuration (`load-test.yml`)
```yaml
config:
  target: "https://genta-banten.go.id"
  phases:
    - duration: 60
      arrivalRate: 10
      name: "Warm up"
    - duration: 120
      arrivalRate: 50
      name: "Sustained load"
    - duration: 60
      arrivalRate: 100
      name: "Peak load"
scenarios:
  - name: "Login flow"
    flow:
      - post:
          url: "/login"
          json:
            email: "test@example.com"
            password: "password"
          capture:
            - json: "$.token"
              as: "token"
      - get:
          url: "/api/user"
          headers:
            Authorization: "Bearer {{ token }}"

  - name: "Dashboard"
    flow:
      - get:
          url: "/admin/dashboard"
          headers:
            Authorization: "Bearer {{ token }}"
```

### Run Load Test
```bash
npm install -g artillery
artillery run load-test.yml --output report.json
artillery report report.json
```

## Optimization Checklist

### Development
- [ ] Use eager loading (`with()`)
- [ ] Select specific columns (`select()`)
- [ ] Add database indexes
- [ ] Use cache for expensive computations
- [ ] Queue long-running tasks
- [ ] Paginate large datasets
- [ ] Minimize N+1 queries

### Build
- [ ] `npm run build` (production assets)
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan event:cache`

### Production
- [ ] OPcache enabled
- [ ] Redis for cache/sessions/queue
- [ ] Nginx gzip + static caching
- [ ] CDN for static assets
- [ ] Database connection pooling
- [ ] Queue workers scaled
- [ ] Monitoring (Pulse/Telescope)
- [ ] Slow query logging
- [ ] Automated backups

### Frontend
- [ ] Font preloading
- [ ] Image optimization
- [ ] Code splitting
- [ ] Lazy loading
- [ ] Minification
- [ ] Tree shaking
- [ ] Critical CSS inlined