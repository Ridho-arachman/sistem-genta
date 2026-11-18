# DESIGN_SYSTEM — GENTA Pangan Banten

**Status**: aktif · 2026-11-18 · sumber: `resources/css/app.css`, `resources/views`

## Token
**Font**: Inter (body), Space Grotesk (heading/display), JetBrains Mono (code) — self-hosted via `@fontsource/*`, tanpa CDN.
**Radii**: selector 1rem, field 0.5rem, box 0.875rem. **Primary**: `#e50000` (gelap `#b20000`, terang `#ff3333`).

### daisyUI theme `genta` (default, `themes: false`)
| Role | Nilai |
|---|---|
| base-100 / base-200 / base-content | `#ffffff` / `#f5f5f5` / `#000000` |
| primary / primary-content | `#e50000` / `#ffffff` |
| secondary / secondary-content | `#000000` / `#ffffff` |
| accent / warning | `#f59e0b` |
| success | `#22c55e` · info `#3b82f6` · error `#ef4444` · neutral `#0f172a` |

### `@theme` custom
`--color-background:#ffffff`, `--color-surface:#f5f5f5`, `--color-surface-hover:#eeeeee`, `--color-border:#e0e0e0`, `--color-text:#000000`, `--color-text-muted:#666666`. Sektor komoditas (landing): `agri-green/light/dark`, `livestock`, `fishery`, `umkm`. Animasi `--animate-fade-up` (keyframes `fade-up`).

## Komponen
- **Layout**: `layouts/base.blade.php` (root + `<x-toast />`), `admin/layouts/app.blade.php`, `pengawas/layouts/app.blade.php`.
- **Admin announcements**: `<x-admin::page-heading>`, `<x-admin::panel>`, `<x-admin::stat-card>`, `<x-admin::table>`, `<x-admin::form-field>`, `<x-admin::empty-state>` (dir `app/Http/...` → `resources/views/admin/components/`, registered `Blade::anonymousComponentPath(...,'admin')`).
- **Pengawas**: `<x-pengawas::page-heading>`, `<x-pengawas::dapur-card>`, `<x-pengawas::foto-upload>`, `<x-pengawas::hasil-indikator>`, dll. (dir `pengawas/components/`, prefix `pengawas`).
- **Umum**: `<x-navbar-web>` / `<x-navbar-app>` / `<x-navbar-guest>`, `<x-heroicon>`, `<x-status-badge>`, `<x-toast>`, `<x-google-map>`, `<x-kitchen-filter>`.
- **Tombol daisyUI**: `btn btn-primary|btn-secondary|btn-ghost|btn-outline`; **input**: `input input-bordered|select select-bordered`; **badge**: `badge-success|warning|error|outline`; **card**: `card border border-[var(--color-border)] rounded-[14px] bg-base-100`; **progress**: `progress progress-primary`.

## Focus & aksesibilitas
`focus-visible` outline 2px primary di `a, button` (bukan `input` — input pakai ring dari daisyUI/`focus:ring-2`). `html lang="id"`. Sidebar admin: tombol hamburger dengan `aria-expanded`/`aria-label`. Tabel ber-search: `x-data` + `x-model`. **Prinsip**: semua aksi punya label teks atau `aria-label`.

## Anti-pattern (jangan lakukan)
- Menambah font CDN baru.
- Menulis warna hex bebas di Blade — pakai variabel `--color-*` atau kelas daisyUI.
- Inline `<style>`/`<script>` besar di view (gunakan modul di `resources/js`).
- CSS custom di luar `@theme` untuk warna yang sudah ada.
