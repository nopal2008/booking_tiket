@extends('layouts.public')
@section('title', 'Pembayaran Sandbox — ' . $booking->booking_code)

@section('content')
<div class="max-w-3xl mx-auto space-y-8" x-data="paymentGateway()">

    <!-- Header & Timer Section -->
    <div class="rounded-3xl bg-slate-950 text-white p-6 sm:p-8 shadow-xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="space-y-1 text-center sm:text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-mono font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                <span>SANDBOX SIMULATION PAYMENT</span>
            </span>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Selesaikan Pembayaran</h1>
            <p class="text-xs text-slate-400">Kode Booking: <span class="font-mono font-bold text-emerald-400">{{ $booking->booking_code }}</span></p>
        </div>

        <!-- 10-Minute Countdown Clock -->
        <div class="text-center sm:text-right bg-slate-900/80 px-5 py-3 rounded-2xl border border-slate-800">
            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sisa Waktu Hold Slot</span>
            <div class="text-2xl font-extrabold font-mono text-amber-400" x-text="formatTime(timeLeft)">10:00</div>
        </div>
    </div>

    <!-- Booking Summary Card -->
    <div class="rounded-3xl bg-white border border-slate-200/90 p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Detail Tagihan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-1">
            <div>
                <span class="text-slate-400">Paket</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ $booking->slot?->package?->name }}</div>
            </div>
            <div>
                <span class="text-slate-400">Tanggal</span>
                <div class="font-bold text-slate-800 mt-0.5">{{ \Carbon\Carbon::parse($booking->slot?->date)->translatedFormat('d M Y') }}</div>
            </div>
            <div>
                <span class="text-slate-400">Peserta</span>
                <div class="font-bold text-slate-800 mt-0.5 font-mono">{{ $booking->items->count() }} Orang</div>
            </div>
            <div>
                <span class="text-slate-400">Total Pembayaran</span>
                <div class="font-bold text-emerald-600 text-base font-mono mt-0.5">
                    Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Selector -->
    <div class="rounded-3xl bg-white border border-slate-200/90 p-6 sm:p-8 shadow-sm space-y-6">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pilih Metode Pembayaran (Sandbox)</h2>

        <!-- Method Tabs -->
        <div class="grid grid-cols-3 gap-3">
            <button type="button" @click="method = 'qris'"
                class="p-4 rounded-2xl border text-center transition-all duration-200 flex flex-col items-center justify-center gap-2"
                :class="method === 'qris' ? 'border-emerald-600 bg-emerald-50/50 shadow-md ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                <span class="text-xl">📱</span>
                <span class="text-xs font-bold text-slate-900">QRIS</span>
                <span class="text-[10px] text-slate-500">GoPay, OVO, BCA, Dana</span>
            </button>

            <button type="button" @click="method = 'va'"
                class="p-4 rounded-2xl border text-center transition-all duration-200 flex flex-col items-center justify-center gap-2"
                :class="method === 'va' ? 'border-emerald-600 bg-emerald-50/50 shadow-md ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                <span class="text-xl">🏦</span>
                <span class="text-xs font-bold text-slate-900">Virtual Account</span>
                <span class="text-[10px] text-slate-500">BCA, Mandiri, BNI, BRI</span>
            </button>

            <button type="button" @click="method = 'card'"
                class="p-4 rounded-2xl border text-center transition-all duration-200 flex flex-col items-center justify-center gap-2"
                :class="method === 'card' ? 'border-emerald-600 bg-emerald-50/50 shadow-md ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'">
                <span class="text-xl">💳</span>
                <span class="text-xs font-bold text-slate-900">Kartu Kredit</span>
                <span class="text-[10px] text-slate-500">Visa / Mastercard</span>
            </button>
        </div>

        <!-- Content for QRIS -->
        <div x-show="method === 'qris'" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-4">
            <div class="max-w-xs mx-auto p-4 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center">
                <span class="font-extrabold text-xs text-slate-900 tracking-wider">NMID: ID1020030040050</span>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode('QRIS-TUMPAKSEWU-' . $booking->booking_code) }}"
                     alt="QRIS Code" class="w-44 h-44 my-3 rounded-lg">
                <span class="text-[11px] font-bold text-slate-600 uppercase">Tumpak Sewu Official Escrow</span>
            </div>
            <p class="text-xs text-slate-500">Buka aplikasi mobile banking atau e-wallet apa saja untuk melakukan scan QRIS di atas.</p>
        </div>

        <!-- Content for Virtual Account -->
        <div x-show="method === 'va'" x-cloak class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Nomor Virtual Account BCA</span>
                    <div class="text-lg font-mono font-extrabold text-slate-900">8801 9823 4567 8901</div>
                </div>
                <button type="button" @click="copied = true; setTimeout(() => copied = false, 2000)" 
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition-colors">
                    <span x-text="copied ? 'Tersalin ✓' : 'Salin'"></span>
                </button>
            </div>
            <p class="text-xs text-slate-500">Gunakan menu Transfer &gt; Virtual Account pada m-banking Anda.</p>
        </div>

        <!-- Content for Card -->
        <div x-show="method === 'card'" x-cloak class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm font-mono text-xs text-slate-700 space-y-2">
                <div class="flex justify-between"><span class="text-slate-400">Nomor Kartu Tes:</span><span class="font-bold">4000 0012 3456 7890</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Masa Berlaku:</span><span>12 / 28</span></div>
                <div class="flex justify-between"><span class="text-slate-400">CVV:</span><span>123</span></div>
            </div>
            <p class="text-xs text-slate-500">Transaksi menggunakan saluran sandbox simulasi aman tanpa memotong dana nyata.</p>
        </div>

        <!-- Submit Simulation Action -->
        <form method="POST" action="{{ url('/payment/' . $booking->booking_code . '/simulate') }}" class="pt-4 space-y-3">
            @csrf
            <input type="hidden" name="method" :value="method">
            
            <button type="submit" 
                class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/25 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Simulasikan Pembayaran Berhasil (Konfirmasi Instan)</span>
            </button>

            <div class="text-center">
                <a href="{{ url('/status?code=' . $booking->booking_code) }}" class="text-xs text-slate-400 hover:text-slate-600">
                    Cek Status Booking Sementara &rarr;
                </a>
            </div>
        </form>
    </div>

</div>

<script>
function paymentGateway() {
    return {
        method: 'qris',
        copied: false,
        timeLeft: 600, // 10 menit
        init() {
            setInterval(() => {
                if (this.timeLeft > 0) this.timeLeft--;
            }, 1000);
        },
        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
    };
}
</script>
@endsection

