# Database Schema Documentation

## Overview
Complete database schema for GENTA Pangan Banten application with all tables, relationships, and indexes.

## Entity Relationship Diagram

```
users (1) ─────< (N) absensis > (N) ───── (1) dapurs
    │                                        │
    │                                        │
    └─────< (N) sp_warnings (via dapur)      │
    │                                        │
    └─────< (N) cctv_anomaly_reports (via cctv) ── (N) < cctvs > (1) ──── dapurs
```

## Tables

### 1. `users`
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manajerial','pengawas','kepala_dapur') NOT NULL DEFAULT 'pengawas',
    dapur_id BIGINT UNSIGNED NULL,
    fcm_token VARCHAR(255) NULL,
    fcm_platform ENUM('android','ios') NULL,
    fcm_updated_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_users_role (role),
    INDEX idx_users_dapur_id (dapur_id),
    INDEX idx_users_fcm_token (fcm_token),
    FOREIGN KEY (dapur_id) REFERENCES dapurs(id) ON DELETE SET NULL
);
```

### 2. `dapurs`
```sql
CREATE TABLE dapurs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    lokasi TEXT NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    status ENUM('operasional','perlu_perhatian','non_operasional') NOT NULL DEFAULT 'operasional',
    progress INT UNSIGNED NOT NULL DEFAULT 0,
    geofence_radius INT UNSIGNED NOT NULL DEFAULT 100,
    user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_dapurs_status (status),
    INDEX idx_dapurs_user_id (user_id),
    INDEX idx_dapurs_coordinates (latitude, longitude),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### 3. `cctvs`
```sql
CREATE TABLE cctvs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dapur_id BIGINT UNSIGNED NOT NULL,
    nama VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NULL,
    rtsp_url VARCHAR(500) NULL,
    status ENUM('online','offline','maintenance') NOT NULL DEFAULT 'offline',
    location_type ENUM('persiapan','memasak','pengemasan','distribusi') NULL,
    keterangan TEXT NULL,
    last_online_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_cctvs_dapur_id (dapur_id),
    INDEX idx_cctvs_status (status),
    INDEX idx_cctvs_location_type (location_type),
    FOREIGN KEY (dapur_id) REFERENCES dapurs(id) ON DELETE CASCADE
);
```

### 4. `absensis`
```sql
CREATE TABLE absensis (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    dapur_id BIGINT UNSIGNED NOT NULL,
    status ENUM('hadir','izin','cuti','sakit','alpha') NOT NULL DEFAULT 'hadir',
    check_in_at TIMESTAMP NULL,
    check_out_at TIMESTAMP NULL,
    check_in_lat DECIMAL(10,8) NULL,
    check_in_lng DECIMAL(11,8) NULL,
    check_out_lat DECIMAL(10,8) NULL,
    check_out_lng DECIMAL(11,8) NULL,
    check_in_accuracy DECIMAL(8,2) NULL,
    check_out_accuracy DECIMAL(8,2) NULL,
    check_in_photo TEXT NULL,
    check_out_photo TEXT NULL,
    is_within_geofence BOOLEAN NOT NULL DEFAULT FALSE,
    distance_from_dapur DECIMAL(10,2) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY uk_absensi_daily (user_id, dapur_id, check_in_at),
    INDEX idx_absensis_user_id (user_id),
    INDEX idx_absensis_dapur_id (dapur_id),
    INDEX idx_absensis_check_in_at (check_in_at),
    INDEX idx_absensis_status (status),
    INDEX idx_absensis_geofence (is_within_geofence),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dapur_id) REFERENCES dapurs(id) ON DELETE CASCADE
);
```

