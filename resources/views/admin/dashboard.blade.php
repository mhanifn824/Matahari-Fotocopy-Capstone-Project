<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Operasional Gerai - Matahari Fotocopy</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN for instant fidelity matching -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        sidebar: {
                            DEFAULT: '#dce8f7',
                            light: '#e8f1fc',
                            dark: '#c7daf2',
                        },
                        amberbrand: {
                            500: '#f59e0b',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .sidebar-bg {
            background: linear-gradient(180deg, #d3e6fe 0%, #bfdbfe 35%, #a3cdfe 70%, #96c7fe 100%);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'dashboard', 'activeAntreanCount' => $stats['antrean_aktif']])


    <!-- ========================================== -->
    <!-- RIGHT MAIN CONTENT AREA                    -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-8 py-3 flex flex-wrap items-center justify-between gap-4">
            
            <!-- Search Bar Field -->
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" id="global-search-input" onkeyup="filterQueueTable()" placeholder="Cari no. antrean, nota, nama..." class="w-full pl-10 pr-4 py-2 text-xs bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-slate-200/80 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-slate-800 placeholder-slate-400">
            </div>

            <!-- Right Info & Actions -->
            <div class="flex items-center gap-3 sm:gap-4">
                
                <!-- Shift Status Capsule & Role Badge -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-200/90 shadow-2xs text-xs font-bold text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <span>Buka • Shift Siang</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-[10px] font-mono font-semibold text-slate-500">Hak Akses Terverifikasi: Admin / Kasir</span>
                </div>

                <!-- Live Clock & Date -->
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/60 font-mono">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="live-clock">14:32 WIB • 24 Okt</span>
                </div>

                <!-- Notification Bell with Red Badge -->
                <div class="relative">
                    <button type="button" onclick="toggleNotificationDropdown()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    <span id="notif-badge" class="absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white {{ $stats['bahan_kritis_count'] > 0 ? '' : 'hidden' }}"></span>

                    <!-- Dropdown notification box -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200 p-4 z-50 text-xs space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 font-bold text-slate-800">
                            <span>Notifikasi Real-Time</span>
                            <span class="text-[10px] text-amber-600 font-mono font-normal">Sistem Live</span>
                        </div>
                        <div id="notif-content" class="space-y-2">
                            @if($stats['bahan_kritis_count'] > 0)
                                <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-100 text-rose-800">
                                    <p class="font-bold">Peringatan Bahan Menipis!</p>
                                    <p class="text-[11px] text-rose-600 mt-0.5">Tersisa {{ $stats['bahan_kritis_count'] }} item inventaris di bawah batas aman.</p>
                                </div>
                            @else
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-800">
                                    <p class="font-bold">Status Sistem Normal</p>
                                    <p class="text-[11px] text-emerald-600 mt-0.5">Semua stok bahan baku aman dan siap melayani pesanan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Profile Silhouette (Warm Brown/Caramel Circle) -->
                <div class="w-8 h-8 rounded-full bg-[#92400e] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <svg class="w-4 h-4 text-amber-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>

            </div>

        </header>

        <!-- MAIN DASHBOARD CONTENT BODY -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

            <!-- PAGE SUB-HEADER TITLE & ACTION BUTTONS -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <div>
                    <div class="flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-amber-600 font-mono">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>PUSAT KENDALI KASIR & PRODUKSI</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                        Dashboard Operasional Gerai
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        Pantau kinerja pesanan harian, omzet, antrean mesin, dan inventaris gerai Bintaro Sektor 7 secara waktu nyata.
                    </p>
                </div>

                <!-- Action Info Group -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <!-- Info Shift & Date Card -->
                    <div class="flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200/90 rounded-xl text-xs shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-slate-800 leading-tight">Hari Ini: <span id="header-date-text">{{ now()->format('d M Y') }}</span></p>
                            <p class="text-[10px] text-slate-400 font-medium">Shift Siang • {{ $user->nama ?? 'Feri Anto' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ALERT BANNER: PERINGATAN STOK KRITIS / STATUS NORMAL -->
            <div id="stock-alert-banner">
                @if($stats['bahan_kritis_count'] > 0)
                    <!-- Critical Warning State (Matches Reference Screenshot) -->
                    <div class="bg-[#fef2f2] border border-[#fecaca] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-rose-800 text-sm">Peringatan Stok Kritis!</span>
                                    <span class="bg-rose-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider font-mono">
                                        TERISISA {{ $stats['bahan_kritis_items']->first()->jumlah_stok ?? 2 }} {{ $stats['bahan_kritis_items']->first()->satuan ?? 'RIM' }}
                                    </span>
                                </div>
                                <p class="text-xs text-rose-700/90 mt-0.5">
                                    {{ $stats['bahan_kritis_items']->first()->nama_bahan ?? 'Kertas HVS A4 70gr' }} menipis. Segera lakukan pemesanan ulang ke supplier CV Sinar Abadi untuk mencegah hambatan antrean.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.stok.index') }}" class="px-4 py-2 bg-white hover:bg-rose-100 text-rose-800 border border-rose-200 rounded-xl text-xs font-bold transition flex-shrink-0 shadow-2xs">
                            Buka Menu Stok
                        </a>
                    </div>
                @else
                    <!-- Optimal Safe State (Clean Empty / Safe condition) -->
                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-emerald-800 text-sm">Status Inventaris Gerai Optimal</span>
                                    <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider font-mono">
                                        SEMUA STOK AMAN
                                    </span>
                                </div>
                                <p class="text-xs text-emerald-700 mt-0.5">
                                    Seluruh bahan baku kertas HVS, toner cetak, dan perlengkapan jilid berada di atas batas minimum. Tidak ada peringatan kritis saat ini.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.stok.index') }}" class="px-4 py-2 bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition flex-shrink-0 shadow-2xs">
                            Buka Menu Stok
                        </a>
                    </div>
                @endif
            </div>

            <!-- ========================================== -->
            <!-- 4 METRIC KPI CARDS                         -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- CARD 1: PESANAN HARI INI -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">PESANAN HARI INI</span>
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span id="stat-pesanan-count" class="text-3xl font-black text-slate-900 tracking-tight font-mono">
                                {{ $stats['pesanan_hari_ini'] }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">Nota</span>
                        </div>

                        <div id="stat-pesanan-trend" class="text-xs mt-1 font-semibold flex items-center gap-1">
                            @if($stats['pesanan_hari_ini'] > 0)
                                <span class="text-emerald-600">↗ +14% vs kemarin</span>
                            @else
                                <span class="text-slate-400">Belum ada pesanan hari ini</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 mt-3 border-t border-slate-100 flex items-center gap-4 text-[11px] text-slate-600 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            <span id="stat-pesanan-selesai">{{ $stats['pesanan_selesai'] }} Selesai</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span id="stat-pesanan-proses">{{ $stats['pesanan_proses'] }} Proses</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: PENDAPATAN SHIFT -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">PENDAPATAN SHIFT</span>
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-xs font-bold text-slate-500">Rp</span>
                            <span id="stat-pendapatan-val" class="text-2xl sm:text-[26px] font-black text-slate-900 tracking-tight font-mono">
                                {{ number_format($stats['pendapatan_shift'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                            <span>Target: Rp 1.500.000</span>
                            <span id="stat-pendapatan-pct" class="font-bold text-slate-700 font-mono">{{ $stats['persentase_pendapatan'] }}%</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="pt-4 mt-3 border-t border-slate-100">
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div id="stat-pendapatan-bar" class="h-full bg-[#f59e0b] rounded-full transition-all duration-500" style="width: {{ $stats['persentase_pendapatan'] }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: ANTREAN AKTIF -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">ANTREAN AKTIF</span>
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span id="stat-antrean-val" class="text-3xl font-black text-slate-900 tracking-tight font-mono">
                                {{ $stats['antrean_aktif'] }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">Antrean</span>
                        </div>

                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Est. Tunggu: ~<strong id="stat-est-tunggu" class="text-slate-700 font-mono">{{ $stats['est_tunggu'] }}</strong> menit</span>
                        </p>
                    </div>

                    <!-- Walk-in vs Online Breakdown Box -->
                    <div class="mt-3 bg-[#f0f6ff] rounded-xl px-3 py-2 flex items-center justify-between text-[11px] text-blue-900 font-semibold">
                        <span id="stat-antrean-walkin">{{ $stats['antrean_walk_in'] }} Walk-In Loket</span>
                        <span class="text-blue-300">•</span>
                        <span id="stat-antrean-online">{{ $stats['antrean_online'] }} Online / WA</span>
                    </div>
                </div>

                <!-- CARD 4: BAHAN KRITIS -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">BAHAN KRITIS</span>
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span id="stat-kritis-val" class="text-3xl font-black text-slate-900 tracking-tight font-mono">
                                {{ $stats['bahan_kritis_count'] }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">Item Menipis</span>
                        </div>

                        <div id="stat-kritis-label" class="mt-1 text-[11px] font-bold tracking-wide uppercase flex items-center gap-1.5">
                            @if($stats['bahan_kritis_count'] > 0)
                                <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                                <span class="text-rose-700">PERLU RESTOCK SEGERA</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-700">SEMUA STOK AMAN</span>
                            @endif
                        </div>
                    </div>

                    <!-- Items List Box -->
                    <div id="stat-kritis-box" class="mt-3 bg-slate-50 rounded-xl px-3 py-2 text-[11px] text-slate-600 min-h-[46px] flex flex-col justify-center">
                        @forelse($stats['bahan_kritis_items']->take(2) as $item)
                            <div class="truncate font-medium">• {{ $item->nama_bahan }} ({{ $item->jumlah_stok }} {{ $item->satuan }})</div>
                        @empty
                            <div class="text-slate-400 italic">Tidak ada bahan yang menipis saat ini.</div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- MIDDLE ROW: TWO COLUMNS (CHARTS & STATS)   -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" id="tren-omzet-section">
                
                <!-- LEFT COLUMN: TREN OMZET 7 HARI TERAKHIR (lg:col-span-8) -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    
                    <div>
                        <!-- Header with Toggle Pill -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                        Tren Omzet 7 Hari Terakhir
                                    </h3>
                                    <span class="text-[11px] font-mono text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200/60 font-semibold">
                                        {{ $stats['periode_7_hari'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Rata-rata pendapatan harian: <span id="chart-avg-text" class="font-bold text-slate-700">Rp {{ number_format($stats['rata_rata_omzet_harian'], 0, ',', '.') }}</span> / hari
                                </p>
                            </div>

                            <!-- Period Buttons (Harian / Mingguan) -->
                            <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold self-start sm:self-auto border border-slate-200/60">
                                <button type="button" class="px-3 py-1 rounded-lg bg-white shadow-2xs font-bold text-slate-800">
                                    Harian
                                </button>
                                <button type="button" onclick="showToast('Mode tampilan mingguan aktif')" class="px-3 py-1 rounded-lg text-slate-500 hover:text-slate-800">
                                    Mingguan
                                </button>
                            </div>
                        </div>

                        <!-- VISUAL LINE CHART (SVG) -->
                        <div class="relative w-full h-48 sm:h-56 mt-4 pt-2">
                            @php
                                $maxRev = max(100000, ...array_column($stats['tren_7_hari'], 'revenue'));
                                $points = [];
                                $svgWidth = 700;
                                $svgHeight = 160;
                                $count = count($stats['tren_7_hari']);
                                
                                foreach ($stats['tren_7_hari'] as $idx => $t) {
                                    $x = 40 + ($idx * (($svgWidth - 80) / max(1, $count - 1)));
                                    // Invert Y for SVG coordinates
                                    $normalized = $stats['pesanan_hari_ini'] > 0 ? ($t['revenue'] / $maxRev) : 0;
                                    $y = $svgHeight - 25 - ($normalized * ($svgHeight - 50));
                                    $points[] = ['x' => $x, 'y' => $y, 'data' => $t];
                                }

                                // Create smooth SVG path string
                                $pathD = '';
                                if (!empty($points)) {
                                    $pathD = 'M ' . $points[0]['x'] . ' ' . $points[0]['y'];
                                    for ($i = 0; $i < count($points) - 1; $i++) {
                                        $xMid = ($points[$i]['x'] + $points[$i+1]['x']) / 2;
                                        $yMid = ($points[$i]['y'] + $points[$i+1]['y']) / 2;
                                        $cpX1 = ($xMid + $points[$i]['x']) / 2;
                                        $cpX2 = ($xMid + $points[$i+1]['x']) / 2;
                                        $pathD .= ' Q ' . $points[$i]['x'] . ' ' . $points[$i]['y'] . ', ' . $xMid . ' ' . $yMid;
                                    }
                                    $lastPoint = end($points);
                                    $pathD .= ' L ' . $lastPoint['x'] . ' ' . $lastPoint['y'];
                                }
                                $areaD = $pathD . ' L ' . end($points)['x'] . ' ' . ($svgHeight - 10) . ' L ' . $points[0]['x'] . ' ' . ($svgHeight - 10) . ' Z';
                            @endphp

                            <svg class="w-full h-full overflow-visible" viewBox="0 0 700 160" preserveAspectRatio="none">
                                <defs>
                                    <!-- Golden Gradient for the Area -->
                                    <linearGradient id="omzetGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.25"/>
                                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>

                                <!-- Grid Lines -->
                                <line x1="40" y1="30" x2="660" y2="30" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4,4"/>
                                <line x1="40" y1="80" x2="660" y2="80" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4,4"/>
                                <line x1="40" y1="135" x2="660" y2="135" stroke="#e2e8f0" stroke-width="1"/>

                                @if($stats['pesanan_hari_ini'] > 0)
                                    <!-- Area Fill -->
                                    <path d="{{ $areaD }}" fill="url(#omzetGradient)" />

                                    <!-- Main Golden Curve -->
                                    <path d="{{ $pathD }}" fill="none" stroke="#d97706" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>

                                    <!-- Points on Curve -->
                                    @foreach($points as $p)
                                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4.5" fill="#ffffff" stroke="#d97706" stroke-width="2.5" class="hover:r-6 transition-all cursor-pointer">
                                            <title>{{ $p['data']['label'] }}: Rp {{ number_format($p['data']['revenue'], 0, ',', '.') }}</title>
                                        </circle>
                                    @endforeach
                                @else
                                    <!-- Empty State Baseline Curve -->
                                    <path d="M 40 135 L 660 135" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-dasharray="4,4"/>
                                    @foreach($points as $p)
                                        <circle cx="{{ $p['x'] }}" cy="135" r="3.5" fill="#ffffff" stroke="#94a3b8" stroke-width="2" />
                                    @endforeach
                                @endif
                            </svg>

                            <!-- Empty State Overlay message if 0 orders -->
                            <div id="chart-empty-overlay" class="absolute inset-0 flex items-center justify-center pointer-events-none {{ $stats['pesanan_hari_ini'] > 0 ? 'hidden' : '' }}">
                                <div class="bg-white/90 backdrop-blur-xs px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs text-center">
                                    <span class="text-xs font-semibold text-slate-500">Belum ada riwayat transaksi 7 hari terakhir</span>
                                    <p class="text-[10px] text-slate-400">Grafik omzet akan otomatis terbentuk seiring aktivitas transaksi kasir</p>
                                </div>
                            </div>
                        </div>

                        <!-- X-Axis Days Labels Row -->
                        <div class="grid grid-cols-7 text-center pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                            @foreach($stats['tren_7_hari'] as $day)
                                <div class="{{ $day['is_today'] ? 'font-black text-slate-900' : 'font-medium' }}">
                                    {{ $day['label'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Callout Info Banner at Bottom of Chart -->
                    <div class="mt-4 bg-[#f0f6ff] rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 text-blue-900">
                            <span class="text-amber-500 font-bold">🟧</span>
                            <span id="chart-peak-desc">
                                @if($stats['pesanan_hari_ini'] > 0)
                                    Puncak pesanan terjadi pukul 10:00 - 13:30 (Mahasiswa STAN & UIN)
                                @else
                                    Sistem siap mendeteksi periode puncak pesanan (Peak Hours) transaksi harian
                                @endif
                            </span>
                        </div>
                        <span id="chart-target-status" class="font-extrabold text-[#ea580c] flex-shrink-0">
                            Tercapai {{ $stats['persentase_pendapatan'] }}% Target
                        </span>
                    </div>

                </div>

                <!-- RIGHT COLUMN: METODE PEMBAYARAN & KONSUMSI BAHAN (lg:col-span-4) -->
                <div class="lg:col-span-4 space-y-4 flex flex-col justify-between">
                    
                    <!-- CARD A: METODE PEMBAYARAN -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm">Metode Pembayaran</h4>
                                    <p class="text-[11px] text-slate-400">Preferensi transaksi pelanggan hari ini</p>
                                </div>
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Donut Chart & Legend Row -->
                            <div class="flex items-center justify-between gap-4 mt-4">
                                
                                <!-- SVG Donut Chart -->
                                <div class="relative w-28 h-28 flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-28 h-28 -rotate-90 transform" viewBox="0 0 36 36">
                                        <!-- Background Ring -->
                                        <path class="text-slate-100" stroke-width="4.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        
                                        @if($stats['pembayaran']['total'] > 0)
                                            <!-- QRIS Segment (Amber/Orange) -->
                                            <path class="text-[#f59e0b]" stroke-dasharray="{{ $stats['pembayaran']['qris_percent'] }}, 100" stroke-width="4.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                            
                                            <!-- Tunai Segment (Blue) -->
                                            @php $tunaiOffset = 100 - $stats['pembayaran']['qris_percent']; @endphp
                                            <path class="text-blue-500" stroke-dasharray="{{ $stats['pembayaran']['tunai_percent'] }}, 100" stroke-dashoffset="-{{ $stats['pembayaran']['qris_percent'] }}" stroke-width="4.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        @endif
                                    </svg>
                                    
                                    <!-- Donut Center Label -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span id="donut-center-pct" class="text-base font-black text-slate-900 font-mono leading-none">
                                            {{ $stats['pembayaran']['total'] > 0 ? $stats['pembayaran']['qris_percent'].'%' : '0%' }}
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                            QRIS
                                        </span>
                                    </div>
                                </div>

                                <!-- Method Legend List -->
                                <div class="flex-1 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                            <span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span>
                                            <span>QRIS Dinamis</span>
                                        </div>
                                        <span id="pay-qris-val" class="font-extrabold text-slate-900 font-mono">
                                            Rp {{ number_format($stats['pembayaran']['qris'], 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span>Tunai Kasir</span>
                                        </div>
                                        <span id="pay-tunai-val" class="font-extrabold text-slate-900 font-mono">
                                            Rp {{ number_format($stats['pembayaran']['tunai'], 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                            <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                                            <span>Transfer / EDC</span>
                                        </div>
                                        <span id="pay-transfer-val" class="font-extrabold text-slate-900 font-mono">
                                            Rp {{ number_format($stats['pembayaran']['transfer'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- CARD B: KONSUMSI BAHAN HARI INI -->
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold tracking-wider uppercase text-slate-500">
                                KONSUMSI BAHAN HARI INI
                            </span>
                            <span class="text-[10px] font-bold font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                Shift Siang
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <!-- Mini Box 1: Kertas HVS A4 -->
                            <div class="bg-[#f0f6ff] rounded-xl p-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-white text-blue-600 flex items-center justify-center shadow-2xs flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <span id="konsumsi-kertas-val" class="text-sm font-black text-slate-900 font-mono block leading-tight">
                                        {{ $stats['konsumsi']['kertas_hvs_rim'] }} Rim
                                    </span>
                                    <span class="text-[11px] text-slate-500 font-medium block truncate">
                                        Kertas HVS A4
                                    </span>
                                </div>
                            </div>

                            <!-- Mini Box 2: Jilid Spiral/Mika -->
                            <div class="bg-[#f0f6ff] rounded-xl p-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-white text-blue-600 flex items-center justify-center shadow-2xs flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <span id="konsumsi-jilid-val" class="text-sm font-black text-slate-900 font-mono block leading-tight">
                                        {{ $stats['konsumsi']['jilid_buku'] }} Buku
                                    </span>
                                    <span class="text-[11px] text-slate-500 font-medium block truncate">
                                        Jilid Spiral/Mika
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- BOTTOM SECTION: ANTREAN BERJALAN DI GERAI  -->
            <!-- ========================================== -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden" id="antrean-section">
                
                <!-- Table Header Bar -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                Antrean Berjalan di Gerai
                            </h3>
                            <span id="queue-badge-title" class="bg-[#ea580c] text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full font-mono uppercase tracking-wide">
                                {{ $activeQueues->count() > 0 ? $activeQueues->count() . ' Prioritas Teratas' : '0 Antrean Berjalan' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Pesanan fisik yang sedang diproses dan menunggu giliran mesin cetak
                        </p>
                    </div>

                    <a href="{{ route('admin.antrean.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition self-start sm:self-auto">
                        <span>Kelola Semua Antrean (<span id="queue-total-link">{{ $stats['antrean_aktif'] }}</span>)</span>
                        <span>→</span>
                    </a>
                </div>

                <!-- TABLE CONTAINER -->
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs" id="queue-table">
                        <thead class="bg-[#f0f6ff]/70 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4">NO ANTREAN</th>
                                <th class="py-3 px-4">NO NOTA</th>
                                <th class="py-3 px-4">PELANGGAN</th>
                                <th class="py-3 px-4">DOKUMEN & SPESIFIKASI</th>
                                <th class="py-3 px-4">TENGGAT</th>
                                <th class="py-3 px-4">STATUS</th>
                                <th class="py-3 px-4 text-center">AKSI KASIR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="queue-table-body">
                            @forelse($activeQueues as $antrean)
                                @php
                                    $pesanan = $antrean->pesanan;
                                    $statusColor = $antrean->status_color;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition queue-row" data-search="{{ strtolower($antrean->kode_antrean . ' ' . ($pesanan?->nomor_nota ?? '') . ' ' . ($pesanan?->display_name ?? '')) }}">
                                    <!-- NO ANTREAN -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold font-mono text-xs border border-blue-100">
                                            {{ $antrean->kode_antrean }}
                                        </span>
                                    </td>

                                    <!-- NO NOTA -->
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                        {{ $pesanan?->nomor_nota ?? '#MF-'.str_pad($antrean->pesanan_id, 3, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- PELANGGAN -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 leading-tight">
                                            {{ $pesanan?->display_name ?? 'Pelanggan Walk-in' }}
                                        </div>
                                        <div class="text-[10px] font-medium text-slate-500 mt-0.5">
                                            {{ $pesanan?->display_tipe ?? 'Walk-in • Reguler' }}
                                        </div>
                                    </td>

                                    <!-- DOKUMEN & SPESIFIKASI -->
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="font-bold text-slate-800 truncate">
                                            {{ $pesanan?->display_judul ?? 'Dokumen Cetak' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate mt-0.5">
                                            {{ $pesanan?->display_spesifikasi ?? 'A4 70g • Standard' }}
                                        </div>
                                    </td>

                                    <!-- TENGGAT -->
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-rose-50 text-rose-600 border border-rose-100">
                                            {{ $pesanan?->display_tenggat ?? '14:50 WIB' }}
                                        </span>
                                    </td>

                                    <!-- STATUS -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $statusColor['bg'] }} {{ $statusColor['text'] }} border {{ $statusColor['border'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }}"></span>
                                            <span>{{ $antrean->status_label }}</span>
                                        </span>
                                    </td>

                                    <!-- AKSI KASIR -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- View Details Eye Button -->
                                            <button type="button" onclick="viewOrderDetails({{ $antrean->antrean_id }}, '{{ $antrean->kode_antrean }}', '{{ $pesanan?->nomor_nota }}', '{{ addslashes($pesanan?->display_name ?? '') }}', '{{ addslashes($pesanan?->display_judul ?? '') }}', '{{ addslashes($pesanan?->display_spesifikasi ?? '') }}', 'Rp {{ number_format($pesanan?->total_harga ?? 0, 0, ',', '.') }}', '{{ strtoupper($pesanan?->metode_pembayaran ?? 'QRIS') }}')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Lihat Detail Pesanan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            <!-- Status Advance Action Button -->
                                            @if($antrean->status_antrean === 'siap')
                                                <button type="button" onclick="updateQueueStatusAjax({{ $antrean->antrean_id }}, 'selesai')" class="px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                                                    Serahkan
                                                </button>
                                            @elseif($antrean->status_antrean === 'menunggu')
                                                <button type="button" onclick="updateQueueStatusAjax({{ $antrean->antrean_id }}, 'cetak')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                    Proses Mesin
                                                </button>
                                            @else
                                                <button type="button" onclick="updateQueueStatusAjax({{ $antrean->antrean_id }}, 'selesai')" class="px-3 py-1 bg-[#ea580c] hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                                                    Selesai
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-state-row">
                                    <td colspan="7" class="py-12 px-4 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
                                            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center shadow-xs">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-extrabold text-slate-800 text-sm">Belum Ada Antrean Pesanan Berjalan</h4>
                                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                                    Sistem siap menerima order baru. Data antrean akan terisi otomatis dan bertambah secara real-time saat pelanggan memesan via portal web atau kasir menginput pesanan walk-in.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-t border-slate-200 py-3.5 px-6 text-center text-xs text-slate-400 flex flex-wrap items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} Matahari Fotocopy & Printing Systems. All rights reserved.</span>
            <span class="flex items-center gap-1.5 font-mono text-[11px] text-emerald-600 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Real-Time Sync: AKTIF (5s Polling)</span>
            </span>
        </footer>

    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: INPUT PESANAN WALK-IN KASIR       -->
    <!-- ========================================== -->
    <div id="modal-walk-in" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Catat Pesanan Walk-in Baru</h3>
                        <p class="text-xs text-slate-400">Order akan langsung masuk antrean cetak realtime</p>
                    </div>
                </div>
                <button type="button" onclick="closeWalkInModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <form id="form-walk-in" onsubmit="submitWalkInOrder(event)" class="mt-5 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pelanggan / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pemesan" required placeholder="Contoh: Bagas Wicaksono / Kantor Notaris" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipe Pelanggan</label>
                        <select name="tipe_pelanggan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                            <option value="Walk-in • Reguler">Walk-in • Reguler</option>
                            <option value="⭐ Member Gold">⭐ Member Gold</option>
                            <option value="Walk-in • Mahasiswa">Walk-in • Mahasiswa</option>
                            <option value="💼 Instansi Dosen">💼 Instansi Dosen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Layanan Utama</label>
                        <select name="produk_id" id="walkin-produk-id" onchange="calculateWalkInPrice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                            @foreach($products as $p)
                                <option value="{{ $p->produk_id }}" data-price="{{ $p->harga_dasar }}">{{ $p->nama_item }} (Rp {{ number_format($p->harga_dasar) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Dokumen / Nama Pekerjaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul_dokumen" required placeholder="Contoh: Skripsi Bab 1-5 Final (3 Rangkap)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Spesifikasi Kertas & Finishing <span class="text-rose-500">*</span></label>
                    <input type="text" name="spesifikasi" required placeholder="Contoh: A4 80g • B/W Bolak-balik • Jilid Softcover" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jumlah Halaman / Kuantitas</label>
                        <input type="number" id="walkin-kuantitas" name="kuantitas" value="50" min="1" oninput="calculateWalkInPrice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Total Tagihan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" id="walkin-total-harga" name="total_harga" value="45000" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs font-mono font-bold text-amber-700 bg-amber-50/50">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                            <option value="qris">🟡 QRIS Dinamis</option>
                            <option value="tunai">🔵 Tunai Kasir</option>
                            <option value="transfer">🟣 Transfer Bank / EDC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Awal Antrean</label>
                        <select name="status_antrean" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 text-xs">
                            <option value="cetak">Mesin 01 (Cetak)</option>
                            <option value="salin">Mesin 02 (Salin)</option>
                            <option value="menunggu">Menunggu Mesin</option>
                            <option value="finishing">Meja Jilid (Finishing)</option>
                            <option value="siap">Siap di Rak Loket</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeWalkInModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-walkin" class="px-5 py-2 rounded-xl bg-[#f59e0b] hover:bg-[#ea580c] text-white font-bold transition shadow-xs">
                        Simpan & Masukkan Antrean
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: MANAJEMEN STOK INVENTARIS         -->
    <!-- ========================================== -->
    <div id="modal-stock" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Manajemen Stok Bahan Baku</h3>
                        <p class="text-xs text-slate-400">Atur ketersediaan kertas, tinta, dan bahan finishing</p>
                    </div>
                </div>
                <button type="button" onclick="closeStockModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <!-- Existing Stocks Quick View -->
            <div class="mt-4 space-y-2">
                <h4 class="font-bold text-slate-700 text-xs">Daftar Bahan Baku Saat Ini:</h4>
                <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden max-h-40 overflow-y-auto custom-scrollbar">
                    @foreach($inventoryItems as $item)
                        <div class="p-2.5 flex items-center justify-between text-xs bg-slate-50/50">
                            <div>
                                <span class="font-bold text-slate-800">{{ $item->nama_bahan }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">Batas Minimum: {{ $item->batas_minimum }} {{ $item->satuan }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold {{ $item->jumlah_stok <= $item->batas_minimum ? 'text-rose-600' : 'text-emerald-700' }}">
                                    {{ $item->jumlah_stok }} {{ $item->satuan }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <form id="form-stock" onsubmit="submitStock(event)" class="mt-5 space-y-3.5 text-xs pt-3 border-t border-slate-100">
                @csrf
                <h4 class="font-bold text-slate-800 text-xs">Tambah / Update Stok Bahan:</h4>
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Bahan Baku</label>
                    <select name="nama_bahan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-xs">
                        <option value="Kertas HVS A4 70gr">Kertas HVS A4 70gr</option>
                        <option value="Kertas HVS F4 70gr">Kertas HVS F4 70gr</option>
                        <option value="Tinta Konica Hitam">Tinta Konica Hitam</option>
                        <option value="Spiral Kawat Jilid">Spiral Kawat Jilid</option>
                        <option value="Lakban Hitam Jilid">Lakban Hitam Jilid</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jumlah Stok Fisik</label>
                        <input type="number" name="jumlah_stok" value="50" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Batas Minimum Kritis</label>
                        <input type="number" name="batas_minimum" value="5" min="1" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Satuan</label>
                    <select name="satuan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-xs">
                        <option value="Rim">Rim</option>
                        <option value="Botol">Botol (Btl)</option>
                        <option value="Pcs">Pcs / Lembar</option>
                        <option value="Roll">Roll</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeStockModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-xs">
                        Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: DETAIL NOTA PESANAN (PREVIEW)     -->
    <!-- ========================================== -->
    <div id="modal-order-details" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span id="modal-detail-antrean" class="text-xs font-mono font-bold bg-blue-50 text-blue-700 px-2 py-0.5 rounded">A-01</span>
                    <span id="modal-detail-nota" class="text-xs font-mono text-slate-500 font-bold ml-2">#MF-2410-081</span>
                </div>
                <button type="button" onclick="closeOrderDetails()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <div class="mt-4 space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Nama Pemesan:</span>
                    <span id="modal-detail-nama" class="font-bold text-slate-900 text-sm">Bagas Wicaksono</span>
                </div>

                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Pekerjaan / Dokumen:</span>
                    <span id="modal-detail-judul" class="font-bold text-slate-800">Skripsi Bab 1-5 Final (3 Rangkap)</span>
                </div>

                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Spesifikasi Cetak:</span>
                    <span id="modal-detail-spek" class="text-slate-600 bg-slate-50 p-2.5 rounded-xl block border border-slate-100 font-medium">A4 80g • B/W Bolak-balik • Jilid Softcover</span>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Metode Pembayaran:</span>
                        <span id="modal-detail-metode" class="font-mono font-bold text-slate-700">QRIS</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Total Harga:</span>
                        <span id="modal-detail-total" class="font-mono font-black text-amber-600 text-sm">Rp 85.000</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeOrderDetails()" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs transition">
                    Tutup Preview
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <!-- ========================================== -->
    <!-- REAL-TIME JAVASCRIPT LOGIC & POLLING       -->
    <!-- ========================================== -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Dynamic Live Clock
        function updateLiveClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dateStr = `${now.getDate()} ${months[now.getMonth()]}`;
            
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB • ${dateStr}`;
            }
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `px-4 py-2.5 rounded-xl shadow-lg border text-xs font-bold text-white transition-all transform duration-300 translate-y-2 opacity-0 pointer-events-auto flex items-center gap-2 ${
                type === 'success' ? 'bg-emerald-600 border-emerald-500' : 'bg-slate-800 border-slate-700'
            }`;
            toast.innerHTML = `<span>✓</span><span>${message}</span>`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Modal Controls
        function openWalkInModal() {
            document.getElementById('modal-walk-in').classList.remove('hidden');
        }
        function closeWalkInModal() {
            document.getElementById('modal-walk-in').classList.add('hidden');
        }
        function openStockModal() {
            document.getElementById('modal-stock').classList.remove('hidden');
        }
        function closeStockModal() {
            document.getElementById('modal-stock').classList.add('hidden');
        }
        function toggleNotificationDropdown() {
            const drop = document.getElementById('notif-dropdown');
            drop.classList.toggle('hidden');
        }

        // Order Details Modal
        function viewOrderDetails(id, antrean, nota, nama, judul, spek, total, metode) {
            document.getElementById('modal-detail-antrean').textContent = antrean;
            document.getElementById('modal-detail-nota').textContent = nota;
            document.getElementById('modal-detail-nama').textContent = nama;
            document.getElementById('modal-detail-judul').textContent = judul;
            document.getElementById('modal-detail-spek').textContent = spek;
            document.getElementById('modal-detail-total').textContent = total;
            document.getElementById('modal-detail-metode').textContent = metode;
            document.getElementById('modal-order-details').classList.remove('hidden');
        }
        function closeOrderDetails() {
            document.getElementById('modal-order-details').classList.add('hidden');
        }

        // Calculate Walk-in order price automatically
        function calculateWalkInPrice() {
            const select = document.getElementById('walkin-produk-id');
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price') || 300);
            const qty = parseInt(document.getElementById('walkin-kuantitas').value || 1);
            document.getElementById('walkin-total-harga').value = price * qty;
        }

        // Search Filter on Table
        function filterQueueTable() {
            const query = document.getElementById('global-search-input').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#queue-table-body tr.queue-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (searchData.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const emptyRow = document.getElementById('empty-state-row');
            if (emptyRow && rows.length > 0) {
                emptyRow.style.display = (visibleCount === 0 && query !== '') ? '' : 'none';
            }
        }

        // Submit Walk-in Order via AJAX
        async function submitWalkInOrder(event) {
            event.preventDefault();
            const form = document.getElementById('form-walk-in');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('admin.dashboard.walk-in') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.status === 'success') {
                    closeWalkInModal();
                    form.reset();
                    showToast(data.message);
                    fetchLiveData(); // Immediate refresh
                }
            } catch (err) {
                console.error(err);
                form.submit(); // fallback
            }
        }

        // Submit Stock Update via AJAX
        async function submitStock(event) {
            event.preventDefault();
            const form = document.getElementById('form-stock');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('admin.dashboard.stock') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.status === 'success') {
                    closeStockModal();
                    showToast(data.message);
                    fetchLiveData();
                }
            } catch (err) {
                console.error(err);
                form.submit();
            }
        }

        // Update Queue Status via AJAX
        async function updateQueueStatusAjax(antreanId, newStatus) {
            try {
                const response = await fetch(`/admin/dashboard/queue/${antreanId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    showToast(data.message);
                    fetchLiveData();
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Real-Time Polling Engine: every 5 seconds
        async function fetchLiveData() {
            try {
                const response = await fetch("{{ route('admin.dashboard.live-data') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) return;
                const data = await response.json();
                const stats = data.stats;

                // Update Metric KPI 1
                document.getElementById('stat-pesanan-count').textContent = stats.pesanan_hari_ini;
                document.getElementById('stat-pesanan-selesai').textContent = `${stats.pesanan_selesai} Selesai`;
                document.getElementById('stat-pesanan-proses').textContent = `${stats.pesanan_proses} Proses`;
                const trendEl = document.getElementById('stat-pesanan-trend');
                if (stats.pesanan_hari_ini > 0) {
                    trendEl.innerHTML = '<span class="text-emerald-600">↗ +14% vs kemarin</span>';
                } else {
                    trendEl.innerHTML = '<span class="text-slate-400">Belum ada pesanan hari ini</span>';
                }

                // Update Metric KPI 2
                document.getElementById('stat-pendapatan-val').textContent = new Intl.NumberFormat('id-ID').format(stats.pendapatan_shift);
                document.getElementById('stat-pendapatan-pct').textContent = `${stats.persentase_pendapatan}%`;
                document.getElementById('stat-pendapatan-bar').style.width = `${stats.persentase_pendapatan}%`;

                // Update Metric KPI 3
                document.getElementById('stat-antrean-val').textContent = stats.antrean_aktif;
                document.getElementById('stat-est-tunggu').textContent = stats.est_tunggu;
                document.getElementById('stat-antrean-walkin').textContent = `${stats.antrean_walk_in} Walk-In Loket`;
                document.getElementById('stat-antrean-online').textContent = `${stats.antrean_online} Online / WA`;
                document.getElementById('sidebar-queue-badge').textContent = stats.antrean_aktif;
                document.getElementById('queue-total-link').textContent = stats.antrean_aktif;

                // Update Metric KPI 4
                document.getElementById('stat-kritis-val').textContent = stats.bahan_kritis_count;
                const kritisLabel = document.getElementById('stat-kritis-label');
                const kritisBox = document.getElementById('stat-kritis-box');
                if (stats.bahan_kritis_count > 0) {
                    kritisLabel.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span><span class="text-rose-700">PERLU RESTOCK SEGERA</span>';
                    kritisBox.innerHTML = stats.bahan_kritis_items.slice(0, 2).map(i => `<div class="truncate font-medium">• ${i.nama_bahan} (${i.jumlah_stok} ${i.satuan})</div>`).join('');
                } else {
                    kritisLabel.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="text-emerald-700">SEMUA STOK AMAN</span>';
                    kritisBox.innerHTML = '<div class="text-slate-400 italic">Tidak ada bahan yang menipis saat ini.</div>';
                }

                // Update Payments & Donut
                document.getElementById('pay-qris-val').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(stats.pembayaran.qris)}`;
                document.getElementById('pay-tunai-val').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(stats.pembayaran.tunai)}`;
                document.getElementById('pay-transfer-val').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(stats.pembayaran.transfer)}`;
                document.getElementById('donut-center-pct').textContent = stats.pembayaran.total > 0 ? `${stats.pembayaran.qris_percent}%` : '0%';

                // Update Consumption
                document.getElementById('konsumsi-kertas-val').textContent = `${stats.konsumsi.kertas_hvs_rim} Rim`;
                document.getElementById('konsumsi-jilid-val').textContent = `${stats.konsumsi.jilid_buku} Buku`;

                // Update Chart Summary
                document.getElementById('chart-avg-text').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(stats.rata_rata_omzet_harian)}`;
                document.getElementById('chart-target-status').textContent = `Tercapai ${stats.persentase_pendapatan}% Target`;

                // Update Table Rows if needed
                renderQueueTable(data.activeQueues);

            } catch (err) {
                console.error("Live polling error:", err);
            }
        }

        // Render queue rows dynamically
        function renderQueueTable(queues) {
            const tbody = document.getElementById('queue-table-body');
            const badgeTitle = document.getElementById('queue-badge-title');

            if (!queues || queues.length === 0) {
                badgeTitle.textContent = '0 Antrean Berjalan';
                tbody.innerHTML = `
                    <tr id="empty-state-row">
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
                                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Belum Ada Antrean Pesanan Berjalan</h4>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Sistem siap menerima order baru. Data antrean akan terisi otomatis dan bertambah secara real-time saat pelanggan memesan via portal web atau kasir menginput pesanan walk-in.
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            badgeTitle.textContent = `${queues.length} Prioritas Teratas`;
            let rowsHtml = '';

            queues.forEach(q => {
                const color = q.status_color;
                let actionBtn = '';

                if (q.status_antrean === 'siap') {
                    actionBtn = `<button type="button" onclick="updateQueueStatusAjax(${q.antrean_id}, 'selesai')" class="px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition shadow-2xs">Serahkan</button>`;
                } else if (q.status_antrean === 'menunggu') {
                    actionBtn = `<button type="button" onclick="updateQueueStatusAjax(${q.antrean_id}, 'cetak')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">Proses Mesin</button>`;
                } else {
                    actionBtn = `<button type="button" onclick="updateQueueStatusAjax(${q.antrean_id}, 'selesai')" class="px-3 py-1 bg-[#ea580c] hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow-2xs">Selesai</button>`;
                }

                rowsHtml += `
                    <tr class="hover:bg-slate-50/70 transition queue-row" data-search="${(q.kode_antrean + ' ' + q.nomor_nota + ' ' + q.nama_pelanggan).toLowerCase()}">
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold font-mono text-xs border border-blue-100">
                                ${q.kode_antrean}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                            ${q.nomor_nota}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900 leading-tight">${q.nama_pelanggan}</div>
                            <div class="text-[10px] font-medium text-slate-500 mt-0.5">${q.tipe_pelanggan}</div>
                        </td>
                        <td class="py-3.5 px-4 max-w-xs">
                            <div class="font-bold text-slate-800 truncate">${q.judul_dokumen}</div>
                            <div class="text-[11px] text-slate-400 truncate mt-0.5">${q.spesifikasi}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-rose-50 text-rose-600 border border-rose-100">
                                ${q.tenggat}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold ${color.bg} ${color.text} border ${color.border}">
                                <span class="w-1.5 h-1.5 rounded-full ${color.dot}"></span>
                                <span>${q.status_label}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="viewOrderDetails(${q.antrean_id}, '${q.kode_antrean}', '${q.nomor_nota}', '${q.nama_pelanggan.replace(/'/g, "\\'")}', '${q.judul_dokumen.replace(/'/g, "\\'")}', '${q.spesifikasi.replace(/'/g, "\\'")}', 'Rp -', 'QRIS')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Lihat Detail">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                ${actionBtn}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
        }

        // Start live polling every 5000ms
        setInterval(fetchLiveData, 5000);
    </script>
</body>
</html>
