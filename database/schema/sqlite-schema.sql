CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" varchar,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "dapurs"(
  "id" varchar not null,
  "nama" varchar not null,
  "lokasi" text,
  "alamat" text,
  "created_at" datetime,
  "updated_at" datetime,
  "kecamatan" varchar,
  "kabupaten_kota" varchar,
  "provinsi" varchar,
  "map_embed_url" text,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "cctvs"(
  "id" varchar not null,
  "dapur_id" varchar not null,
  "nama" varchar not null,
  "rtsp_url" varchar,
  "status" varchar check("status" in('online', 'offline', 'maintenance')) not null default 'offline',
  "created_at" datetime,
  "updated_at" datetime,
  "section" varchar,
  "camera_number" integer,
  "stream_key" varchar,
  "last_checked_at" datetime,
  foreign key("dapur_id") references "dapurs"("id") on delete cascade,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "fcm_tokens"(
  "id" varchar not null,
  "user_id" varchar not null,
  "token" varchar not null,
  "platform" varchar check("platform" in('android', 'ios', 'web')) not null default 'android',
  "last_used_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "fcm_tokens_user_id_token_unique" on "fcm_tokens"(
  "user_id",
  "token"
);
CREATE INDEX "fcm_tokens_user_id_last_used_at_index" on "fcm_tokens"(
  "user_id",
  "last_used_at"
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" varchar not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "users"(
  id varchar(26) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  role VARCHAR(20) NOT NULL DEFAULT 'pengawas' CHECK(role IN('admin', 'pengawas', 'manajerial', 'kepala_dapur')),
  registered_by varchar(26) NULL REFERENCES users(id) ON DELETE SET NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  email_verified_at DATETIME NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
  ,
  "dapur_id" varchar,
  "deleted_at" datetime,
  "created_by" varchar,
  "updated_by" varchar,
  "deleted_by" varchar
);
CREATE TABLE IF NOT EXISTS "sp_warnings"(
  "id" varchar not null,
  "user_id" varchar not null,
  "admin_id" varchar not null,
  "level" integer not null,
  "reason" text not null,
  "status" varchar check("status" in('aktif', 'dicabut', 'selesai')) not null default 'aktif',
  "issued_at" datetime not null default CURRENT_TIMESTAMP,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("admin_id") references "users"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "sp_warnings_user_id_status_index" on "sp_warnings"(
  "user_id",
  "status"
);
CREATE INDEX "sp_warnings_admin_id_created_at_index" on "sp_warnings"(
  "admin_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "cctv_anomaly_reports"(
  "id" varchar not null,
  "cctv_id" varchar not null,
  "admin_id" varchar not null,
  "description" text not null,
  "severity" varchar check("severity" in('rendah', 'sedang', 'tinggi')) not null default 'sedang',
  "status" varchar check("status" in('baru', 'diproses', 'selesai')) not null default 'baru',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("cctv_id") references "cctvs"("id") on delete cascade,
  foreign key("admin_id") references "users"("id") on delete cascade,
  primary key("id")
);
CREATE INDEX "cctv_anomaly_reports_cctv_id_status_index" on "cctv_anomaly_reports"(
  "cctv_id",
  "status"
);
CREATE INDEX "cctv_anomaly_reports_admin_id_created_at_index" on "cctv_anomaly_reports"(
  "admin_id",
  "created_at"
);
CREATE TABLE IF NOT EXISTS "settings"(
  "id" varchar not null,
  "key" varchar not null,
  "value" text,
  "type" varchar not null default 'string',
  "group" varchar not null default 'general',
  "label" varchar,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "settings_key_unique" on "settings"("key");
CREATE TABLE IF NOT EXISTS "laporan_hasil"(
  "id" varchar not null,
  "laporan_id" varchar not null,
  "indikator" varchar not null,
  "hasil" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "created_by" varchar,
  "updated_by" varchar,
  foreign key("laporan_id") references "laporans"("id") on delete cascade on update no action,
  foreign key("created_by") references "users"("id"),
  foreign key("updated_by") references "users"("id"),
  primary key("id")
);
CREATE UNIQUE INDEX "laporan_hasil_laporan_id_indikator_unique" on "laporan_hasil"(
  "laporan_id",
  "indikator"
);
CREATE TABLE IF NOT EXISTS "laporans"(
  "id" varchar not null,
  "dapur_id" varchar not null,
  "status" varchar not null default('diperiksa'),
  "catatan" text,
  "selfie_photo" text,
  "latitude" numeric,
  "longitude" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  "address" text,
  "created_by" varchar,
  "updated_by" varchar,
  foreign key("dapur_id") references "dapurs"("id") on delete cascade on update no action,
  foreign key("created_by") references "users"("id") on delete no action on update no action,
  foreign key("updated_by") references "users"("id") on delete no action on update no action,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "dapur_user"(
  "dapur_id" varchar not null,
  "user_id" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update no action,
  foreign key("dapur_id") references "dapurs"("id") on delete cascade on update no action,
  primary key("dapur_id", "user_id")
);
CREATE UNIQUE INDEX "dapur_user_dapur_id_user_id_unique" on "dapur_user"(
  "dapur_id",
  "user_id"
);
CREATE INDEX "cctvs_dapur_id_status_index" on "cctvs"("dapur_id", "status");
CREATE UNIQUE INDEX "cctvs_stream_key_unique" on "cctvs"("stream_key");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_reserved_available_index" on "jobs"("queue","reserved_at","available_at");
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" varchar not null,
  "queue" varchar not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE INDEX "failed_jobs_connection_queue_failed_at_index" on "failed_jobs"(
  "connection",
  "queue",
  "failed_at"
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE INDEX "laporans_dapur_id_status_index" on "laporans"(
  "dapur_id",
  "status"
);
CREATE INDEX "laporans_created_by_index" on "laporans"("created_by");
CREATE INDEX "laporans_created_at_index" on "laporans"("created_at");
CREATE INDEX "dapur_user_user_id_index" on "dapur_user"("user_id");

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_10_24_120931_create_dapurs_table',1);
INSERT INTO migrations VALUES(5,'2026_10_24_120932_create_cctvs_table',1);
INSERT INTO migrations VALUES(6,'2026_10_24_120933_create_absensis_table',1);
INSERT INTO migrations VALUES(7,'2026_10_24_121019_add_role_to_users_table',1);
INSERT INTO migrations VALUES(8,'2026_10_24_150948_add_hls_url_to_cctvs_table',1);
INSERT INTO migrations VALUES(9,'2026_10_24_161351_create_fcm_tokens_table',1);
INSERT INTO migrations VALUES(10,'2026_10_24_161355_add_geofence_to_dapurs_table',1);
INSERT INTO migrations VALUES(11,'2026_10_24_162139_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(12,'2026_10_24_194524_update_users_table_add_registered_by_and_roles',1);
INSERT INTO migrations VALUES(13,'2026_10_24_194532_add_section_and_camera_number_to_cctvs_table',1);
INSERT INTO migrations VALUES(14,'2026_10_24_194535_create_sp_warnings_table',1);
INSERT INTO migrations VALUES(15,'2026_10_24_194543_create_cctv_anomaly_reports_table',1);
INSERT INTO migrations VALUES(16,'2026_10_25_194714_add_unique_constraint_to_absensis_table',1);
INSERT INTO migrations VALUES(17,'2026_10_27_091845_add_dapur_id_to_users_and_create_dapur_user_table',1);
INSERT INTO migrations VALUES(18,'2026_10_27_133730_add_is_active_and_audit_to_users_table',1);
INSERT INTO migrations VALUES(19,'2026_10_27_133735_add_soft_deletes_to_users_table',1);
INSERT INTO migrations VALUES(20,'2026_10_27_152049_remove_is_active_and_audit_from_users_table',1);
INSERT INTO migrations VALUES(21,'2026_10_27_160715_create_settings_table',1);
INSERT INTO migrations VALUES(22,'2026_10_28_055901_add_audit_columns_to_users_table',1);
INSERT INTO migrations VALUES(23,'2026_10_28_131311_add_wilayah_columns_to_dapurs_table',1);
INSERT INTO migrations VALUES(24,'2026_10_30_173840_create_laporans_table',1);
INSERT INTO migrations VALUES(25,'2026_11_01_084751_create_laporan_photos_table',1);
INSERT INTO migrations VALUES(26,'2026_11_02_163142_add_indikator_and_address_to_laporan_photos_table',1);
INSERT INTO migrations VALUES(27,'2026_11_02_220443_drop_absensis_table',1);
INSERT INTO migrations VALUES(28,'2026_11_03_030653_add_user_id_to_laporans_table',1);
INSERT INTO migrations VALUES(29,'2026_11_03_040230_drop_lat_long_from_laporan_photos',1);
INSERT INTO migrations VALUES(30,'2026_11_03_041432_add_address_to_laporans_table',1);
INSERT INTO migrations VALUES(31,'2026_11_03_041455_drop_address_from_laporan_photos',1);
INSERT INTO migrations VALUES(32,'2026_11_03_050341_create_laporan_hasil_table',1);
INSERT INTO migrations VALUES(33,'2026_11_03_055114_add_audit_columns_to_laporan_hasil_table',1);
INSERT INTO migrations VALUES(34,'2026_11_03_055115_add_audit_columns_to_laporans_table',1);
INSERT INTO migrations VALUES(35,'2026_11_03_055116_add_audit_columns_to_laporan_photos_table',1);
INSERT INTO migrations VALUES(36,'2026_11_03_061433_drop_user_id_from_laporans_table',1);
INSERT INTO migrations VALUES(37,'2026_11_03_150325_drop_laporan_photos_table',1);
INSERT INTO migrations VALUES(38,'2026_11_03_154827_alter_selfie_photo_column_to_text_on_laporans_table',1);
INSERT INTO migrations VALUES(39,'2026_11_03_170604_add_map_embed_url_to_dapurs_table',1);
INSERT INTO migrations VALUES(40,'2026_11_03_170906_remove_lat_long_geofence_from_dapurs_table',1);
INSERT INTO migrations VALUES(41,'2026_11_09_193728_fix_dapur_user_pivot_table',1);
INSERT INTO migrations VALUES(42,'2026_11_17_000000_add_stream_key_and_last_checked_at_to_cctvs_table',1);
INSERT INTO migrations VALUES(43,'2026_11_18_000000_fix_jobs_table_primary_keys',1);
INSERT INTO migrations VALUES(44,'2026_11_18_000001_drop_cctv_legacy_url_columns',1);
INSERT INTO migrations VALUES(45,'2026_11_18_000002_add_performance_indexes',1);
