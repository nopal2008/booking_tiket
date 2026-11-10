# UI — Peta Route & Data

Semua view memakai Tailwind/Alpine/Chart.js via CDN (tanpa build npm). Untuk produksi, pindahkan ke Vite.

## Publik
| URL | View | Variabel |
|-----|------|----------|
| GET / | public.home | - |
| GET /packages?date= | public.packages | `$packages` (paginate 15), `$date` |
| GET /packages/{slug}/book | public.booking | `$package`, `$slots`, `$selectedDate` |
| POST /booking | (controller) | `slot_id`, `items[][name,id_number,phone]` |
| GET /status?code= | public.status | `$code`, `$booking` |
| GET /voucher/{code} | PDF | - |

## Admin (middleware auth + role)
| URL | View | Variabel |
|-----|------|----------|
| GET /admin | admin.dashboard | `$stats`, `$dailyLabels`, `$dailyData`, `$statusCounts`, `$upcoming` |
| GET /admin/bookings | admin.bookings.index | sudah ada di setup.py |
| GET /admin/checkin | admin.checkin | - |
| POST /admin/checkin | JSON | `{token}` -> `{ok, message, booking}` |

## Catatan performa
- Dashboard: gunakan `groupBy`/`withCount` + `Cache::remember(..., 60)`, bukan daftar.
- `$slots` di halaman booking: batasi rentang tanggal (mis. 60 hari ke depan) dan `select` kolom seperlunya.
- `$upcoming` maksimal 15 baris dan eager load `package:id,name`.
