#!/usr/bin/env python3
# setup.py — jalankan di root project Laravel: python setup.py
import os

FILES = {}

FILES["README.md"] = """# Tumpak Sewu Reservation System

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
"""

FILES["docs/PRD.md"] = """# PRD v1.0 — Sistem Reservasi Tumpak Sewu

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
"""

FILES["docs/PRD-addendum-indexing.md"] = """# Addendum v1.1 — Indexing & Paginasi 15

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
"""

FILES["database/migrations/2026_01_01_000001_create_packages_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->decimal('price', 12, 2);
            $t->unsignedSmallInteger('capacity_per_slot')->default(10);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['is_active', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};
"""

FILES["database/migrations/2026_01_01_000002_create_slots_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('slots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->unsignedSmallInteger('capacity');
            $t->unsignedSmallInteger('used')->default(0);
            $t->timestamps();
            $t->unique(['package_id', 'date'], 'idx_slots_package_date');
            $t->index(['date', 'capacity'], 'idx_slots_date_capacity');
        });
    }
    public function down(): void { Schema::dropIfExists('slots'); }
};
"""

FILES["database/migrations/2026_01_01_000003_create_bookings_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->string('booking_code', 32)->unique('idx_bookings_code');
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('slot_id')->constrained()->restrictOnDelete();
            $t->enum('status', ['pending','paid','confirmed','checked_in','cancelled'])->default('pending');
            $t->decimal('total_amount', 12, 2)->default(0);
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at'], 'idx_bookings_status_created');
            $t->index(['slot_id', 'status'], 'idx_bookings_slot_status');
            $t->index(['user_id', 'created_at'], 'idx_bookings_user_created');
            $t->index('paid_at', 'idx_bookings_paid_at');
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
"""

FILES["database/migrations/2026_01_01_000004_create_booking_items_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('id_number', 32)->nullable();
            $t->string('phone', 20)->nullable();
            $t->timestamps();
            $t->index('booking_id', 'idx_items_booking');
            $t->index('name', 'idx_items_name');
        });
    }
    public function down(): void { Schema::dropIfExists('booking_items'); }
};
"""

FILES["database/migrations/2026_01_01_000005_create_checkins_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('checkins', function (Blueprint $t) {
            $t->id();
            $t->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $t->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('checked_at');
            $t->timestamps();
            $t->index('booking_id', 'idx_checkins_booking');
            $t->index('checked_at', 'idx_checkins_checked_at');
        });
    }
    public function down(): void { Schema::dropIfExists('checkins'); }
};
"""

FILES["database/migrations/2026_01_01_000006_create_activity_logs_table.php"] = """<?php
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 64);
            $t->string('subject_type')->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'created_at'], 'idx_logs_user_created');
            $t->index(['action', 'created_at'], 'idx_logs_action_created');
        });
    }
    public function down(): void { Schema::dropIfExists('activity_logs'); }
};
"""

FILES["app/Http/Controllers/Admin/BookingController.php"] = """<?php
namespace App\\Http\\Controllers\\Admin;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Booking;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Cache;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(15, max(5, (int) $request->input('per_page', 15)));
        $status  = $request->input('status');

        $cacheKey = 'bookings:list:' . md5(json_encode([
            'status' => $status,
            'page'   => $request->input('page', 1),
            'per'    => $perPage,
        ]));

        $bookings = Cache::remember($cacheKey, 60, function () use ($status, $perPage) {
            return Booking::query()
                ->select(['id','booking_code','slot_id','user_id','status','total_amount','paid_at','created_at'])
                ->with(['slot:id,package_id,date,capacity,used','slot.package:id,name,price'])
                ->withCount('items')
                ->when($status, fn($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();
        });

        return view('admin.bookings.index', compact('bookings', 'status', 'perPage'));
    }
}
"""

FILES["app/Http/Resources/BookingResource.php"] = """<?php
namespace App\\Http\\Resources;

use Illuminate\\Http\\Resources\\Json\\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->booking_code,
            'status'      => $this->status,
            'package'     => $this->slot?->package?->name,
            'date'        => $this->slot?->date,
            'items_count' => $this->items_count ?? $this->items()->count(),
            'total'       => $this->total_amount,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
"""

FILES["resources/views/admin/bookings/index.blade.php"] = """@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-semibold mb-4">Daftar Booking</h1>

<form method="GET" class="mb-4 flex gap-2">
    <select name="status" class="border p-2 rounded">
        <option value="">Semua Status</option>
        @foreach(['pending','paid','confirmed','checked_in','cancelled'] as $s)
            <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
        @endforeach
    </select>
    <select name="per_page" class="border p-2 rounded">
        @foreach([5,10,15] as $n)
            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }} / halaman</option>
        @endforeach
    </select>
    <button class="bg-blue-600 text-white px-4 rounded">Filter</button>
</form>

<table class="w-full text-sm border">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Kode</th>
            <th class="p-2 text-left">Paket</th>
            <th class="p-2 text-left">Tanggal</th>
            <th class="p-2 text-right">Peserta</th>
            <th class="p-2 text-right">Total</th>
            <th class="p-2 text-left">Status</th>
        </tr>
    </thead>
    <tbody>
    @forelse($bookings as $b)
        <tr class="border-t">
            <td class="p-2">{{ $b->booking_code }}</td>
            <td class="p-2">{{ $b->slot?->package?->name }}</td>
            <td class="p-2">{{ $b->slot?->date }}</td>
            <td class="p-2 text-right">{{ $b->items_count }}</td>
            <td class="p-2 text-right">{{ number_format($b->total_amount, 0, ',', '.') }}</td>
            <td class="p-2">{{ $b->status }}</td>
        </tr>
    @empty
        <tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada data</td></tr>
    @endforelse
    </tbody>
</table>

<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
"""

def main():
    for path, content in FILES.items():
        os.makedirs(os.path.dirname(path) or ".", exist_ok=True)
        with open(path, "w", encoding="utf-8", newline="\n") as f:
            f.write(content)
        print(f"  created: {path}")
    print(f"\nSelesai. {len(FILES)} file dibuat.")

if __name__ == "__main__":
    main()