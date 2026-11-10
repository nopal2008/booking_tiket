# Addendum v1.1 — Indexing & Paginasi 15

## Aturan Paginasi
- Maksimal 15 baris per request, hard-cap di server: `min(15, max(5, $perPage))`
- Fase 1: `paginate(15)->withQueryString()`
- Fase 2: infinite scroll via `cursorPaginate(15)` endpoint JSON
- Export CSV/PDF via queue job, chunk(500), tidak dibatasi 15
- Chart/statistik agregat: groupBy/withCount, bukan list

## Index per Tabel

### bookings
- idx_bookings_status_created (status, created_at)
- idx_bookings_slot_status (slot_id, status)
- idx_bookings_user_created (user_id, created_at)
- idx_bookings_code (booking_code) UNIQUE
- idx_bookings_paid_at (paid_at)

### booking_items
- idx_items_booking (booking_id)
- idx_items_name (name)

### slots
- idx_slots_package_date (package_id, date) UNIQUE
- idx_slots_date_capacity (date, capacity)

### checkins
- idx_checkins_booking (booking_id)
- idx_checkins_checked_at (checked_at)

### activity_logs
- idx_logs_user_created (user_id, created_at)
- idx_logs_action_created (action, created_at)

### payments
- idx_payments_booking (booking_id)
- idx_payments_status_created (status, created_at)

## Verifikasi
| Uji | Target |
|-----|--------|
| EXPLAIN daftar booking | type ref/range, bukan ALL |
| Muat 15 baris dari 10.000 | P95 <200ms |
| Infinite scroll batch 15 | 1 query list + eager load |
| Overselling slot terakhir | 1 sukses dari 10 paralel |
| Query log dashboard | 0 lazy load |
