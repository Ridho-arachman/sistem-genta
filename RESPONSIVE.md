# RESPONSIVE — Strategi responsif

**Status**: aktif · 2026-11-23

## Breakpoint (sesuai penggunaan nyata di Blade + JS)
Tailwind default: `sm` (640px), `md` (768px), `lg` (1024px), `xl` (1280px), `2xl`.
Paling sering dipakai `sm:`/`md:`/`lg:`; `xl:` dan `2xl:` tidak dipakai (angka pastinya dicari dengan grep, tidak dicatat di sini supaya tidak basi).
Selain itu ada **arbiter** `max-[900px]:*` pada sidebar admin (off-canvas di bawah 900px) dan `min-[901px]:translate-x-0`.

## Pola nyata
1. **Mobile-first** dengan kelas `sm:`, `md:`, `lg:` (bukan `max-*` untuk sebagian besar komponen).
2. **Admin shell**: sidebar fixed 230px; konten `lg:ml-[230px]`; hamburger `lg:hidden`; konten `pt-[64px]` (navbar fixed 72px, sidebar mulai `top-[72px]`).
3. **Tabel**: dibungkus `overflow-x-auto` di panel; halaman list admin tidak horizontal scroll saat < 640px.
4. **Grid stat**: `grid-cols-2 lg:grid-cols-4` (dashboard), `grid-cols-1 lg:grid-cols-2` (kartu dapur kepala-dapur).
5. **Form**: `grid-cols-1 md:grid-cols-3` untuk input berlabel; label kecil uppercase.
6. **Navbar**: brand text disembunyikan di < `sm:` (tinggal logo), nama user dipotong dengan `max-w-[160px] truncate`.
7. **Landing**: kelas reveal via Alpine `fade-up`.

## Aturan
- Untuk komponen baru, patok: default mobile → `sm:` (tablet kecil) → `md:` (tablet) → `lg:` (desktop). Jangan menambah `xl`/`2xl` kecuali benar-benar perlu.
- Off-canvas butuh `x-data` dan `max-[900px]:translate-x-full` / `-translate-x-full` flip.
- Sidebar harus `overflow-y-auto` dan `fixed top-[72px]`; body tidak scroll horizontal.
- Print style: `@media print` di `app.css` menyembunyikan `header`, `#sidebar`, `#toast` dan menghilangkan background.

## Testing manual minimal
- 360×640 (HP kecil): login, dashboard, list dapur → tidak overflow.
- 768×1024 (tablet): sidebar tetap off-canvas; tabel bisa scroll horizontal di container.
- 1280×800 (laptop): sidebar tampil; `lg:ml-[230px]` aktif.
