<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console') — Tumpak Sewu</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased selection:bg-emerald-600 selection:text-white min-h-screen" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">

        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 border-r border-slate-800 flex flex-col justify-between transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0'">
            
            <div>
                <!-- Brand Logo -->
                <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800/80">
                    <a href="{{ url('/admin') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold">
                            TS
                        </div>
                        <div>
                            <span class="font-extrabold text-sm tracking-tight text-white">Tumpak<span class="text-emerald-400">Sewu</span></span>
                            <span class="block text-[9px] uppercase font-bold tracking-widest text-slate-400">Admin Command</span>
                        </div>
                    </a>
                    <button class="md:hidden text-slate-400 hover:text-white" @click="sidebarOpen = false">&times;</button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 text-xs font-semibold">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 py-2">Operasional Utama</div>
                    
                    <a href="{{ url('/admin') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('admin') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ url('/admin/bookings') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('admin/bookings*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Daftar Booking (15/hal)</span>
                    </a>

                    <a href="{{ url('/admin/checkin') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('admin/checkin*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Scan Check-in QR</span>
                    </a>

                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 py-2 pt-4">Tautan Publik</div>

                    <a href="{{ url('/') }}" target="_blank"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 transition-all duration-200">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Buka Halaman Publik &rarr;</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom User Card -->
            <div class="p-4 border-t border-slate-800/80">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs">
                        A
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-white truncate">Administrator Pos</div>
                        <div class="text-[10px] text-slate-400 truncate">admin@tumpaksewu.com</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Body Wrapper -->
        <div class="flex-1 min-w-0 md:pl-64 flex flex-col">

            <!-- Top Header Bar -->
            <header class="sticky top-0 z-30 h-16 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800 px-4 sm:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button class="md:hidden text-slate-400 hover:text-white p-2" @click="sidebarOpen = true">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-2 text-xs font-semibold">
                        <span class="text-slate-500">Admin</span>
                        <span class="text-slate-600">/</span>
                        <span class="text-white">@yield('title', 'Overview')</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[11px] font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Sistem Aktif & Terlindungi</span>
                    </div>
                </div>
            </header>

            <!-- Main Workspace Content -->
            <main class="flex-1 p-4 sm:p-8 bg-slate-900 space-y-6">
                @if(session('success'))
                    <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-xs font-medium text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Frontend Performance Telemetry -->
    <script src="{{ asset('js/telemetry.js') }}"></script>
    @stack('scripts')
</body>
</html>
