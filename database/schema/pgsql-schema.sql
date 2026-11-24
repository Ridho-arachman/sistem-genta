--
-- PostgreSQL database dump
--

\restrict sOwbAoeQxEcChYE8z05KpsUTp1aqKRkaKIKoHdg5Y11BW7h528zovjzfMggLH7b

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cctv_anomaly_reports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cctv_anomaly_reports (
    id character(26) NOT NULL,
    cctv_id character(26) NOT NULL,
    admin_id character(26) NOT NULL,
    description text NOT NULL,
    severity character varying(255) DEFAULT 'sedang'::character varying NOT NULL,
    status character varying(255) DEFAULT 'baru'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT cctv_anomaly_reports_severity_check CHECK (((severity)::text = ANY ((ARRAY['rendah'::character varying, 'sedang'::character varying, 'tinggi'::character varying])::text[]))),
    CONSTRAINT cctv_anomaly_reports_status_check CHECK (((status)::text = ANY ((ARRAY['baru'::character varying, 'diproses'::character varying, 'selesai'::character varying])::text[])))
);


--
-- Name: cctvs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cctvs (
    id character(26) NOT NULL,
    dapur_id character(26) NOT NULL,
    nama character varying(255) NOT NULL,
    rtsp_url character varying(255),
    status character varying(255) DEFAULT 'offline'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    section character varying(255),
    camera_number integer,
    stream_key character(26),
    last_checked_at timestamp(0) without time zone,
    CONSTRAINT cctvs_status_check CHECK (((status)::text = ANY ((ARRAY['online'::character varying, 'offline'::character varying, 'maintenance'::character varying])::text[])))
);


--
-- Name: dapur_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dapur_user (
    dapur_id character(26) NOT NULL,
    user_id character(26) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: dapurs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.dapurs (
    id character(26) NOT NULL,
    nama character varying(255) NOT NULL,
    lokasi text,
    alamat text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    kecamatan character varying(100),
    kabupaten_kota character varying(100),
    provinsi character varying(100),
    map_embed_url text
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fcm_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.fcm_tokens (
    id character(26) NOT NULL,
    user_id character(26) NOT NULL,
    token character varying(255) NOT NULL,
    platform character varying(255) DEFAULT 'android'::character varying NOT NULL,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT fcm_tokens_platform_check CHECK (((platform)::text = ANY ((ARRAY['android'::character varying, 'ios'::character varying, 'web'::character varying])::text[])))
);


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: laporan_hasil; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.laporan_hasil (
    id character(26) NOT NULL,
    laporan_id character(26) NOT NULL,
    indikator character varying(255) NOT NULL,
    hasil character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    created_by character(26),
    updated_by character(26),
    CONSTRAINT laporan_hasil_hasil_check CHECK (((hasil)::text = ANY ((ARRAY['diperiksa'::character varying, 'temuan_sedang'::character varying, 'temuan_kritis'::character varying])::text[])))
);


--
-- Name: laporans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.laporans (
    id character(26) NOT NULL,
    dapur_id character(26) NOT NULL,
    status character varying(255) DEFAULT 'diperiksa'::character varying NOT NULL,
    catatan text,
    selfie_photo text,
    latitude numeric(10,8),
    longitude numeric(11,8),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    address text,
    created_by character(26),
    updated_by character(26),
    CONSTRAINT laporans_status_check CHECK (((status)::text = ANY ((ARRAY['diperiksa'::character varying, 'temuan'::character varying, 'tindak lanjut'::character varying, 'selesai'::character varying])::text[])))
);


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id character(26) NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id character(26),
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.settings (
    id character(26) NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    type character varying(255) DEFAULT 'string'::character varying NOT NULL,
    "group" character varying(255) DEFAULT 'general'::character varying NOT NULL,
    label character varying(255),
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: sp_warnings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sp_warnings (
    id character(26) NOT NULL,
    user_id character(26) NOT NULL,
    admin_id character(26) NOT NULL,
    level smallint NOT NULL,
    reason text NOT NULL,
    status character varying(255) DEFAULT 'aktif'::character varying NOT NULL,
    issued_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT sp_warnings_status_check CHECK (((status)::text = ANY ((ARRAY['aktif'::character varying, 'dicabut'::character varying, 'selesai'::character varying])::text[])))
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id character(26) NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role character varying(20) DEFAULT 'pengawas'::character varying NOT NULL,
    registered_by character(26),
    dapur_id character(26),
    deleted_at timestamp(0) without time zone,
    created_by character(26),
    updated_by character(26),
    deleted_by character(26),
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'pengawas'::character varying, 'manajerial'::character varying, 'kepala_dapur'::character varying])::text[])))
);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cctv_anomaly_reports cctv_anomaly_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctv_anomaly_reports
    ADD CONSTRAINT cctv_anomaly_reports_pkey PRIMARY KEY (id);


