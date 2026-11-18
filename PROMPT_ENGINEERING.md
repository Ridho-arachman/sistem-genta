# PROMPT_ENGINEERING — Panduan (belum ada fitur AI)

**Status**: tidak ada fitur AI aktif di GENTA Pangan Banten saat ini · 2026-11-18

Dokumen ini adalah panduan jika fitur AI ditambahkan nanti (mis. ringkasan otomatis laporan pengawasan, rekomendasi tindakan, atau chat asisten pengawas). Tidak ada kode AI di `app/` sekarang.

## Prinsip dasar (kalau dibangun)
- Prompt disimpan di `config/ai.php` atau `resources/ai/prompts/*.md`, di-version-control, bukan inline di controller.
- Template: system prompt (peran + konteks domain GENTA), few-shot contoh domain dapur Indonesia, instruksi output (JSON bila di-parse, plain text bila hanya ditampilkan).
- Selalu sebut zona waktu WIB dan terminologi domain (dapur, pengawas, MBG) di system prompt.

## Checklist sebelum merge fitur AI
- [ ] Tidak mengirim PII pengguna tanpa pseudonimisasi (email, nama pengawas boleh, tapi pertimbangkan masking).
- [ ] Timeout dan retry terbatas; hasil AI **tidak** mengubah status CCTV otomatis tanpa konfirmasi manusia.
- [ ] Output di-escape sebelum ditampilkan (mencegah HTML/JS injection dari model).
- [ ] Ada fallback ketika model tidak tersedia (tampilkan UI tanpa AI, bukan error).
- [ ] Ada evaluasi sebelum rilis (lihat `AI_EVALUATION_AND_BENCHMARKS.md`).

## Jangan lakukan
- Menggunakan LLM untuk memutuskan status kamera (itu sudah ditangani ffprobe).
- Menyimpan API key di repo.
