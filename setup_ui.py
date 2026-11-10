#!/usr/bin/env python3
# setup_ui.py — jalankan di root project Laravel setelah setup.py: python setup_ui.py
# UI: Blade + Tailwind (CDN) + Alpine.js (CDN) + Chart.js (CDN). Tidak perlu build npm.
import os

FILES = {}

# ---------------------------------------------------------------- LAYOUT PUBLIK
FILES["resources/views/layouts/public.blade.php"] = r"""<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tumpak Sewu Reservation')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<header class="bg-white border-b sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ url('/') }}" class="font-bold text-emerald-700">Tumpak Sewu <span class="text-slate-400 font-normal">Reservasi</span></a>
        <nav class="flex gap-5 text-sm">
            <a href="{{ url('/packages') }}" class="hover:text-emerald-700">Paket</a>
            <a href="{{ url('/status') }}" class="hover:text-emerald-700">Cek Booking</a>
        </nav>
    </div>
</header>

@if(session('success'))
    <div class="max-w-6xl mx-auto px-4 mt-4"><div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded p-3">{{ session('success') }}</div></div>
@endif
@if($errors->any())
    <div class="max-w-6xl mx-auto px-4 mt-4"><div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded p-3">
        <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div></div>
@endif

<main class="max-w-6xl mx-auto px-4 py-8">@yield('content')</main>

<footer class="border-t bg-white mt-12">
    <div class="max-w-6xl mx-auto px-4 py-6 text-xs text-slate-500 space-y-1">
        <p><strong>Disclaimer:</strong> Situs ini adalah layanan pendamping (guide, transport, paket) dan <strong>bukan</strong> sistem tiket masuk resmi.
        Tiket masuk resmi dikelola pemerintah melalui <a class="underline" href="https://cobansewu.com" target="_blank" rel="noopener">cobansewu.com</a>.</p>
        <p>&copy; {{ date('Y') }} Tumpak Sewu Reservation System</p>
    </div>
</footer>
</body>
</html>
"""

# ---------------------------------------------------------------- LAYOUT ADMIN
FILES["resources/views/layouts/admin.blade.php"] = r"""<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Tumpak Sewu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800" x-data="{ open:false }">
<div class="min-h-screen md:flex">
    <aside class="bg-slate-900 text-slate-200 md:w-56 md:min-h-screen md:block" :class="open ? 'block' : 'hidden md:block'">
        <div class="px-4 h-14 flex items-center font-semibold text-white border-b border-slate-800">Tumpak Sewu Admin</div>
        <nav class="p-2 text-sm space-y-1">
            @php $p = request()->path(); @endphp
            @foreach([
                ['/admin','Dashboard','admin'],
                ['/admin/bookings','Booking','admin/bookings*'],
                ['/admin/checkin','Scan Check-in','admin/checkin*'],
                ['/admin/packages','Paket','admin/packages*'],
                ['/admin/guides','Guide','admin/guides*'],
                ['/admin/slots','Slot & Harga','admin/slots*'],
                ['/admin/logs','Activity Log','admin/logs*'],
            ] as [$href,$label,$pat])
                <a href="{{ url($href) }}" class="block px-3 py-2 rounded {{ request()->is($pat) ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </aside>

    <div class="flex-1 min-w-0">
        <header class="bg-white border-b h-14 px-4 flex items-center justify-between">
            <button class="md:hidden text-slate-600" @click="open=!open">&#9776;</button>
            <span class="text-sm text-slate-500 hidden md:block">@yield('title', 'Admin')</span>
            <form method="POST" action="{{ url('/logout') }}" class="text-sm">
                @csrf
                <span class="mr-3 text-slate-500">{{ auth()->user()->name ?? '' }}</span>
                <button class="text-red-600 hover:underline">Keluar</button>
            </form>
        </header>

        <main class="p-4 md:p-6">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded p-3">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
"""

