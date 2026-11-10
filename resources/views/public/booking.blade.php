@extends('layouts.public')
@section('title', 'Reservasi ' . $package->name . ' — Tumpak Sewu')

@section('content')
@php
    $slotData = $slots->map(fn($s) => [
        'id' => $s->id,
        'date' => \Illuminate\Support\Carbon::parse($s->date)->toDateString(),
        'dayName' => \Illuminate\Support\Carbon::parse($s->date)->translatedFormat('D'),
        'dayNum' => \Illuminate\Support\Carbon::parse($s->date)->format('d'),
        'month' => \Illuminate\Support\Carbon::parse($s->date)->translatedFormat('M'),
        'remaining' => max(0, $s->capacity - $s->used),
        'capacity' => $s->capacity,
    ])->values();
@endphp

<div x-data="bookingEngine(@js($slotData), {{ (float) $package->price }}, @js($selectedDate ?? null))" class="space-y-8">

    <!-- Stepper Breadcrumb Header -->
    <div class="flex items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <a href="{{ url('/packages') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 mb-1 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Katalog Paket</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $package->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi pemilihan tanggal slot dan data peserta untuk menerbitkan e-voucher resmi.</p>
        </div>

        <!-- Live Hold Badge -->
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold font-mono">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span>PROTEKSI ANTI-OVERBOOKING AKTIF</span>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Left Column: Slot Selector & Participant Forms -->
        <div class="lg:col-span-2 space-y-8">

            <!-- STEP 1: DATE & SLOT SELECTION -->
            <section class="rounded-3xl bg-white border border-slate-200/90 p-6 md:p-8 shadow-sm space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center">
                            1
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Pilih Tanggal Kunjungan</h2>
                            <p class="text-xs text-slate-500">Angka pada setiap tanggal menunjukkan sisa kuota yang tersedia.</p>
                        </div>
                    </div>

                    <template x-if="chosen">
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-mono">
                            Terpilih: <span x-text="fmtFull(chosen.date)"></span>
                        </span>
                    </template>
                </div>

                <!-- Horizontal/Grid Slot Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2.5 pt-2">
                    <template x-for="s in slots" :key="s.id">
                        <button type="button" @click="pick(s)" :disabled="s.remaining === 0"
                            class="relative flex flex-col items-center justify-center p-3 rounded-2xl border text-center transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed group"
                            :class="chosen && chosen.id === s.id 
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-600/30 ring-2 ring-emerald-500 ring-offset-2' 
                                : 'bg-slate-50 hover:bg-white hover:border-emerald-500 border-slate-200 text-slate-800'">
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="chosen && chosen.id === s.id ? 'text-emerald-100' : 'text-slate-400'" x-text="s.dayName"></span>
                            <span class="text-xl font-extrabold my-0.5" x-text="s.dayNum"></span>
                            <span class="text-[10px] font-medium" :class="chosen && chosen.id === s.id ? 'text-emerald-100' : 'text-slate-500'" x-text="s.month"></span>
                            
                            <div class="mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                :class="chosen && chosen.id === s.id 
                                    ? 'bg-white/20 text-white' 
                                    : (s.remaining === 0 ? 'bg-slate-200 text-slate-500' : (s.remaining <= 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'))">
                                <span x-text="s.remaining === 0 ? 'Penuh' : 'Sisa ' + s.remaining"></span>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="slots.length === 0" x-cloak class="p-8 text-center text-slate-400 text-sm">
                    Belum ada kuota slot yang dibuka untuk periode ini.
                </div>
            </section>

            <!-- STEP 2: PARTICIPANTS FORM -->
            <form id="bookingForm" method="POST" action="{{ url('/booking') }}" class="rounded-3xl bg-white border border-slate-200/90 p-6 md:p-8 shadow-sm space-y-6">
                @csrf
                <input type="hidden" name="slot_id" :value="chosen ? chosen.id : ''">

                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center">
                            2
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Data Manifest Peserta</h2>
                            <p class="text-xs text-slate-500">Wajib diisi sesuai KTP/Paspor untuk kepentingan asuransi & manifest pos.</p>
                        </div>
                    </div>

                    <button type="button" @click="add()" :disabled="!chosen || people.length >= chosen.remaining"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Peserta</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(p, i) in people" :key="i">
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4 relative transition-all duration-200 hover:border-slate-300">
                            <!-- Card Header -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-900 text-white text-xs font-bold font-mono" x-text="i + 1"></span>
                                    <span class="text-xs font-bold text-slate-700" x-text="i === 0 ? 'Peserta Utama (Ketua Rombongan)' : 'Peserta Anggota ' + (i + 1)"></span>
                                </div>
                                <button type="button" @click="people.splice(i, 1)" x-show="people.length > 1"
                                    class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>

                            <!-- Input Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Lengkap *</label>
                                    <input type="text" :name="`items[${i}][name]`" x-model="p.name" required placeholder="Sesuai KTP / Paspor"
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. KTP / Paspor</label>
                                    <input type="text" :name="`items[${i}][id_number]`" x-model="p.id_number" placeholder="16 digit NIK"
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. WhatsApp</label>
                                    <input type="tel" :name="`items[${i}][phone]`" x-model="p.phone" placeholder="08xxxxxxxxxx"
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Terms agreement -->
                <div class="pt-2 flex items-start gap-2.5 text-xs text-slate-500">
                    <input type="checkbox" id="agree" required checked class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="agree" class="cursor-pointer leading-relaxed">
                        Saya mengonfirmasi bahwa seluruh data peserta benar dan memahami bahwa paket ini merupakan layanan pemandu & pendamping (bukan tiket gerbang retribusi utama).
                    </label>
                </div>
            </form>
        </div>

        <!-- Right Column: Sticky Summary & Realtime Checkout Card -->
        <aside class="lg:sticky lg:top-24 space-y-5">
            <div class="rounded-3xl bg-slate-900 text-white p-6 md:p-7 shadow-xl border border-slate-800 space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">Ringkasan Reservasi</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-mono">
                        INSTANT LOCK
                    </span>
                </div>

                <!-- Details List -->
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Paket Layanan</span>
                        <span class="text-white font-semibold text-right max-w-[160px] truncate">{{ $package->name }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Tanggal Eksplorasi</span>
                        <span class="text-white font-semibold" x-text="chosen ? fmt(chosen.date) : 'Pilih tanggal di kiri'"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Jumlah Peserta</span>
                        <span class="text-white font-semibold font-mono" x-text="people.length + ' Orang'"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Tarif Layanan</span>
                        <span class="text-white font-semibold" x-text="rp(price) + ' / orang'"></span>
                    </div>
                </div>

                <!-- Price Total Section -->
                <div class="pt-4 border-t border-slate-800 space-y-2">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Biaya</span>
                        <div class="text-2xl font-extrabold text-emerald-400" x-text="rp(price * people.length)"></div>
                    </div>
                    <div class="text-[11px] text-slate-500 text-right">Sudah termasuk pemandu, safety gear & asuransi</div>
                </div>

                <!-- Submit CTA Button -->
                <button form="bookingForm" type="submit" :disabled="!chosen"
                    class="w-full flex items-center justify-center gap-2 py-4 px-6 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm shadow-lg shadow-emerald-500/25 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Kunci Slot & Konfirmasi</span>
                </button>

                <!-- Guarantee Badges -->
                <div class="space-y-2 pt-2 border-t border-slate-800/80 text-[11px] text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400">✓</span>
                        <span>Garansi slot anti-konflik real-time</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400">✓</span>
                        <span>E-voucher & QR instan untuk ditunjukkan di pos</span>
                    </div>
                </div>
            </div>
        </aside>

    </div>

</div>

<script>
function bookingEngine(slots, price, preselect) {
    return {
        slots,
        price,
        chosen: null,
        people: [{ name: '', id_number: '', phone: '' }],

        init() {
            if (preselect) {
                this.chosen = this.slots.find(s => s.date === preselect && s.remaining > 0) || null;
            }
            if (!this.chosen && this.slots.length > 0) {
                // Preselect first available slot with quota
                const firstAvailable = this.slots.find(s => s.remaining > 0);
                if (firstAvailable) this.chosen = firstAvailable;
            }
        },

        pick(s) {
            this.chosen = s;
            if (this.people.length > s.remaining) {
                this.people = this.people.slice(0, s.remaining);
            }
        },

        add() {
            if (this.chosen && this.people.length < this.chosen.remaining) {
                this.people.push({ name: '', id_number: '', phone: '' });
            }
        },

        fmt(d) {
            return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },

        fmtFull(d) {
            return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        rp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }
    };
}
</script>
@endsection