--
-- Name: cctvs cctvs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctvs
    ADD CONSTRAINT cctvs_pkey PRIMARY KEY (id);


--
-- Name: cctvs cctvs_stream_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctvs
    ADD CONSTRAINT cctvs_stream_key_unique UNIQUE (stream_key);


--
-- Name: dapur_user dapur_user_dapur_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dapur_user
    ADD CONSTRAINT dapur_user_dapur_id_user_id_unique UNIQUE (dapur_id, user_id);


--
-- Name: dapur_user dapur_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dapur_user
    ADD CONSTRAINT dapur_user_pkey PRIMARY KEY (dapur_id, user_id);


--
-- Name: dapurs dapurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dapurs
    ADD CONSTRAINT dapurs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fcm_tokens fcm_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fcm_tokens
    ADD CONSTRAINT fcm_tokens_pkey PRIMARY KEY (id);


--
-- Name: fcm_tokens fcm_tokens_user_id_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fcm_tokens
    ADD CONSTRAINT fcm_tokens_user_id_token_unique UNIQUE (user_id, token);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: laporan_hasil laporan_hasil_laporan_id_indikator_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporan_hasil
    ADD CONSTRAINT laporan_hasil_laporan_id_indikator_unique UNIQUE (laporan_id, indikator);


--
-- Name: laporan_hasil laporan_hasil_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporan_hasil
    ADD CONSTRAINT laporan_hasil_pkey PRIMARY KEY (id);


--
-- Name: laporans laporans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporans
    ADD CONSTRAINT laporans_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_unique UNIQUE (key);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: sp_warnings sp_warnings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sp_warnings
    ADD CONSTRAINT sp_warnings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: cctv_anomaly_reports_admin_id_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cctv_anomaly_reports_admin_id_created_at_index ON public.cctv_anomaly_reports USING btree (admin_id, created_at);


--
-- Name: cctv_anomaly_reports_cctv_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cctv_anomaly_reports_cctv_id_status_index ON public.cctv_anomaly_reports USING btree (cctv_id, status);


--
-- Name: cctvs_dapur_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cctvs_dapur_id_status_index ON public.cctvs USING btree (dapur_id, status);


--
-- Name: dapur_user_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX dapur_user_user_id_index ON public.dapur_user USING btree (user_id);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: fcm_tokens_user_id_last_used_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fcm_tokens_user_id_last_used_at_index ON public.fcm_tokens USING btree (user_id, last_used_at);


--
-- Name: jobs_queue_reserved_available_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_reserved_available_index ON public.jobs USING btree (queue, reserved_at, available_at);


--
-- Name: laporans_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX laporans_created_at_index ON public.laporans USING btree (created_at);


--
-- Name: laporans_created_by_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX laporans_created_by_index ON public.laporans USING btree (created_by);


--
-- Name: laporans_dapur_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX laporans_dapur_id_status_index ON public.laporans USING btree (dapur_id, status);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: sp_warnings_admin_id_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sp_warnings_admin_id_created_at_index ON public.sp_warnings USING btree (admin_id, created_at);


--
-- Name: sp_warnings_user_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sp_warnings_user_id_status_index ON public.sp_warnings USING btree (user_id, status);


--
-- Name: cctv_anomaly_reports cctv_anomaly_reports_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctv_anomaly_reports
    ADD CONSTRAINT cctv_anomaly_reports_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: cctv_anomaly_reports cctv_anomaly_reports_cctv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctv_anomaly_reports
    ADD CONSTRAINT cctv_anomaly_reports_cctv_id_foreign FOREIGN KEY (cctv_id) REFERENCES public.cctvs(id) ON DELETE CASCADE;


--
-- Name: cctvs cctvs_dapur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cctvs
    ADD CONSTRAINT cctvs_dapur_id_foreign FOREIGN KEY (dapur_id) REFERENCES public.dapurs(id) ON DELETE CASCADE;


--
-- Name: dapur_user dapur_user_dapur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dapur_user
    ADD CONSTRAINT dapur_user_dapur_id_foreign FOREIGN KEY (dapur_id) REFERENCES public.dapurs(id) ON DELETE CASCADE;


--
-- Name: dapur_user dapur_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.dapur_user
    ADD CONSTRAINT dapur_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: fcm_tokens fcm_tokens_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fcm_tokens
    ADD CONSTRAINT fcm_tokens_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: laporan_hasil laporan_hasil_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporan_hasil
    ADD CONSTRAINT laporan_hasil_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: laporan_hasil laporan_hasil_laporan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporan_hasil
    ADD CONSTRAINT laporan_hasil_laporan_id_foreign FOREIGN KEY (laporan_id) REFERENCES public.laporans(id) ON DELETE CASCADE;


