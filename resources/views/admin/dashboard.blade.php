@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Booking Hari Ini -->
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-5 shadow-sm space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Booking Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-white font-mono">{{ $stats['bookings_today'] }}</div>
            <div class="text-[11px] text-emerald-400 flex items-center gap-1">
                <span>↑ Transaksi real-time</span>
            </div>
        </div>

        <!-- Card 2: Pendapatan Bulan Ini -->
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-5 shadow-sm space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Bulan Ini</span>
                <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-white font-mono">
                Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400">Status Paid & Confirmed</div>
        </div>

        <!-- Card 3: Check-in Hari Ini -->
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-5 shadow-sm space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Check-in Pos Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-white font-mono">{{ $stats['checkins_today'] }}</div>
            <div class="text-[11px] text-blue-400">Verifikasi QR Code</div>
        </div>

        <!-- Card 4: Menunggu Bayar -->
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-5 shadow-sm space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Bayar</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-amber-400 font-mono">{{ $stats['pending'] }}</div>
            <div class="text-[11px] text-slate-400">Slot dalam masa hold 10 mnt</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Daily Trend (2 cols) -->
        <div class="lg:col-span-2 rounded-3xl bg-slate-950 border border-slate-800 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white tracking-wide">Tren Booking 14 Hari Terakhir</h2>
                    <p class="text-xs text-slate-500">Agregasi volume transaksi per hari (Cache TTL 60s)</p>
                </div>
                <span class="text-[10px] font-mono px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400">
                    Auto-Cached
                </span>
            </div>
            <div class="h-64">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- Status Doughnut (1 col) -->
        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-sm font-bold text-white tracking-wide">Komposisi Status</h2>
                <p class="text-xs text-slate-500">Distribusi seluruh status booking</p>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Upcoming Slots Occupancy Table (Max 15 rows clamped) -->
    <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white tracking-wide">Slot & Okupansi Terdekat</h2>
                <p class="text-xs text-slate-500">Maksimal 15 baris (Optimasi index `package_id, date`)</p>
            </div>
            <a href="{{ url('/admin/bookings') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                Lihat Semua Booking &rarr;
            </a>
        </div>

        <div class="rounded-2xl border border-slate-800/80 overflow-x-auto text-xs">
            <table class="w-full">
                <thead class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold">
                    <tr>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Paket Layanan</th>
                        <th class="p-3 text-right">Terisi</th>
                        <th class="p-3 text-right">Kapasitas</th>
                        <th class="p-3 w-48 text-left">Tingkat Okupansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($upcoming as $s)
                        @php
                            $pct = $s->capacity ? round(($s->used / $s->capacity) * 100) : 0;
                            $barColor = $pct >= 90 ? 'bg-rose-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <tr class="hover:bg-slate-900/50 transition-colors">
                            <td class="p-3 text-white font-sans font-medium">{{ \Carbon\Carbon::parse($s->date)->translatedFormat('d M Y') }}</td>
                            <td class="p-3 text-slate-300 font-sans font-medium">{{ $s->package?->name ?? '-' }}</td>
                            <td class="p-3 text-right text-emerald-400 font-bold">{{ $s->used }}</td>
                            <td class="p-3 text-right text-slate-400">{{ $s->capacity }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-slate-400 w-9 text-right">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500">Belum ada data slot terdekat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 14 Days Trend Chart
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Booking',
                    data: @json($dailyData),
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(51, 65, 85, 0.4)' },
                        ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10 } }
                    }
                }
            }
        });
    }

    // Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($statusCounts)),
                datasets: [{
                    data: @json(array_values($statusCounts)),
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#14b8a6', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    }
                },
                cutout: '70%',
            }
        });
    }
});
</script>
@endpush
