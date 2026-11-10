# PRD v1.0 — Sistem Reservasi Tumpak Sewu

## Catatan Lingkup
Produk BUKAN pengganti sistem tiket resmi (cobansewu.com). Fokus: reservasi guide, paket, rombongan.

## Tujuan
- G1: >=80% reservasi via sistem
- G2: 0 konflik slot
- G3: Dashboard <2 dtk
- G4: P95 query <200 ms

## Kebutuhan Fungsional Publik
| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-P1 | Landing + disclaimer | Must |
| FR-P2 | Katalog paket + filter tanggal | Must |
| FR-P3 | Kalender slot real-time | Must |
| FR-P4 | Form booking peserta | Must |
| FR-P5 | Pembayaran sandbox | Must |
| FR-P6 | E-voucher PDF + QR | Must |
| FR-P7 | Cek status booking | Should |
| FR-P8 | Kebijakan reschedule/cancel | Should |

## Kebutuhan Fungsional Admin
| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-A1 | Login + RBAC | Must |
| FR-A2 | CRUD paket/guide/slot/harga | Must |
| FR-A3 | Manajemen booking | Must |
| FR-A4 | Scanner QR check-in | Must |
| FR-A5 | Dashboard Chart.js | Must |
| FR-A6 | Export CSV manifest | Should |
| FR-A7 | Activity log | Should |

## Alur Booking
Pilih paket -> pilih slot -> isi data -> hold slot (Redis TTL 10mnt) -> bayar -> confirmed -> e-voucher -> check-in

## NFR
- Paginasi maks 15 baris (server-side clamp)
- Eager loading wajib, preventLazyLoading di dev
- Redis: cache dashboard TTL 60s, session, queue, lock slot SETNX
- CSRF, rate-limit, QR HMAC, DocumentRoot -> public/
- Transaksi DB + lock Redis cegah overselling

## Model Data
users, roles, packages, guides, slots, bookings, booking_items, payments, vouchers, checkins, activity_logs

## Milestone
M1 Setup+auth (1w) | M2 CRUD+katalog (2w) | M3 Booking+lock+pay (2w) | M4 Voucher+QR (1w) | M5 Dashboard+optimasi (1w) | M6 Uji+README (1w)

## Kriteria Penerimaan
- 10 request paralel slot terakhir -> 1 sukses
- Dashboard <2dtk pada 1000 booking, 0 N+1
- QR tervalidasi + tercatat checkins
- README lengkap + disclaimer

## Risiko
| Risiko | Mitigasi |
|--------|----------|
| Konflik sistem resmi | Layanan pendamping + disclaimer |
| Redis Windows rewel | Fallback predis |
| Trafik rendah | Jangan over-engineer |
| Data pribadi | Enkripsi kolom sensitif |
