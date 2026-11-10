@extends('layouts.public')
@section('title', 'Tumpak Sewu Experience — Reservasi Guide & Paket Jelajah')

@section('content')
<div class="space-y-16 md:space-y-24">

    <!-- HERO SECTION -->
    <section class="relative rounded-3xl overflow-hidden bg-slate-950 text-white shadow-2xl border border-slate-800">
        <!-- Ambient Background Glows & Topography Effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-slate-950 to-teal-950 opacity-90"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-teal-500/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 px-6 py-16 sm:px-12 sm:py-24 max-w-5xl mx-auto text-center space-y-8">
            <!-- Super-tag Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 text-xs font-semibold backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Pemandu Wisata Resmi & Pelindung Keselamatan Wisatawan</span>
            </div>

            <!-- Hero Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-[1.15] text-white">
                Taklukkan Kemegahan <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-200">
                    Air Terjun Tumpak Sewu
                </span>
                dengan Aman.
            </h1>

            <!-- Subtitle -->
            <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-300 font-normal leading-relaxed">
                Nikmati petualangan menyusuri lembah eksotis di bawah kaki Gunung Semeru bersama pemandu lokal berlisensi. Slot kuota real-time terintegrasi, e-voucher instan, dan jaminan keamanan ekstra.
            </p>

            <!-- Quick Action Interactive Search Bar -->
            <div class="pt-4 max-w-3xl mx-auto">
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-3 rounded-2xl sm:rounded-full shadow-2xl">
                    <form method="GET" action="{{ url('/packages') }}" class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="flex-1 w-full flex items-center gap-3 px-4 py-2 bg-white/5 rounded-full border border-white/10 sm:border-transparent">
                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="text-left w-full">
                                <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tanggal Eksplorasi</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                    class="bg-transparent text-white font-semibold text-sm focus:outline-none w-full cursor-pointer">
                            </div>
                        </div>

                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/30 transition-all duration-300 hover:scale-[1.03]">
                            <span>Cek Ketersediaan Slot</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Trust Highlights Metrics -->
            <div class="pt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center border-t border-slate-800/80">
                <div class="p-3">
                    <div class="text-2xl font-extrabold text-white">100%</div>
                    <div class="text-xs text-slate-400 mt-0.5">Guide Lokal Berlisensi</div>
                </div>
                <div class="p-3">
                    <div class="text-2xl font-extrabold text-emerald-400">0 Overbooking</div>
                    <div class="text-xs text-slate-400 mt-0.5">Sistem Kunci Slot Otomatis</div>
                </div>
                <div class="p-3">
                    <div class="text-2xl font-extrabold text-white">4.9 / 5.0</div>
                    <div class="text-xs text-slate-400 mt-0.5">Rating Kepuasan Tamu</div>
                </div>
                <div class="p-3">
                    <div class="text-2xl font-extrabold text-amber-400">&lt; 1 Menit</div>
                    <div class="text-xs text-slate-400 mt-0.5">Penerbitan E-Voucher & QR</div>
                </div>
            </div>
        </div>
    </section>

    <!-- OFFICIAL RETRIBUTION NOTICE BANNER -->
    <section class="rounded-2xl bg-amber-500/10 border border-amber-500/30 p-6 md:p-8 backdrop-blur-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-amber-950">Pemberitahuan Wajib bagi Pengunjung</h3>
                    <p class="text-sm text-slate-600 leading-relaxed max-w-2xl">
                        Tiket masuk retribusi kawasan gerbang utama dikelola langsung oleh dinas pariwisata pemerintah melalui portal resmi <strong>cobansewu.com</strong> (efektif sejak 27 Feb 2025). Platform ini memfasilitasi <em>pemandu bersertifikat, safety harness, sepatu trekking, dan dokumentasi</em> untuk keselamatan Anda saat turun ke dasar lembah.
                    </p>
                </div>
            </div>
            <a href="https://cobansewu.com" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-sm flex-shrink-0">
                <span>Kunjungi Portal Resmi Tiket</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </section>

    <!-- WHY YOU NEED A LOCAL GUIDE -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-600">Standard Keselamatan Tingkat Tinggi</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Mengapa Anda Membutuhkan Pemandu Lokal?</h2>
            <p class="text-sm text-slate-500">Dasar tebing Tumpak Sewu memiliki topografi curam dengan bebatuan basah dan aliran sungai deras yang menuntut keahlian navigasi khusus.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    🛡️
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Navigasi Jalur Curam & Ekstrem</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Turunan tangga bambu dan tebing air terjun memerlukan pijakan akurat. Pemandu kami memandu langkah Anda langkah demi langkah dengan safety gear lengkap.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-lg">
                    🌊
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Pemantauan Debit Air Real-Time</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Pemandu terhubung langsung dengan tim pos hulu sungai Semeru untuk mendeteksi potensi peningkatan debit air secara cepat dan memastikan kunjungan selalu aman.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300 space-y-3">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-lg">
                    📸
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Spot Foto Ikonik & Goa Tetes</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Akses angle foto terbaik di dasar tirai air terjun dan eksplorasi stalaktit Goa Tetes tanpa tersesat atau merusak ekosistem alam sekitar.
                </p>
            </div>
        </div>
    </section>

    <!-- STEP BY STEP PROCESS -->
    <section class="rounded-3xl bg-slate-900 text-white p-8 md:p-14 space-y-10">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Kemudahan Reservasi Digital</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Alur Pemesanan 3 Langkah Praktis</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <div class="space-y-3">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold text-sm">
                    1
                </div>
                <h3 class="text-lg font-bold text-white">Pilih Paket & Tanggal</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Tentukan layanan guide mandiri, paket rombongan, atau paket dokumentasi. Pilih tanggal pada kalender dengan sisa kuota yang ditampilkan real-time.
                </p>
            </div>

            <div class="space-y-3">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold text-sm">
                    2
                </div>
                <h3 class="text-lg font-bold text-white">Kunci Slot (Hold 10 Menit)</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Sistem mengunci kuota peserta secara eksklusif agar tidak terjadi bentrok dengan pengunjung lain. Lakukan pembayaran via saluran pembayaran aman.
                </p>
            </div>

            <div class="space-y-3">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold text-sm">
                    3
                </div>
                <h3 class="text-lg font-bold text-white">E-Voucher & Check-in Cepat</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    E-Voucher ber-QR Code otomatis terbit seketika. Tunjukkan QR Code pada petugas pos pemandu di lokasi untuk scan check-in tanpa antre.
                </p>
            </div>
        </div>

        <div class="text-center pt-4">
            <a href="{{ url('/packages') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm transition-all duration-300 hover:scale-105 shadow-xl shadow-emerald-500/20">
                <span>Mulai Reservasi Sekarang</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </section>

    <!-- RESCHEDULE & REFUND POLICY -->
    <section class="rounded-2xl bg-white border border-slate-200/80 p-6 md:p-8 space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                📋
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Kebijakan Reschedule & Pembatalan Fleksibel</h3>
                <p class="text-xs text-slate-500">Transparansi dan kenyamanan perjalanan Anda adalah prioritas kami.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 text-sm text-slate-600">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <div class="font-bold text-slate-800">Ketentuan Reschedule Tanggal</div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Reschedule tanggal kunjungan bebas biaya dapat dilakukan maksimal 1 kali selambat-lambatnya <strong>H-3 sebelum tanggal kunjungan</strong> (tergantung ketersediaan kuota slot baru).
                </p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                <div class="font-bold text-slate-800">Ketentuan Pembatalan & Cuaca Ekstrem</div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pembatalan pada H-3 atau lebih mendapatkan pengembalian 100%. Jika jalur ditutup mendadak oleh pos pengawas akibat bahaya lahar/debit banjir, dana paket akan di-refund penuh.
                </p>
            </div>
        </div>
    </section>

</div>
@endsection