# ---------------------------------------------------------------- LANDING (FR-P1)
FILES["resources/views/public/home.blade.php"] = r"""@extends('layouts.public')
@section('title', 'Tumpak Sewu - Reservasi Guide & Paket')

@section('content')
<section class="rounded-2xl bg-gradient-to-br from-emerald-700 to-teal-600 text-white p-8 md:p-14">
    <h1 class="text-3xl md:text-5xl font-bold leading-tight">Jelajahi Air Terjun Tumpak Sewu<br>bersama guide lokal</h1>
    <p class="mt-4 max-w-xl text-emerald-50">Reservasi guide, transport, dan paket rombongan. Slot real-time, bayar online, e-voucher langsung terbit.</p>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ url('/packages') }}" class="bg-white text-emerald-800 font-medium px-5 py-2.5 rounded-lg">Lihat Paket</a>
        <a href="{{ url('/status') }}" class="border border-white/60 px-5 py-2.5 rounded-lg">Cek Status Booking</a>
    </div>
</section>

<section class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900">
    <strong>Penting:</strong> Ini bukan sistem tiket masuk resmi. Tiket masuk wajib dibeli terpisah di
    <a class="underline" href="https://cobansewu.com" target="_blank" rel="noopener">cobansewu.com</a> (sejak 27 Februari 2025).
</section>

<section class="mt-10 grid md:grid-cols-3 gap-4">
    @foreach([
        ['1. Pilih paket','Guide, transport, atau paket rombongan sesuai kebutuhan.'],
        ['2. Pilih tanggal & slot','Kalender menampilkan sisa kuota secara real-time.'],
        ['3. Bayar & dapat e-voucher','Slot ditahan 10 menit. Setelah bayar, e-voucher + QR dikirim.'],
    ] as [$t,$d])
        <div class="bg-white rounded-xl border p-5"><h3 class="font-semibold">{{ $t }}</h3><p class="text-sm text-slate-600 mt-1">{{ $d }}</p></div>
    @endforeach
</section>

<section class="mt-10">
    <h2 class="text-xl font-semibold mb-3">Kebijakan Reschedule & Pembatalan</h2>
    <div class="bg-white rounded-xl border p-5 text-sm text-slate-600 space-y-1">
        <p>Reschedule gratis maksimal 1x, paling lambat H-3 sebelum kunjungan.</p>
        <p>Pembatalan H-3 atau lebih: refund penuh. Di bawah H-3: sesuai ketentuan paket.</p>
        <p class="text-xs text-slate-400">Sesuaikan teks ini dengan kebijakan operasional Anda.</p>
    </div>
</section>
@endsection
"""

# ---------------------------------------------------------------- KATALOG (FR-P2)
FILES["resources/views/public/packages.blade.php"] = r"""@extends('layouts.public')
@section('title', 'Katalog Paket')

{{-- Variabel: $packages (paginator, 15 maks), $date (string|null) --}}
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Katalog Paket</h1>
    <form method="GET" class="flex gap-2 items-end">
        <label class="text-sm">Tanggal kunjungan
            <input type="date" name="date" value="{{ $date ?? '' }}" min="{{ now()->toDateString() }}" class="block border rounded p-2 mt-1">
        </label>
        <button class="bg-emerald-600 text-white px-4 py-2 rounded">Cari</button>
    </form>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
@forelse($packages as $pkg)
    <article class="bg-white border rounded-xl overflow-hidden flex flex-col">
        <div class="h-32 bg-gradient-to-br from-emerald-100 to-teal-200"></div>
        <div class="p-4 flex-1 flex flex-col">
            <h2 class="font-semibold">{{ $pkg->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Kapasitas {{ $pkg->capacity_per_slot }} orang / slot</p>
            @isset($pkg->remaining)
                <p class="text-xs mt-2 {{ $pkg->remaining > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                    {{ $pkg->remaining > 0 ? 'Sisa '.$pkg->remaining.' kuota pada '.$date : 'Penuh pada '.$date }}
                </p>
            @endisset
            <div class="mt-auto pt-4 flex items-center justify-between">
                <span class="font-bold text-emerald-700">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                <a href="{{ url('/packages/'.$pkg->slug.'/book'.($date ? '?date='.$date : '')) }}" class="text-sm bg-emerald-600 text-white px-3 py-1.5 rounded">Pesan</a>
            </div>
        </div>
    </article>
@empty
    <p class="col-span-full text-center text-slate-500 py-12">Belum ada paket tersedia.</p>
@endforelse
</div>

<div class="mt-6">{{ $packages->links() }}</div>
@endsection
"""

