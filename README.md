# Tumpak Sewu Reservation System

Sistem reservasi layanan pendamping (guide, transport, paket) untuk Tumpak Sewu.
**BUKAN** sistem tiket masuk resmi. Tiket resmi dikelola pemerintah via cobansewu.com sejak 27 Feb 2025.

## Stack
Laravel 13, PHP 8.3, MySQL 8, Redis, XAMPP 3.3.0, Chart.js

## Setup
1. `composer install && npm install`
2. Copy `.env.example` -> `.env`, atur DB & Redis
3. `php artisan key:generate && php artisan migrate --seed`
4. XAMPP VirtualHost: `DocumentRoot "C:/xampp/htdocs/project/public"`
5. Install Redis (Memurai/WSL2) + `php_redis.dll` sesuai versi PHP
6. `php artisan queue:work`

## Aturan Performa
- Paginasi maksimal 15 baris per tampilan (server-side clamp)
- Index wajib pada kolom filter/sort — lihat `docs/PRD-addendum-indexing.md`
- Eager loading wajib; `preventLazyLoading()` di dev