--
-- Name: laporan_hasil laporan_hasil_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporan_hasil
    ADD CONSTRAINT laporan_hasil_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id);


--
-- Name: laporans laporans_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporans
    ADD CONSTRAINT laporans_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: laporans laporans_dapur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporans
    ADD CONSTRAINT laporans_dapur_id_foreign FOREIGN KEY (dapur_id) REFERENCES public.dapurs(id) ON DELETE CASCADE;


--
-- Name: laporans laporans_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.laporans
    ADD CONSTRAINT laporans_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id);


--
-- Name: sp_warnings sp_warnings_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sp_warnings
    ADD CONSTRAINT sp_warnings_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: sp_warnings sp_warnings_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sp_warnings
    ADD CONSTRAINT sp_warnings_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users users_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: users users_dapur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_dapur_id_foreign FOREIGN KEY (dapur_id) REFERENCES public.dapurs(id) ON DELETE SET NULL;


--
-- Name: users users_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: users users_registered_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_registered_by_foreign FOREIGN KEY (registered_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: users users_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict sOwbAoeQxEcChYE8z05KpsUTp1aqKRkaKIKoHdg5Y11BW7h528zovjzfMggLH7b

--
-- PostgreSQL database dump
--

\restrict e9cs0CGC3iDDihlJLCohWwpV7WmxaJOtY1XDjbWYiODseN8h4rUX2afU7PaWIVk

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_10_24_120931_create_dapurs_table	1
5	2026_10_24_120932_create_cctvs_table	1
6	2026_10_24_120933_create_absensis_table	1
7	2026_10_24_121019_add_role_to_users_table	1
8	2026_10_24_150948_add_hls_url_to_cctvs_table	1
9	2026_10_24_161351_create_fcm_tokens_table	1
10	2026_10_24_161355_add_geofence_to_dapurs_table	1
11	2026_10_24_162139_create_personal_access_tokens_table	1
12	2026_10_24_194524_update_users_table_add_registered_by_and_roles	1
13	2026_10_24_194532_add_section_and_camera_number_to_cctvs_table	1
14	2026_10_24_194535_create_sp_warnings_table	1
15	2026_10_24_194543_create_cctv_anomaly_reports_table	1
16	2026_10_25_194714_add_unique_constraint_to_absensis_table	1
17	2026_10_27_091845_add_dapur_id_to_users_and_create_dapur_user_table	1
18	2026_10_27_133730_add_is_active_and_audit_to_users_table	1
19	2026_10_27_133735_add_soft_deletes_to_users_table	1
20	2026_10_27_152049_remove_is_active_and_audit_from_users_table	1
21	2026_10_27_160715_create_settings_table	1
22	2026_10_28_055901_add_audit_columns_to_users_table	1
23	2026_10_28_131311_add_wilayah_columns_to_dapurs_table	1
24	2026_10_30_173840_create_laporans_table	1
25	2026_11_01_084751_create_laporan_photos_table	1
26	2026_11_02_163142_add_indikator_and_address_to_laporan_photos_table	1
27	2026_11_02_220443_drop_absensis_table	1
28	2026_11_03_030653_add_user_id_to_laporans_table	1
29	2026_11_03_040230_drop_lat_long_from_laporan_photos	1
30	2026_11_03_041432_add_address_to_laporans_table	1
31	2026_11_03_041455_drop_address_from_laporan_photos	1
32	2026_11_03_050341_create_laporan_hasil_table	1
33	2026_11_03_055114_add_audit_columns_to_laporan_hasil_table	1
34	2026_11_03_055115_add_audit_columns_to_laporans_table	1
35	2026_11_03_055116_add_audit_columns_to_laporan_photos_table	1
36	2026_11_03_061433_drop_user_id_from_laporans_table	1
37	2026_11_03_150325_drop_laporan_photos_table	1
38	2026_11_03_154827_alter_selfie_photo_column_to_text_on_laporans_table	1
39	2026_11_03_170604_add_map_embed_url_to_dapurs_table	1
40	2026_11_03_170906_remove_lat_long_geofence_from_dapurs_table	1
41	2026_11_09_193728_fix_dapur_user_pivot_table	2
42	2026_11_17_000000_add_stream_key_and_last_checked_at_to_cctvs_table	3
43	2026_11_18_000000_fix_jobs_table_primary_keys	4
44	2026_11_18_000001_drop_cctv_legacy_url_columns	5
45	2026_11_18_000002_add_performance_indexes	6
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 47, true);


--
-- PostgreSQL database dump complete
--

\unrestrict e9cs0CGC3iDDihlJLCohWwpV7WmxaJOtY1XDjbWYiODseN8h4rUX2afU7PaWIVk

