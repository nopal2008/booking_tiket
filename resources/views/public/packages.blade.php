@extends('layouts.public')
@section('title', 'Katalog Paket Layanan Pendamping — Tumpak Sewu')

@section('content')
<div class="space-y-8">

    <!-- Header Section with Date Selector -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Pemandu & Fasilitas Resmi</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Paket Pendamping</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-xl">
                Pilih paket pendamping sesuai kebutuhan Anda. Seluruh pemandu kami telah tersertifikasi standar keselamatan medan basah.
            </p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 text-sm w-full sm:w-auto">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs text-slate-500">Tanggal:</span>
                    <input type="date" name="date" value="{{ $date ?? '' }}" min="{{ now()->toDateString() }}" 
                        class="bg-transparent font-medium text-slate-800 focus:outline-none cursor-pointer text-xs sm:text-sm">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors shadow-sm w-full sm:w-auto">
                    Cek Kuota
                </button>
                @if(!empty($date))
                    <a href="{{ url('/packages') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Active Filter Status Notification -->
    @if(!empty($date))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 flex items-center justify-between text-xs text-emerald-800">
            <div class="flex items-center gap-2 font-medium">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Menampilkan ketersediaan kuota slot untuk tanggal <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</strong></span>
            </div>
            <span class="text-[11px] text-emerald-700 font-mono">Data terverifikasi real-time</span>
        </div>
    @endif

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $pkg)
            @php
                $isFull = isset($pkg->remaining) && $pkg->remaining === 0;
                $isLow = isset($pkg->remaining) && $pkg->remaining > 0 && $pkg->remaining <= 5;
            @endphp
            <article class="group rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col justify-between">
                <div>
                    <!-- Visual Card Banner -->
                    <div class="relative h-44 bg-gradient-to-br from-emerald-800 via-teal-900 to-slate-950 p-5 flex flex-col justify-between overflow-hidden">
                        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-2xl"></div>

                        <!-- Top Badges -->
                        <div class="relative z-10 flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 text-white backdrop-blur-md border border-white/20">
                                <span>👥 Max {{ $pkg->capacity_per_slot }} Orang</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-400 text-slate-950 shadow-sm">
                                <span>★ 4.9</span>
                            </span>
                        </div>

                        <!-- Title on Banner -->
                        <div class="relative z-10">
                            <span class="text-[10px] font-bold tracking-widest text-emerald-300 uppercase">Layanan Unggulan</span>
                            <h2 class="text-xl font-extrabold text-white leading-snug line-clamp-2">{{ $pkg->name }}</h2>
                        </div>
                    </div>

                    <!-- Card Body Details -->
                    <div class="p-6 space-y-4">
                        <!-- Quota Indicator (If Date Chosen) -->
                        @if(isset($pkg->remaining))
                            <div class="p-3 rounded-xl border {{ $isFull ? 'bg-rose-50 border-rose-200 text-rose-800' : ($isLow ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800') }} text-xs">
                                <div class="flex justify-between items-center font-bold">
                                    <span>Status Kuota</span>
                                    <span>{{ $isFull ? 'PENUH' : 'Sisa ' . $pkg->remaining . ' slot' }}</span>
                                </div>
                                <div class="w-full bg-slate-200 h-1.5 rounded-full mt-2 overflow-hidden">
                                    @php
                                        $percent = round(($pkg->remaining / $pkg->capacity_per_slot) * 100);
                                    @endphp
                                    <div class="h-full rounded-full {{ $isFull ? 'bg-rose-500' : ($isLow ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-slate-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Durasi jelajah: ± 3 - 4 Jam</span>
                            </div>
                        @endif

                        <!-- Inclusions Checklist -->
                        <div class="space-y-2 pt-2 border-t border-slate-100 text-xs text-slate-600 font-medium">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Pemandu lokal tersertifikasi keselamatan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Helm pengaman & sepatu trekking air</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Bantuan foto & spot terbaik Goa Tetes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Price & Action -->
                <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mulai dari</span>
                        <div class="text-xl font-extrabold text-slate-900">
                            Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            <span class="text-xs font-normal text-slate-400">/ grup</span>
                        </div>
                    </div>

                    @if($isFull)
                        <button disabled class="px-5 py-2.5 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed">
                            Slot Penuh
                        </button>
                    @else
                        <a href="{{ url('/packages/'.$pkg->slug.'/book'.($date ? '?date='.$date : '')) }}" 
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-all">
                            <span>Pilih Slot</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full py-16 text-center space-y-3 bg-white rounded-3xl border border-dashed border-slate-300">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                    🔍
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tidak ada paket ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Silakan ubah filter tanggal atau klik tombol reset untuk melihat seluruh katalog layanan.</p>
                <a href="{{ url('/packages') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs">
                    Tampilkan Semua Paket
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $packages->links() }}
    </div>

</div>
@endsection
