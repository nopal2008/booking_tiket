@extends('layouts.admin')
@section('title', 'Manajemen Booking')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Toolbar -->
    <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide">Daftar Transaksi Booking</h1>
                <p class="text-xs text-slate-500">Maksimal 15 baris per halaman (Hard-clamped server side untuk performa P95 &lt; 200ms)</p>
            </div>
            
            <div class="text-xs text-slate-400 font-mono bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-800">
                Total: <span class="text-emerald-400 font-bold">{{ $bookings->total() }}</span> entri
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ url('/admin/bookings') }}" class="flex flex-wrap items-center gap-3 pt-2">
            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-400">Status:</label>
                <select name="status" class="bg-slate-900 border border-slate-800 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    @foreach(['pending' => 'Pending (Hold)', 'paid' => 'Paid (Terbayar)', 'confirmed' => 'Confirmed', 'checked_in' => 'Checked In', 'cancelled' => 'Cancelled'] as $val => $lbl)
                        <option value="{{ $val }}" @selected($status === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-400">Per Halaman:</label>
                <select name="per_page" class="bg-slate-900 border border-slate-800 text-white rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                    @foreach([5, 10, 15] as $n)
                        <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }} baris</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-colors">
                Terapkan Filter
            </button>

            @if(!empty($status) || $perPage != 15)
                <a href="{{ url('/admin/bookings') }}" class="px-3 py-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="rounded-3xl bg-slate-950 border border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto text-xs">
            <table class="w-full">
                <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-4 text-left">Kode Booking</th>
                        <th class="p-4 text-left">Paket Layanan</th>
                        <th class="p-4 text-left">Tanggal Kunjungan</th>
                        <th class="p-4 text-center">Peserta</th>
                        <th class="p-4 text-right">Total Tagihan</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($bookings as $b)
                        @php
                            $badge = [
                                'confirmed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'checked_in' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                'paid' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                            ][$b->status] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                        @endphp
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="p-4 font-mono font-bold text-white">{{ $b->booking_code }}</td>
                            <td class="p-4 text-slate-200 font-medium">{{ $b->slot?->package?->name ?? '-' }}</td>
                            <td class="p-4 text-slate-400">{{ \Carbon\Carbon::parse($b->slot?->date)->translatedFormat('d M Y') }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-300 font-mono font-bold">
                                    {{ $b->items_count }} org
                                </span>
                            </td>
                            <td class="p-4 text-right font-mono font-bold text-emerald-400">
                                Rp {{ number_format($b->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-block px-3 py-1 rounded-full border text-[11px] font-bold font-mono {{ $badge }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ url('/status?code=' . $b->booking_code) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-emerald-400 font-semibold text-xs border border-slate-800 transition-colors">
                                    <span>E-Voucher</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                Tidak ada data reservasi yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 bg-slate-900 border-t border-slate-800 flex justify-between items-center text-xs text-slate-400">
            <div>
                Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} entri
            </div>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
