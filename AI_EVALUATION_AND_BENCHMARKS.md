# AI_EVALUATION_AND_BENCHMARKS — Evaluasi (belum ada fitur AI)

**Status**: tidak ada fitur AI · 2026-11-18

Belum ada model/LLM yang dipakai, jadi belum ada benchmark. Jika fitur AI ditambahkan, gunakan rubrik berikut.

## Rubrik evaluasi minimal (sebelum rilis)
| Aspek | Ukur | Ambang |
|---|---|---|
| Akurasi domain | jumlah jawaban benar pada ±50 pertanyaan domain dapur | ≥ 90% |
| Tidak berhalusinasi | klaim tidak didukung data pada set uji | 0 kritis |
| Format output | sesuai schema (JSON) bila diparse | 99% valid |
| Bahasa | Indonesia formal, tidak mengganti istilah domain | manual review |
| Latency | p95 response | < 4 detik |
| Biaya | rata-rata token per request | catat, tanpa ambang |

## Cara mengukur
- Set uji di `tests/fixtures/ai/` (belum ada; buat saat fitur ada).
- Script evaluasi: jalankan N pertanyaan, bandingkan dengan jawaban referensi; simpan hasil di `storage/app/ai-eval/` (gitignored).
- Untuk keputusan operasional (mis. prioritas anomaly), **manusia tetap penentu**; AI hanya menyarankan.

## Benchmark yang TIDAK berlaku di sistem ini
- Tidak ada benchmark vision (kamera dianalisis NVR/manusia, bukan LLM di sisi kita).
- Tidak ada fine-tuning; bila nanti ada, catat dataset (sumber, izin, tanggal, ukuran).
