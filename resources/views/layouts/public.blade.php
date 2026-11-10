<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tumpak Sewu — Reservasi Layanan Pendamping & Pemandu Lokal')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN with Custom Config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        forest: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(15, 23, 42, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-600 selection:text-white min-h-screen flex flex-col">

    <!-- Official Notice Disclaimer Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    INFO RESMI
                </span>
                <span>
                    Website ini merupakan layanan resmi pendamping (guide, porter & paket). Tiket masuk retribusi kawasan dikelola mandiri via
                    <a href="https://cobansewu.com" target="_blank" rel="noopener noreferrer" class="text-emerald-400 font-medium underline hover:text-emerald-300 ml-1">cobansewu.com</a>.
                </span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400">
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Kuota Slot Real-time</span>
                <a href="{{ url('/status') }}" class="hover:text-white transition-colors">Cek Status Reservasi &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 flex items-center justify-center text-white shadow-lg shadow-emerald-700/20 group-hover:scale-105 transition-transform duration-300">
                    <!-- Waterfall / Mountain Minimalist Icon -->
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3l-6 12h5l-2 6 8-10h-5l4-8z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">
                        Tumpak<span class="text-emerald-600">Sewu</span>
                    </span>
                    <span class="block text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        Experience & Guiding
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl text-sm font-semibold {{ request()->is('/') ? 'text-emerald-700 bg-emerald-50/80 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                    Beranda
                </a>
                <a href="{{ url('/packages') }}" class="px-4 py-2 rounded-xl text-sm font-semibold {{ request()->is('packages*') ? 'text-emerald-700 bg-emerald-50/80 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                    Katalog Paket
                </a>
                <a href="{{ url('/status') }}" class="px-4 py-2 rounded-xl text-sm font-semibold {{ request()->is('status*') ? 'text-emerald-700 bg-emerald-50/80 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }} transition-colors">
                    Cek Booking
                </a>
                <a href="{{ url('/admin') }}" class="ml-2 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                    Admin Portal
                </a>
            </nav>

            <!-- Action Button -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/packages') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/25 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                    <span>Pilih Paket</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Global Flash Notification Toast -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-4 flex items-start gap-3 backdrop-blur-sm shadow-sm mb-4">
                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1 text-sm font-medium text-emerald-900">
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 text-lg leading-none">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms class="rounded-2xl bg-rose-500/10 border border-rose-500/30 p-4 flex items-start gap-3 backdrop-blur-sm shadow-sm mb-4">
                <div class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 text-sm text-rose-900">
                    <div class="font-bold mb-1">Harap periksa kembali:</div>
                    <ul class="list-disc pl-5 space-y-0.5 font-medium">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-rose-700 hover:text-rose-950 text-lg leading-none">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        @yield('content')
    </main>

    <!-- Luxury Footer -->
    <footer class="bg-slate-950 text-slate-300 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3l-6 12h5l-2 6 8-10h-5l4-8z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">Tumpak<span class="text-emerald-400">Sewu</span> Reservasi</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        Platform resmi pemesanan jasa pemandu wisata berlisensi, safety gear, dan paket jelajah Air Terjun Tumpak Sewu, Lumajang - Malang, Jawa Timur.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                        Status Sistem: Normal (Zero Overselling Guarantee)
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Navigasi Layanan</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ url('/') }}" class="hover:text-emerald-400 transition-colors">Beranda</a></li>
                        <li><a href="{{ url('/packages') }}" class="hover:text-emerald-400 transition-colors">Katalog Paket Pendamping</a></li>
                        <li><a href="{{ url('/status') }}" class="hover:text-emerald-400 transition-colors">Cek Status Tiket / E-Voucher</a></li>
                        <li><a href="{{ url('/admin') }}" class="hover:text-emerald-400 transition-colors">Portal Pemandu & Admin</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Pemberitahuan Wajib</h4>
                    <div class="rounded-xl bg-slate-900 p-4 border border-slate-800 text-xs text-slate-400 space-y-2">
                        <p class="font-semibold text-slate-300">Bukan Pengganti Tiket Gerbang</p>
                        <p class="leading-relaxed">
                            Berdasarkan regulasi resmi per 27 Februari 2025, tiket masuk gerbang utama wajib diperoleh via portal pemerintah <a href="https://cobansewu.com" target="_blank" class="text-emerald-400 underline">cobansewu.com</a>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Tumpak Sewu Guiding & Reservation Network. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="text-slate-400 font-mono">Engineered for Performance (P95 &lt; 200ms)</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Frontend Performance & Health Telemetry Script -->
    <script src="{{ asset('js/telemetry.js') }}"></script>
    @stack('scripts')
</body>
</html>