### 5. `sp_warnings`
```sql
CREATE TABLE sp_warnings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    dapur_id BIGINT UNSIGNED NOT NULL,
    issued_by BIGINT UNSIGNED NOT NULL,
    level ENUM('SP1','SP2','SP3') NOT NULL DEFAULT 'SP1',
    nomor_surat VARCHAR(50) NOT NULL UNIQUE,
    pelanggaran TEXT NOT NULL,
    dasar_hukum TEXT NULL,
    tanggal_terbit DATE NOT NULL,
    tanggal_tenggat DATE NULL,
    status ENUM('draft','terbit','diterima','ditanggapi','selesai','batal') NOT NULL DEFAULT 'draft',
    tanggapan TEXT NULL,
    tanggapan_at TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY uk_sp_nomor_surat (nomor_surat),
    INDEX idx_sp_warnings_dapur_id (dapur_id),
    INDEX idx_sp_warnings_status (status),
    INDEX idx_sp_warnings_level (level),
    INDEX idx_sp_warnings_tanggal_terbit (tanggal_terbit),
    INDEX idx_sp_warnings_tanggal_tenggat (tanggal_tenggat),
    FOREIGN KEY (dapur_id) REFERENCES dapurs(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 6. `cctv_anomaly_reports`
```sql
CREATE TABLE cctv_anomaly_reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    cctv_id BIGINT UNSIGNED NOT NULL,
    reported_by BIGINT UNSIGNED NOT NULL,
    type ENUM('offline','blurry','frozen','no_signal','tampered','other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved','dismissed') NOT NULL DEFAULT 'open',
    resolved_by BIGINT UNSIGNED NULL,
    resolved_at TIMESTAMP NULL,
    resolution_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_anomaly_cctv_id (cctv_id),
    INDEX idx_anomaly_reported_by (reported_by),
    INDEX idx_anomaly_status (status),
    INDEX idx_anomaly_type (type),
    FOREIGN KEY (cctv_id) REFERENCES cctvs(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 7. `password_reset_tokens` (Laravel default)
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

### 8. `sessions` (Laravel default)
```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
);
```

### 9. `migrations` (Laravel default)
```sql
CREATE TABLE migrations (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
);
```

### 10. `failed_jobs` (Laravel default)
```sql
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## Migrations Order

| Order | Migration | Description |
|-------|-----------|-------------|
| 1 | `2014_10_12_000000_create_users_table` | Users table |
| 2 | `2014_10_12_100000_create_password_reset_tokens_table` | Password reset |
| 3 | `2019_08_19_000000_create_failed_jobs_table` | Failed jobs |
| 4 | `2019_12_14_000001_create_personal_access_tokens_table` | Sanctum tokens |
| 5 | `2024_01_01_000000_create_dapurs_table` | Kitchens |
| 6 | `2024_01_02_000000_create_cctvs_table` | CCTV devices |
| 7 | `2024_01_03_000000_create_absensis_table` | Attendance |
| 8 | `2024_01_04_000000_create_sp_warnings_table` | SP Warnings |
| 9 | `2024_01_05_000000_create_cctv_anomaly_reports_table` | Anomaly reports |
| 10 | `2026_10_25_194714_add_unique_constraint_to_absensi_table` | Unique constraint |

## Indexes Summary

| Table | Indexes |
|-------|---------|
| users | email (unique), role, dapur_id, fcm_token |
| dapurs | status, user_id, coordinates |
| cctvs | dapur_id, status, location_type |
| absensis | user_id, dapur_id, check_in_at, status, geofence, **unique (user,dapur,date)** |
| sp_warnings | dapur_id, status, level, tanggal_terbit, tanggal_tenggat, **nomor_surat (unique)** |
| cctv_anomaly_reports | cctv_id, reported_by, status, type |

## Relationships

### One-to-Many
- User → Absensi (hasMany)
- User → SpWarning (issued_by)
- User → CctvAnomalyReport (reported_by)
- Dapur → CCTV (hasMany)
- Dapur → Absensi (hasMany)
- Dapur → SpWarning (hasMany)
- CCTV → CctvAnomalyReport (hasMany)

### Many-to-One
- Absensi → User (belongsTo)
- Absensi → Dapur (belongsTo)
- CCTV → Dapur (belongsTo)
- SpWarning → Dapur (belongsTo)
- SpWarning → User (issued_by, verified_by)
- CctvAnomalyReport → CCTV (belongsTo)
- CctvAnomalyReport → User (reported_by, resolved_by)

### One-to-One (via dapur)
- User → Dapur (pengawas/kepala_dapur assigned)

## Seeders

### DatabaseSeeder
```php
public function run(): void
{
    // Admin
    User::factory()->admin()->create([
        'name' => 'Admin Genta',
        'email' => 'admin@genta-banten.go.id',
    ]);

    // Manajerial
    User::factory()->manajerial()->create([
        'name' => 'Manajerial Genta',
        'email' => 'manajerial@genta-banten.go.id',
    ]);

    // 4 Dapurs with pengawas & kepala_dapur
    $dapurs = Dapur::factory(4)->create([
        'status' => 'operasional',
        'progress' => fn() => fake()->numberBetween(70, 100),
    ]);
    
    foreach ($dapurs as $dapur) {
        User::factory()->pengawas()->create(['dapur_id' => $dapur->id]);
        User::factory()->kepalaDapur()->create(['dapur_id' => $dapur->id]);
        
        // 4 CCTVs per dapur
        Cctv::factory(4)->create([
            'dapur_id' => $dapur->id,
            'status' => fake()->randomElement(['online', 'online', 'online', 'offline']),
            'location_type' => fake()->randomElement(['persiapan', 'memasak', 'pengemasan', 'distribusi']),
        ]);
    }
}
```

## Common Queries

### Dashboard Stats
```php
// Admin/Manajerial dashboard
$stats = [
    'total_pengawas' => User::role('pengawas')->count(),
    'total_manajerial' => User::role('manajerial')->count(),
    'total_kepala_dapur' => User::role('kepala_dapur')->count(),
    'total_dapur' => Dapur::count(),
    'total_cctv' => Cctv::count(),
    'cctv_online' => Cctv::where('status', 'online')->count(),
    'cctv_offline' => Cctv::where('status', 'offline')->count(),
    'cctv_maintenance' => Cctv::where('status', 'maintenance')->count(),
    'absensi_hari_ini' => Absensi::whereDate('check_in_at', today())->count(),
    'sp_warnings_aktif' => SpWarning::whereIn('status', ['terbit','diterima','ditanggapi'])->count(),
    'anomaly_reports_open' => CctvAnomalyReport::where('status', 'open')->count(),
];
```

### Pengawas Dashboard Data
```php
// Own dapur only
$dapur = auth()->user()->dapur;
$cctvs = $dapur->cctvs;
$absensiToday = Absensi::where('user_id', auth()->id())
    ->whereDate('check_in_at', today())
    ->first();
```

### SP Warning Overdue
```php
SpWarning::where('status', '!=', 'selesai')
    ->where('status', '!=', 'batal')
    ->where('tanggal_tenggat', '<', now())
    ->with('dapur')
    ->get();
```

## Performance Optimization

### Eager Loading
```php
// Good
Dapur::with(['user', 'cctvs'])->get();
Absensi::with(['user', 'dapur'])->get();
SpWarning::with(['dapur', 'issuer', 'verifier'])->get();

// Avoid N+1
Dapur::all()->each(function ($dapur) {
    echo $dapur->user->name; // BAD - triggers query per dapur
});
```

### Chunking Large Datasets
```php
Absensi::whereDate('check_in_at', today())
    ->chunkById(100, function ($absensis) {
        foreach ($absensis as $absensi) {
            // Process
        }
    });
```

## Backup & Recovery

### Automated Backup (Laravel Backup)
```bash
# Daily backup
php artisan backup:run --only-db

# Configure in config/backup.php
'databases' => ['mysql'],
```

### Point-in-Time Recovery
```bash
# Binary log enabled on MySQL
mysqlbinlog --start-datetime="2026-10-26 10:00:00" --stop-datetime="2026-10-26 10:30:00" binlog.000001 | mysql -u root -p
```