# ---------------------------------------------------------------- BOOKING (FR-P3, FR-P4)
FILES["resources/views/public/booking.blade.php"] = r"""@extends('layouts.public')
@section('title', 'Pesan '.$package->name)

{{-- Variabel: $package, $slots (koleksi: id, date, capacity, used), $selectedDate (opsional) --}}
@section('content')
@php
    $slotData = $slots->map(fn($s) => [
        'id' => $s->id,
        'date' => \Illuminate\Support\Carbon::parse($s->date)->toDateString(),
        'remaining' => max(0, $s->capacity - $s->used),
    ])->values();
@endphp

<div x-data="booking(@js($slotData), {{ (float) $package->price }}, @js($selectedDate ?? null))" class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <section class="bg-white border rounded-xl p-5">
            <h1 class="text-xl font-semibold">{{ $package->name }}</h1>
            <p class="text-sm text-slate-500">Pilih tanggal. Angka menunjukkan sisa kuota.</p>

            <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2 mt-4">
                <template x-for="s in slots" :key="s.id">
                    <button type="button" @click="pick(s)" :disabled="s.remaining === 0"
                        class="border rounded-lg p-2 text-center text-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="chosen && chosen.id === s.id ? 'bg-emerald-600 text-white border-emerald-600' : 'hover:border-emerald-500'">
                        <div class="font-medium" x-text="fmt(s.date)"></div>
                        <div class="text-xs" x-text="s.remaining === 0 ? 'Penuh' : 'Sisa ' + s.remaining"></div>
                    </button>
                </template>
            </div>
            <p x-show="slots.length === 0" x-cloak class="text-sm text-slate-500 mt-4">Belum ada slot dibuka.</p>
        </section>

        <form id="bookingForm" method="POST" action="{{ url('/booking') }}" class="bg-white border rounded-xl p-5 space-y-4">
            @csrf
            <input type="hidden" name="slot_id" :value="chosen ? chosen.id : ''">
            <h2 class="font-semibold">Data Peserta</h2>

            <template x-for="(p, i) in people" :key="i">
                <div class="grid md:grid-cols-7 gap-2 items-end border rounded-lg p-3">
                    <label class="md:col-span-3 text-sm">Nama
                        <input type="text" :name="`items[${i}][name]`" x-model="p.name" required class="w-full border rounded p-2 mt-1">
                    </label>
                    <label class="md:col-span-2 text-sm">No. Identitas
                        <input type="text" :name="`items[${i}][id_number]`" x-model="p.id_number" class="w-full border rounded p-2 mt-1">
                    </label>
                    <label class="md:col-span-1 text-sm">Telepon
                        <input type="text" :name="`items[${i}][phone]`" x-model="p.phone" class="w-full border rounded p-2 mt-1">
                    </label>
                    <button type="button" @click="people.splice(i,1)" x-show="people.length > 1" class="text-red-600 text-sm">Hapus</button>
                </div>
            </template>

            <button type="button" @click="add()" :disabled="chosen && people.length >= chosen.remaining"
                class="text-sm text-emerald-700 disabled:opacity-40">+ Tambah peserta</button>

            <p class="text-xs text-slate-500">Data identitas dienkripsi di server dan hanya dipakai untuk manifest kunjungan.</p>
        </form>
    </div>

    <aside class="lg:sticky lg:top-20 h-fit bg-white border rounded-xl p-5 space-y-3">
        <h2 class="font-semibold">Ringkasan</h2>
        <dl class="text-sm space-y-1">
            <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd x-text="chosen ? fmt(chosen.date) : '-'"></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Peserta</dt><dd x-text="people.length"></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Harga / orang</dt><dd x-text="rp(price)"></dd></div>
        </dl>
        <div class="border-t pt-3 flex justify-between font-bold"><span>Total</span><span x-text="rp(price * people.length)"></span></div>
        <button form="bookingForm" :disabled="!chosen" class="w-full bg-emerald-600 text-white py-2.5 rounded-lg disabled:opacity-40">Tahan Slot & Bayar</button>
        <p class="text-xs text-slate-500">Slot ditahan 10 menit setelah Anda menekan tombol.</p>
    </aside>
</div>

<script>
function booking(slots, price, preselect) {
    return {
        slots, price, chosen: null,
        people: [{ name: '', id_number: '', phone: '' }],
        init() { if (preselect) this.chosen = this.slots.find(s => s.date === preselect && s.remaining > 0) || null; },
        pick(s) { this.chosen = s; this.people = this.people.slice(0, s.remaining); },
        add() { this.people.push({ name: '', id_number: '', phone: '' }); },
        fmt(d) { return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }); },
        rp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
    };
}
</script>
@endsection
"""

