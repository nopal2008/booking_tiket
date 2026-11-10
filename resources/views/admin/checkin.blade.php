@extends('layouts.admin')
@section('title', 'Scan Check-in QR')

@push('styles')
<style>
@keyframes laser {
    0% { top: 5%; opacity: 0.2; }
    50% { opacity: 1; }
    100% { top: 90%; opacity: 0.2; }
}
.animate-laser {
    animation: laser 2.2s ease-in-out infinite alternate;
}
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="checkinScanner()" x-init="initScanner()">

    <!-- Header Section -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>POS PEMANDU & VALIDASI TIKET</span>
        </div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Scanner Kamera Check-in</h1>
        <p class="text-xs text-slate-400">
            Arahkan kamera ke QR Code pada E-Voucher pengunjung atau masukkan kode manual untuk memverifikasi kehadiran di pos.
        </p>
    </div>

    <!-- Scanner Viewfinder Card -->
    <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6 shadow-2xl relative overflow-hidden">
        
        <!-- Camera Viewfinder Frame -->
        <div class="relative w-full aspect-video sm:aspect-square max-w-sm mx-auto rounded-2xl overflow-hidden bg-slate-900 border border-slate-700/60 shadow-inner flex items-center justify-center">
            
            <!-- Html5Qrcode Camera Target -->
            <div id="reader" class="w-full h-full object-cover"></div>

            <!-- Futuristic Laser Line Overlay -->
            <div class="absolute inset-x-6 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_15px_#10b981] animate-laser pointer-events-none"></div>

            <!-- Viewfinder Corner Accents -->
            <div class="absolute top-4 left-4 w-6 h-6 border-t-2 border-l-2 border-emerald-400 pointer-events-none"></div>
            <div class="absolute top-4 right-4 w-6 h-6 border-t-2 border-r-2 border-emerald-400 pointer-events-none"></div>
            <div class="absolute bottom-4 left-4 w-6 h-6 border-b-2 border-l-2 border-emerald-400 pointer-events-none"></div>
            <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-emerald-400 pointer-events-none"></div>

            <!-- Camera Status Text -->
            <div x-show="!cameraReady" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center text-slate-400 text-xs space-y-2 p-4 text-center">
                <svg class="w-8 h-8 text-slate-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menghubungkan ke lensa kamera...</span>
                <span class="text-[10px] text-slate-500">Pastikan izin kamera diizinkan di browser</span>
            </div>
        </div>

        <!-- Manual Input Fallback Bar -->
        <div class="mt-6 pt-6 border-t border-slate-800 space-y-3">
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider text-center">
                Atau Masukkan Kode Booking Secara Manual
            </span>

            <form @submit.prevent="verifyToken(manualToken)" class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" x-model="manualToken" placeholder="Contoh: TS-SAMPLE01"
                        class="w-full bg-slate-900 border border-slate-700/80 rounded-2xl px-4 py-3 text-sm font-mono font-bold text-white uppercase placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" :disabled="busy || !manualToken"
                    class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="busy ? 'Memverifikasi...' : 'Verifikasi'"></span>
                </button>
            </form>

            <div class="flex items-center justify-center gap-3 text-xs text-slate-500">
                <span>Tes kode contoh:</span>
                <button type="button" @click="manualToken = 'TS-SAMPLE01'; verifyToken('TS-SAMPLE01')" class="text-emerald-400 font-mono font-bold hover:underline">
                    TS-SAMPLE01
                </button>
            </div>
        </div>
    </div>

    <!-- Scan Result Card -->
    <div x-show="result" x-cloak class="rounded-3xl border p-6 transition-all duration-300 shadow-xl"
        :class="result && result.ok ? 'bg-emerald-950/80 border-emerald-500/50 text-emerald-100' : 'bg-rose-950/80 border-rose-500/50 text-rose-100'">
        
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-lg flex-shrink-0"
                :class="result && result.ok ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'">
                <span x-text="result && result.ok ? '✓' : '✕'"></span>
            </div>

            <div class="flex-1 space-y-1">
                <h3 class="text-base font-bold text-white" x-text="result && result.ok ? 'Check-in Berhasil Dikonfirmasi!' : 'Validasi Check-in Gagal'"></h3>
                <p class="text-xs opacity-90" x-text="result && result.message"></p>

                <template x-if="result && result.booking">
                    <div class="mt-4 pt-3 border-t border-emerald-500/30 grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <span class="block text-[10px] opacity-70">Kode</span>
                            <span class="font-mono font-bold text-white" x-text="result.booking.code"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] opacity-70">Paket</span>
                            <span class="font-medium text-white" x-text="result.booking.package"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] opacity-70">Rombongan</span>
                            <span class="font-bold text-emerald-400 font-mono" x-text="result.booking.people + ' Orang'"></span>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="result = null" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function checkinScanner() {
    return {
        manualToken: '',
        result: null,
        busy: false,
        cameraReady: false,
        qrInstance: null,

        initScanner() {
            setTimeout(() => {
                if (typeof Html5Qrcode === 'undefined') return;

                this.qrInstance = new Html5Qrcode('reader');
                this.qrInstance.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: 240 },
                    (decodedText) => this.verifyToken(decodedText),
                    () => {}
                )
                .then(() => {
                    this.cameraReady = true;
                })
                .catch(() => {
                    this.cameraReady = false;
                });
            }, 300);
        },

        async verifyToken(token) {
            token = (token || '').trim();
            if (!token || this.busy) return;

            this.busy = true;

            try {
                const response = await fetch('{{ url('/admin/checkin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ token })
                });

                const data = await response.json();
                this.result = data;
            } catch (err) {
                this.result = {
                    ok: false,
                    message: 'Terjadi gangguan koneksi ke server saat memverifikasi token.'
                };
            }

            // Debounce scanner to prevent double scans
            setTimeout(() => {
                this.busy = false;
            }, 2500);
        }
    };
}
</script>
@endpush
