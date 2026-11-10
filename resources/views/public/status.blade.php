@extends('layouts.public')
@section('title', 'Cek Status & E-Voucher — Tumpak Sewu')

@push('styles')
<style>
@media print {
    /* Set page margins and force exact background print */
    @page {
        margin: 1cm;
        size: auto;
    }
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Hide headers, footers, telemetry monitor, and no-print elements */
    header, footer, #telemetry-hud, .no-print, .no-print * {
        display: none !important;
        visibility: hidden !important;
    }

    /* Hide everything inside main */
    main, main * {
        visibility: hidden;
    }

    /* Show ONLY the printable voucher card */
    #printable-voucher, #printable-voucher * {
        visibility: visible;
    }

    #printable-voucher {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 1px solid #94a3b8 !important;
        border-radius: 20px !important;
        box-shadow: none !important;
        overflow: hidden !important;
    }

    /* Keep action buttons inside the voucher hidden */
    #printable-voucher .no-print,
    #printable-voucher .no-print * {
        display: none !important;
        visibility: hidden !important;
    }
}
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-8">

    <!-- Header & Search Form Section (Hidden on Print) -->
    <div class="no-print space-y-8">
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold font-mono">
                <span>VERIFIKASI DIGITAL E-VOUCHER</span>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Cek Status Reservasi</h1>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Masukkan kode unik reservasi (format TS-XXXXXXXX) untuk memeriksa status pemesanan atau mencetak e-voucher resmi Anda.
            </p>
        </div>

        <!-- Search Form -->
        <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-sm">
            <form method="GET" action="{{ url('/status') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="code" value="{{ $code ?? '' }}" required placeholder="Contoh: TS-SAMPLE01"
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono font-semibold text-slate-900 placeholder:text-slate-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition-all duration-300">
                    Periksa Status
                </button>
            </form>

            <!-- Quick Sample Code Link -->
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Uji coba dengan contoh reservasi:</span>
                <a href="{{ url('/status?code=TS-SAMPLE01') }}" class="text-emerald-700 font-mono font-bold hover:underline">
                    TS-SAMPLE01
                </a>
            </div>
        </div>

        <!-- Result: Not Found -->
        @if(!empty($code) && empty($booking))
            <div class="rounded-3xl bg-rose-50 border border-rose-200 p-6 text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-xl font-bold">
                    ✕
                </div>
                <h3 class="text-base font-bold text-rose-900">Reservasi Tidak Ditemukan</h3>
                <p class="text-xs text-rose-700 max-w-sm mx-auto">
                    Kode reservasi <strong>"{{ $code }}"</strong> tidak cocok dengan arsip kami. Mohon periksa kembali ejaan kode dari konfirmasi Anda.
                </p>
            </div>
        @endif
    </div>

    <!-- Result: Booking Found - Boarding Pass Design -->
    @if(!empty($booking))
        @php
            $statusMeta = [
                'confirmed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'dot' => 'bg-emerald-500', 'label' => 'Terkonfirmasi (Siap Eksplorasi)'],
                'checked_in' => ['bg' => 'bg-teal-100 text-teal-800 border-teal-300', 'dot' => 'bg-teal-500', 'label' => 'Selesai Check-in di Pos'],
                'paid' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-300', 'dot' => 'bg-blue-500', 'label' => 'Terbayar'],
                'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'dot' => 'bg-amber-500', 'label' => 'Menunggu Pembayaran'],
                'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'dot' => 'bg-rose-500', 'label' => 'Dibatalkan'],
            ][$booking->status] ?? ['bg' => 'bg-slate-100 text-slate-800 border-slate-300', 'dot' => 'bg-slate-500', 'label' => $booking->status];
        @endphp

        <div id="printable-voucher" class="relative rounded-3xl bg-white border border-slate-200/90 shadow-xl overflow-hidden transition-all duration-300">

            <!-- Top Header of the Boarding Pass -->
            <div class="bg-gradient-to-br from-emerald-800 via-teal-900 to-slate-950 p-6 md:p-8 text-white relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-300">E-Voucher Resmi Pendamping</div>
                        <h2 class="text-2xl font-black font-mono tracking-tight mt-0.5">{{ $booking->booking_code }}</h2>
                        <p class="text-xs text-slate-300 mt-1">{{ $booking->slot?->package?->name ?? 'Paket Layanan' }}</p>
                    </div>

                    <!-- Status Pill -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full {{ $statusMeta['bg'] }} border text-xs font-bold self-start sm:self-auto shadow-sm">
                        <span class="w-2 h-2 rounded-full {{ $statusMeta['dot'] }} animate-pulse"></span>
                        <span>{{ $statusMeta['label'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Perforated Cutout Line Divider -->
            <div class="relative bg-white py-3">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-50 border-r border-slate-200"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-50 border-l border-slate-200"></div>
                <div class="border-t-2 border-dashed border-slate-200 mx-6"></div>
            </div>

            <!-- Pass Body Content -->
            <div class="p-6 md:p-8 space-y-6">
                <!-- Grid Highlights -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="space-y-1">
                        <span class="text-slate-400 font-medium">Tanggal Kunjungan</span>
                        <div class="text-sm font-extrabold text-slate-900">
                            {{ \Carbon\Carbon::parse($booking->slot?->date)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-400 font-medium">Jumlah Peserta</span>
                        <div class="text-sm font-extrabold text-slate-900 font-mono">
                            {{ $booking->items->count() }} Orang
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-400 font-medium">Total Tarif</span>
                        <div class="text-sm font-extrabold text-emerald-700 font-mono">
                            Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-400 font-medium">Waktu Kumpul</span>
                        <div class="text-sm font-extrabold text-slate-900">
                            07:30 - 08:30 WIB
                        </div>
                    </div>
                </div>

                <!-- Participant Manifest Table -->
                <div class="space-y-3 pt-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Manifest Peserta Terdaftar</h3>
                    <div class="rounded-2xl border border-slate-100 overflow-hidden text-xs">
                        <table class="w-full">
                            <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="p-3 text-left">No.</th>
                                    <th class="p-3 text-left">Nama Lengkap</th>
                                    <th class="p-3 text-left">No. Identitas</th>
                                    <th class="p-3 text-left">WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($booking->items as $idx => $item)
                                    <tr>
                                        <td class="p-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-semibold text-slate-900">{{ $item->name }}</td>
                                        <td class="p-3 font-mono text-slate-600">{{ $item->id_number ?: '-' }}</td>
                                        <td class="p-3 font-mono text-slate-600">{{ $item->phone ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- QR Code Verification Section -->
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            Tunjukkan di Pos Lapangan
                        </span>
                        <h4 class="text-sm font-bold text-slate-900">QR Validasi Pemandu</h4>
                        <p class="text-xs text-slate-500 max-w-sm">
                            Pemandu akan melakukan scan kamera langsung pada QR ini untuk verifikasi kehadiran tanpa perlu cetak fisik.
                        </p>
                    </div>

                    <!-- Clean QR Code Visual Generator -->
                    <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center flex-shrink-0">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($booking->booking_code) }}&margin=4" 
                             alt="QR Code {{ $booking->booking_code }}"
                             class="w-28 h-28 rounded-lg"
                             loading="lazy">
                        <span class="font-mono text-[10px] font-bold text-slate-600 mt-1">{{ $booking->booking_code }}</span>
                    </div>
                </div>

                <!-- Action Buttons (Hidden on Print) -->
                <div class="pt-4 flex flex-col sm:flex-row items-center gap-3 no-print">
                    <button onclick="window.print()" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak / Simpan E-Voucher</span>
                    </button>
                    <a href="https://maps.google.com/?q=Air+Terjun+Tumpak+Sewu+Lumajang" target="_blank" rel="noopener" class="w-full sm:w-auto py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Titik Kumpul (Maps)</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