# ---------------------------------------------------------------- CEK STATUS (FR-P7)
FILES["resources/views/public/status.blade.php"] = r"""@extends('layouts.public')
@section('title', 'Cek Status Booking')

{{-- Variabel: $code (string|null), $booking (Booking|null, dengan slot.package & items) --}}
@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Cek Status Booking</h1>
    <form method="GET" class="flex gap-2">
        <input name="code" value="{{ $code ?? '' }}" placeholder="Kode booking" class="flex-1 border rounded p-2" required>
        <button class="bg-emerald-600 text-white px-4 rounded">Cek</button>
    </form>

    @if(!empty($code) && empty($booking))
        <p class="mt-6 text-sm text-red-600">Booking dengan kode tersebut tidak ditemukan.</p>
    @endif

    @if(!empty($booking))
        @php
            $badge = [
                'pending' => 'bg-amber-100 text-amber-800',
                'paid' => 'bg-blue-100 text-blue-800',
                'confirmed' => 'bg-emerald-100 text-emerald-800',
                'checked_in' => 'bg-teal-100 text-teal-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ][$booking->status] ?? 'bg-slate-100';
        @endphp
        <div class="mt-6 bg-white border rounded-xl p-5 space-y-3">
            <div class="flex justify-between items-center">
                <span class="font-mono font-semibold">{{ $booking->booking_code }}</span>
                <span class="text-xs px-2 py-1 rounded-full {{ $badge }}">{{ $booking->status }}</span>
            </div>
            <dl class="text-sm space-y-1">
                <div class="flex justify-between"><dt class="text-slate-500">Paket</dt><dd>{{ $booking->slot?->package?->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd>{{ $booking->slot?->date }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Peserta</dt><dd>{{ $booking->items->count() }}</dd></div>
                <div class="flex justify-between font-semibold"><dt>Total</dt><dd>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</dd></div>
            </dl>
            @if(in_array($booking->status, ['confirmed','checked_in']))
                <a href="{{ url('/voucher/'.$booking->booking_code) }}" class="block text-center bg-emerald-600 text-white py-2 rounded-lg">Unduh E-Voucher (PDF)</a>
            @endif
        </div>
    @endif
</div>
@endsection
"""

