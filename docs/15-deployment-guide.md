# Deployment Guide

## Overview
Production deployment guide for GENTA Pangan Banten on Laravel Cloud, Docker, or traditional VPS.

## Requirements

### Server Requirements
- **PHP**: 8.5+
- **Extensions**: BCMath, Ctype, cURL, DOM, Fileinfo, GD, iconv, intl, mbstring, OpenSSL, PCRE, PDO, PDO MySQL, Redis, Tokenizer, XML
- **Database**: MySQL 8.0+ / PostgreSQL 14+
- **Queue**: Redis 7+
- **Web Server**: Nginx / Apache
- **SSL**: Let's Encrypt or custom certificate

### PHP Configuration
```ini
; php.ini
memory_limit = 256M
max_execution_time = 60
post_max_size = 50M
upload_max_filesize = 50M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
opcache.save_comments = 1
```

## Environment Configuration

### Production `.env`
```env
APP_NAME="GENTA Pangan Banten"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://genta-banten.go.id
APP_TIMEZONE=Asia/Jakarta

LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=genta_production
DB_USERNAME=genta_user
DB_PASSWORD=secure_password_here

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.genta-banten.go.id

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=redis_password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@genta-banten.go.id
MAIL_PASSWORD=mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@genta-banten.go.id
MAIL_FROM_NAME="GENTA Pangan Banten"

FCM_SERVER_KEY=your_fcm_server_key_from_firebase
FCM_SENDER_ID=your_sender_id

FILESYSTEM_DISK=local
# Or for S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=ap-southeast-1
# AWS_BUCKET=genta-banten-storage

VITE_APP_NAME="GENTA Pangan Banten"
```

## Deployment Methods

### Method 1: Laravel Cloud (Recommended)

#### Setup
```bash
# Install Laravel Cloud CLI
composer require laravel/cloud --dev

# Login
php artisan cloud:login

# Create project
php artisan cloud:project:create genta-banten

# Deploy
php artisan cloud:deploy
```

#### Benefits
- Zero-downtime deployments
- Automatic SSL
- Horizontal scaling
- Built-in monitoring
- Database backups
- Queue workers managed

### Method 2: Docker Deployment

#### `Dockerfile`
```dockerfile
FROM php:8.5-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    linux-headers \
    $PHPIZE_DEPS \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    redis

# PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    ctype \
    curl \
    dom \
    fileinfo \
    gd \
    iconv \
    intl \
    mbstring \
    opcache \
    pdo_mysql \
    tokenizer \
    xml \
    zip \
    redis \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js for assets
RUN apk add --no-cache nodejs npm

WORKDIR /var/www/html

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies & build assets
RUN npm ci && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80 443

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

#### `docker/nginx.conf`
```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 50M;
    
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml application/json;
    
    # Upstream
    upstream php {
        server unix:/var/run/php/php8.5-fpm.sock;
    }
    
    server {
        listen 80;
        listen [::]:80;
        server_name genta-banten.go.id;
        return 301 https://$server_name$request_uri;
    }
    
    server {
        listen 443 ssl http2;
        listen [::]:443 ssl http2;
        server_name genta-banten.go.id;
        root /var/www/html/public;
        index index.php;
        
        ssl_certificate /etc/letsencrypt/live/genta-banten.go.id/fullchain.pem;
        ssl_certificate_key /etc/letsencrypt/live/genta-banten.go.id/privkey.pem;
        ssl_protocols TLSv1.2 TLSv1.3;
        ssl_ciphers HIGH:!aNULL:!MD5;
        
        # Security headers
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";
        add_header X-XSS-Protection "1; mode=block";
        add_header Referrer-Policy "strict-origin-when-cross-origin";
        
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        
        location ~ \.php$ {
            fastcgi_pass php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_hide_header X-Powered-By;
        }
        
        location ~ /\.(?!well-known).* {
            deny all;
        }
        
        # Cache static assets
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
    }
}
```

#### `docker/supervisord.conf`
```ini
[supervisord]
nodaemon=true
user=root

[program:php-fpm]
command=php-fpm8.5 -F
user=www-data
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/php-fpm.log
stderr_logfile=/var/log/supervisor/php-fpm.err.log

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/nginx.log
stderr_logfile=/var/log/supervisor/nginx.err.log

[program:queue-worker]
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
user=www-data
numprocs=2
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/queue.log
stderr_logfile=/var/log/supervisor/queue.err.log

[program:scheduler]
command=php /var/www/html/artisan schedule:work
user=www-data
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/scheduler.log
stderr_logfile=/var/log/supervisor/scheduler.err.log
```

#### `docker-compose.yml`
```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
      - letsencrypt:/etc/letsencrypt
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - REDIS_HOST=redis
    depends_on:
      - db
      - redis
    restart: unless-stopped

  db:
    image: mysql:8.0
    volumes:
      - db_data:/var/lib/mysql
    environment:
      - MYSQL_DATABASE=genta_production
      - MYSQL_USER=genta_user
      - MYSQL_PASSWORD=secure_password
      - MYSQL_ROOT_PASSWORD=root_password
    restart: unless-stopped

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    command: redis-server --requirepass redis_password
    restart: unless-stopped

volumes:
  db_data:
  redis_data:
  letsencrypt:
```

#### Deploy with Docker
```bash
# Build
docker-compose build

# Run migrations
docker-compose run --rm app php artisan migrate --force

