# MEMORY_AND_CONTEXT — Memory (belum ada fitur AI)

**Status**: tidak ada fitur AI · 2026-11-18

"Memory" dalam konteks GENTA berarti penyimpanan konteks agar jawaban AI (jika nanti ada) konsisten antar sesi. Saat ini **belum ada** layer memory apapun — state aplikasi hanya di database relasional.

## Yang sudah ada (bukan AI, tapi "memory" sistem)
- `settings` (key-value pengaturan aplikasi).
- `fcm_tokens`, `personal_access_tokens`, `sessions`, `cache` (state ringan).
- `laporans` + `laporan_hasil` = riwayat tugas pengawas (bisa dipakai sebagai konteks kalau nanti ada ringkasan AI).

## Rancangan jika AI ditambahkan
1. **Short-term memory**: percakapan terakhir user (disimpan di tabel baru, TTL 24 jam, mis. `ai_messages`).
2. **Long-term memory**: ringkasan profil dapur/pengawas (tabel `ai_memories` atau hasil agregasi query langsung, tanpa tabel baru).
3. **Context retrieval**: ambil ±10 laporan terakhir + status kamera + SP aktif sebagai konteks (query, bukan vector DB — cukup untuk skala ini).
4. **Privacy**: memory hanya untuk user yang bersangkutan; admin bisa lihat (audit), pengawas hanya lihat miliknya.

## Jangan lakukan
- Menaruh PII user lain di konteks prompt pengawas.
- Membuat vector database / embedding service tanpa kebutuhan nyata (over-engineering untuk ±400 dapur).
- Menyimpan isi RTSP/token di memory apa pun.