# ---------------------------------------------------------------- DASHBOARD (FR-A5)
FILES["resources/views/admin/dashboard.blade.php"] = r"""@extends('layouts.admin')
@section('title', 'Dashboard')

{{-- Variabel (semua hasil agregat, di-cache 60 dtk):
     $stats        = ['bookings_today'=>int,'revenue_month'=>num,'checkins_today'=>int,'pending'=>int]
     $dailyLabels  = ['01 Nov', ...]   $dailyData = [..]   (booking per hari, 14 hari)
     $statusCounts = ['pending'=>n,'paid'=>n,'confirmed'=>n,'checked_in'=>n,'cancelled'=>n]
     $upcoming     = slot terdekat (id, date, capacity, used, package.name) maks 15 --}}
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
        ['Booking hari ini', $stats['bookings_today']],
        ['Pendapatan bulan ini', 'Rp '.number_format($stats['revenue_month'], 0, ',', '.')],
        ['Check-in hari ini', $stats['checkins_today']],
        ['Menunggu bayar', $stats['pending']],
    ] as [$label, $val])
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-slate-500">{{ $label }}</p>
            <p class="text-2xl font-semibold mt-1">{{ $val }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-4 mt-4">
    <div class="lg:col-span-2 bg-white rounded-xl border p-4">
        <h2 class="font-semibold mb-2 text-sm">Booking 14 hari terakhir</h2>
        <div class="h-64"><canvas id="dailyChart"></canvas></div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <h2 class="font-semibold mb-2 text-sm">Komposisi status</h2>
        <div class="h-64"><canvas id="statusChart"></canvas></div>
    </div>
</div>

<div class="bg-white rounded-xl border mt-4 overflow-x-auto">
    <h2 class="font-semibold text-sm p-4 pb-2">Slot terdekat</h2>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500"><tr>
            <th class="p-2 text-left">Tanggal</th><th class="p-2 text-left">Paket</th>
            <th class="p-2 text-right">Terisi</th><th class="p-2 w-40">Okupansi</th>
        </tr></thead>
        <tbody>
        @forelse($upcoming as $s)
            @php $pct = $s->capacity ? round($s->used / $s->capacity * 100) : 0; @endphp
            <tr class="border-t">
                <td class="p-2">{{ $s->date }}</td>
                <td class="p-2">{{ $s->package?->name }}</td>
                <td class="p-2 text-right">{{ $s->used }} / {{ $s->capacity }}</td>
                <td class="p-2"><div class="h-2 bg-slate-200 rounded"><div class="h-2 rounded {{ $pct >= 90 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div></div></td>
            </tr>
        @empty
            <tr><td colspan="4" class="p-4 text-center text-slate-500">Belum ada slot.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: { labels: @json($dailyLabels), datasets: [{ label: 'Booking', data: @json($dailyData), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.12)', fill: true, tension: .3 }] },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: @json(array_keys($statusCounts)), datasets: [{ data: @json(array_values($statusCounts)), backgroundColor: ['#f59e0b','#3b82f6','#10b981','#14b8a6','#ef4444'] }] },
    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
"""

# ---------------------------------------------------------------- SCANNER QR (FR-A4)
FILES["resources/views/admin/checkin.blade.php"] = r"""@extends('layouts.admin')
@section('title', 'Scan Check-in')

{{-- POST /admin/checkin  body: {token}  respons JSON: {ok:bool, message:string, booking?:{code,package,people}} --}}
@section('content')
<div class="max-w-xl mx-auto space-y-4" x-data="scanner()" x-init="start()">
    <div class="bg-white border rounded-xl p-4">
        <div id="reader" class="w-full overflow-hidden rounded-lg"></div>
        <p class="text-xs text-slate-500 mt-2">Arahkan kamera ke QR pada e-voucher.</p>
    </div>

    <form @submit.prevent="send(manual)" class="flex gap-2">
        <input x-model="manual" placeholder="Atau masukkan kode/token manual" class="flex-1 border rounded p-2 text-sm">
        <button class="bg-slate-800 text-white px-4 rounded text-sm">Cek</button>
    </form>

    <div x-show="result" x-cloak class="rounded-xl border p-4"
         :class="result && result.ok ? 'bg-emerald-50 border-emerald-300' : 'bg-red-50 border-red-300'">
        <p class="font-semibold" x-text="result && result.ok ? 'Check-in berhasil' : 'Gagal'"></p>
        <p class="text-sm" x-text="result && result.message"></p>
        <template x-if="result && result.booking">
            <p class="text-sm mt-1"><span x-text="result.booking.code" class="font-mono"></span> &middot;
            <span x-text="result.booking.package"></span> &middot; <span x-text="result.booking.people"></span> orang</p>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function scanner() {
    return {
        manual: '', result: null, busy: false, qr: null,
        start() {
            this.qr = new Html5Qrcode('reader');
            this.qr.start({ facingMode: 'environment' }, { fps: 10, qrbox: 240 }, t => this.send(t), () => {})
                .catch(() => { this.result = { ok: false, message: 'Kamera tidak bisa diakses. Gunakan input manual.' }; });
        },
        async send(token) {
            if (!token || this.busy) return;
            this.busy = true;
            try {
                const r = await fetch('{{ url('/admin/checkin') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                               'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ token })
                });
                this.result = await r.json();
            } catch (e) {
                this.result = { ok: false, message: 'Koneksi bermasalah.' };
            }
            setTimeout(() => this.busy = false, 2500); // cegah scan ganda beruntun
        }
    };
}
</script>
@endpush
"""

# ---------------------------------------------------------------- PETA ROUTE
FILES["docs/UI-routes.md"] = r"""# UI — Peta Route & Data

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