# Start
docker-compose up -d

# View logs
docker-compose logs -f
```

### Method 3: Traditional VPS (Ubuntu 22.04+)

#### Server Setup
```bash
# Update
sudo apt update && sudo apt upgrade -y

# Install PHP 8.5
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.5 php8.5-fpm php8.5-mysql php8.5-redis php8.5-gd php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip php8.5-bcmath php8.5-intl

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Redis
sudo apt install -y redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

#### Application Deployment
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/your-org/genta-banten.git
sudo chown -R www-data:www-data genta-banten
cd genta-banten

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci && sudo -u www-data npm run build

# Environment
sudo cp .env.example .env
sudo vim .env  # Configure production values

# Generate key
sudo -u www-data php artisan key:generate

# Migrate database
sudo -u www-data php artisan migrate --force

# Storage link
sudo -u www-data php artisan storage:link

# Optimize
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Nginx Configuration (`/etc/nginx/sites-available/genta-banten`)
```nginx
server {
    listen 80;
    server_name genta-banten.go.id www.genta-banten.go.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name genta-banten.go.id www.genta-banten.go.id;
    root /var/www/genta-banten/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/genta-banten.go.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/genta-banten.go.id/privkey.pem;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

#### Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/genta-banten /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# SSL with Let's Encrypt
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d genta-banten.go.id -d www.genta-banten.go.id
```

#### Supervisor for Queue (`/etc/supervisor/conf.d/genta.conf`)
```ini
[program:genta-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/genta-banten/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/genta-banten/storage/logs/queue.log

[program:genta-scheduler]
command=php /var/www/genta-banten/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/genta-banten/storage/logs/scheduler.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

## Post-Deployment Checklist

### Verification
```bash
# Test application
curl -I https://genta-banten.go.id

# Test login
curl -X POST https://genta-banten.go.id/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}'

# Test queue
php artisan queue:work --once

# Test scheduler
php artisan schedule:run

# Check logs
tail -f storage/logs/laravel.log
```

### Monitoring Setup
```bash
# Laravel Telescope (dev only)
# composer require laravel/telescope --dev
# php artisan telescope:install

# Laravel Pulse (production monitoring)
composer require laravel/pulse
php artisan pulse:install
```

### Backup Automation
```bash
# Add to crontab
0 2 * * * /usr/bin/php /var/www/genta-banten/artisan backup:run --only-db >> /var/log/backup.log 2>&1
0 3 * * 0 /usr/bin/php /var/www/genta-banten/artisan backup:clean >> /var/log/backup.log 2>&1
```

## Rollback Procedure

### Laravel Cloud
```bash
php artisan cloud:rollback
```

### Docker
```bash
# Tag current as backup
docker tag genta-app:latest genta-app:backup-$(date +%Y%m%d)

# Deploy previous version
docker-compose down
docker tag genta-app:previous genta-app:latest
docker-compose up -d
```

### Traditional
```bash
# Keep previous releases
/var/www/
├── releases/
│   ├── 20261026100000/
│   ├── 20261025100000/
│   └── 20261024100000/
├── current -> releases/20261026100000
└── shared/
    ├── .env
    ├── storage/
    └── bootstrap/cache/

# Rollback
ln -sfn /var/www/releases/20261025100000 /var/www/current
sudo systemctl reload php8.5-fpm nginx
```

## Health Checks

### Application Health Endpoint
```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'redis' => Redis::connection()->ping() ? 'connected' : 'disconnected',
        'queue' => Queue::size() < 1000 ? 'healthy' : 'backlogged',
    ]);
})->name('health');
```

### Load Balancer Health Check
```nginx
# In load balancer config
location /health {
    access_log off;
    proxy_pass http://backend;
}
```

## Security Hardening

### Firewall (UFW)
```bash
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### Fail2Ban
```bash
sudo apt install fail2ban
# Configure /etc/fail2ban/jail.local for nginx/php
```

### Database Security
```sql
-- Create read-only user for reporting
CREATE USER 'genta_report'@'%' IDENTIFIED BY 'password';
GRANT SELECT ON genta_production.* TO 'genta_report'@'%';
```

## Performance Tuning

### OPcache
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

### Database
```sql
-- Analyze tables
ANALYZE TABLE users, dapurs, cctvs, absensis, sp_warnings, cctv_anomaly_reports;

-- Optimize
OPTIMIZE TABLE absensis, cctv_anomaly_reports;
```

### Queue Workers
```bash
# Horizontal scaling
# Run multiple workers on different servers
php artisan queue:work redis --queue=high,default,low --sleep=3 --tries=3
```

## Troubleshooting

### Common Issues
| Issue | Solution |
|-------|----------|
| 500 Error | Check `storage/logs/laravel.log` |
| Assets not loading | Run `npm run build`, check `public/build` |
| Queue not processing | Check supervisor status, Redis connection |
| Migration failed | Check foreign key constraints, run `migrate:fresh` |
| Permission denied | `chown -R www-data:www-data storage bootstrap/cache` |
| SSL errors | Renew certbot, check nginx config |

### Debug Commands
```bash
# Clear all caches
php artisan optimize:clear

# Check config
php artisan config:show app

# Check routes
php artisan route:list --name=admin

# Check queue
php artisan queue:monitor

# Test email
php artisan tinker --execute 'Mail::raw("Test", function($m) { $m->to("test@example.com")->subject("Test"); });'
```