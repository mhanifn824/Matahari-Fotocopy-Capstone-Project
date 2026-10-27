<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Penjualan & Analitik Gerai - Matahari Fotocopy</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
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
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc5fb',
                            400: '#36a6f6',
                            500: '#0c87eb',
                            600: '#0069c7',
                            700: '#0154a1',
                            800: '#064785',
                            900: '#0b3c6f',
                        },
                        amber: {
                            450: '#F59E0B',
                            500: '#F59E0B',
                            600: '#D97706',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .sidebar-bg {
            background: linear-gradient(180deg, #d3e6fe 0%, #bfdbfe 35%, #a3cdfe 70%, #96c7fe 100%);
        }
        .donut-chart {
            background: conic-gradient(
                #F59E0B 0% 48%,
                #3B82F6 48% 76%,
                #64748B 76% 94%,
                #D97706 94% 100%
            );
            border-radius: 50%;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900 bg-[#F4F7FC]">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'laporan', 'activeAntreanCount' => $activeAntreanCount])


    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA                          -->
    <!-- ========================================== -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#F4F7FC]">
        
        <!-- TOPBAR NAVIGATION -->
        <header class="bg-white/95 backdrop-blur border-b border-slate-200/80 px-4 md:px-8 py-3 sticky top-0 z-30 flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <!-- Search bar -->
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Cari no. antrean, nota, nama..." 
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition placeholder:text-slate-400 font-medium"
                >
            </div>

            <!-- Topbar Badges & Actions -->
            <div class="flex items-center gap-3 flex-wrap justify-end w-full sm:w-auto">
                <!-- Shift Capsule -->
                <div class="flex items-center gap-2 bg-white border border-slate-200/90 shadow-2xs px-3 py-1.5 rounded-full text-xs font-semibold text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <span>Buka • Shift Siang</span>
                </div>

                <!-- Live Clock Capsule -->
                <div class="flex items-center gap-2 bg-slate-100/80 border border-slate-200 px-3 py-1.5 rounded-full text-xs font-mono font-medium text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="liveClockDisplay">{{ date('H:i') }} WIB • {{ date('d M') }}</span>
                </div>

                <!-- Notification Bell -->
                <button class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" onclick="alert('Notifikasi: Laporan transaksi terupdate real-time.')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>

                <!-- Profile Circle -->
                <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xs ring-2 ring-amber-200">
                    FA
                </div>
            </div>
        </header>

        <!-- SUBHEADER & TOP ACTIONS -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Title & Breadcrumb -->
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">
                        ANALITIK POS • <span class="text-amber-500">CABANG BINTARO SEKTOR 3</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                        Laporan Penjualan &amp; Analitik Gerai
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Rekapitulasi pendapatan harian, performa layanan, volume cetak, dan tren transaksi Bintaro
                    </p>
                </div>

                <!-- Top Right Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Ekspor Laporan Excel -->
                    <a href="{{ route('admin.laporan.export') }}" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Ekspor Laporan Excel (.xlsx)</span>
                    </a>

                    <!-- Unduh Rekap PDF (Amber Button) -->
                    <button type="button" onclick="window.print()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Rekap PDF</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- FILTER BAR & SHIFT INFO                    -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-2.5 rounded-2xl border border-slate-200/80 shadow-2xs">
                
                <!-- Filter Pills -->
                <div class="flex items-center gap-1.5 flex-wrap w-full sm:w-auto">
                    <a href="{{ route('admin.laporan.index', ['rentang' => 'hari_ini']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $rentang === 'hari_ini' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('admin.laporan.index', ['rentang' => '7_hari']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $rentang === '7_hari' ? 'bg-white border border-slate-300 text-slate-900 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
                        7 Hari Terakhir
                    </a>
                    <a href="{{ route('admin.laporan.index', ['rentang' => 'bulan_ini']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $rentang === 'bulan_ini' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Bulan Ini (Okt 2024)
                    </a>
                    <a href="{{ route('admin.laporan.index', ['rentang' => 'kustom']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $rentang === 'kustom' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Kustom Rentang 📅
                    </a>
                </div>

                <!-- Shift Badge & Refresh Button -->
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-700">
                        <span>👤</span>
                        <span>Shift: <strong>Shift Siang + Feri (14:00 - 21:00)</strong></span>
                    </div>
                    <button type="button" onclick="window.location.reload()" class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition" title="Refresh Laporan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 4 KPI SUMMARY METRIC CARDS                 -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: TOTAL OMZET 7 HARI -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL OMZET 7 HARI</span>
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">Rp {{ number_format($totalOmzet7Hari, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-blue-600 font-bold mt-1 flex items-center gap-1">
                            <span>📈 +14.2%</span>
                            <span class="text-slate-400 font-normal">vs. minggu lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: TOTAL NOTA PESANAN -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL NOTA PESANAN</span>
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $totalNotaPesanan }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Nota</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            Rata-rata 26 nota / hari • <span class="text-emerald-600 font-bold">Selesai 99.4%</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: VOLUME KERTAS TERCETAK -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">VOLUME KERTAS TERCETAK</span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ number_format($volumeKertas, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Lembar</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-1 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>A4 (72%) • F4/A3 (28%)</span>
                        </p>
                    </div>
                </div>

                <!-- Card 4: RATA-RATA TRANSAKSI -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">RATA-RATA TRANSAKSI</span>
                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            <span class="font-bold text-slate-700">Tinggi: Rp 310.000</span> per nota cetak
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- MID-SECTION: CHARTS (2 COLUMNS)            -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                
                <!-- COLUMN 1: TREN PENDAPATAN 7 HARI (BAR CHART) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">Tren Pendapatan 7 Hari</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Fluktuasi omzet harian 18 - 24 Oktober 2024</p>
                            </div>
                            <!-- Legend -->
                            <div class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold">
                                <span class="w-2.5 h-2.5 rounded bg-amber-500"></span>
                                <span>Omzet Harian</span>
                            </div>
                        </div>

                        <!-- Bar Chart Graphic Area -->
                        <div class="pt-6 pb-2">
                            <div class="h-44 flex items-end justify-between gap-2 px-2 border-b border-slate-100">
                                @foreach($trend7Hari as $t)
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <!-- Value tag -->
                                    <span class="text-[10px] font-bold font-mono {{ !empty($t['is_peak']) ? 'text-amber-600 font-black' : 'text-slate-400' }}">
                                        {{ $t['label'] }}
                                    </span>
                                    <!-- Bar Container -->
                                    <div class="w-full max-w-[36px] bg-slate-50 rounded-t-xl h-full flex items-end overflow-hidden p-0.5">
                                        <div 
                                            class="w-full rounded-t-lg transition-all duration-500 {{ !empty($t['is_peak']) ? 'bg-amber-500 shadow-sm' : 'bg-amber-400/80 group-hover:bg-amber-500' }}" 
                                            style="height: {{ $t['height'] }}%;"
                                        ></div>
                                    </div>
                                    <!-- Day label -->
                                    <span class="text-[10px] font-bold {{ !empty($t['is_peak']) ? 'text-slate-900 font-black' : 'text-slate-500' }}">
                                        {{ $t['hari'] }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Highlight Peak Day Banner -->
                    <div class="mt-4 p-3 rounded-2xl bg-blue-50/70 border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 text-blue-900 font-bold">
                            <span class="text-blue-600">✓</span>
                            <span>Hari Paling Produktif: Rabu (Tugas Akhir Kampus UPJ &amp; STAN)</span>
                        </div>
                        <div class="font-mono font-black text-blue-700 whitespace-nowrap">
                            Rp 1.650.000 / 38 Nota
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: KOMPOSISI LAYANAN (DONUT CHART) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">Komposisi Layanan</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Proporsi omzet menurut jenis pekerjaan</p>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-400">Total 100%</span>
                        </div>

                        <!-- Donut Graphic & Legend Breakdown -->
                        <div class="pt-6 pb-2 flex flex-col sm:flex-row items-center justify-center sm:justify-around gap-6">
                            
                            <!-- Visual Donut (CSS Conic Gradient) -->
                            <div class="relative w-36 h-36 flex-shrink-0">
                                <div class="w-full h-full donut-chart flex items-center justify-center shadow-xs">
                                    <!-- Inner White Hole -->
                                    <div class="w-24 h-24 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
                                        <span class="text-base font-black text-slate-900 font-mono leading-none">48%</span>
                                        <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mt-0.5">CETAK B/W</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Legend List -->
                            <div class="space-y-2.5 text-xs w-full sm:w-auto">
                                @foreach($serviceBreakdown as $sb)
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $sb['color'] }};"></span>
                                        <span class="text-slate-700 font-bold text-xs">{{ $sb['nama'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 font-mono text-[11px] whitespace-nowrap">
                                        <span class="font-extrabold text-slate-900">{{ $sb['persen'] }}%</span>
                                        <span class="text-slate-400 font-normal">({{ $sb['label_nilai'] }})</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <!-- Bottom Machine Capacity -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                        <span>Kapasitas Produksi Mesin</span>
                        <span class="font-mono font-bold text-blue-600">78.4% Utilisasi Optimal</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- HEATMAP: ANALITIK WAKTU / JAM SIBUK        -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-5">
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-2xs font-bold text-base">
                            🕒
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-extrabold text-slate-900">Analitik Waktu: Jam Sibuk Operasional Gerai</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wider bg-amber-500 text-white font-mono uppercase">
                                    High Load Notice
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Lonjakan antrean fisik teratur terjadi pada 2 rentang waktu: <strong>13.00 – 15.00 WIB</strong> (Revisi Dosen Siang) dan <strong>19.00 – 21.00 WIB</strong> (Mahasiswa PKN STAN &amp; UPJ cetak tugas malam).
                            </p>
                        </div>
                    </div>

                    <!-- Recommendation Card -->
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 flex items-center gap-3 flex-shrink-0">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase font-mono block">REKOMENDASI KASIR</span>
                            <span class="text-xs font-extrabold text-slate-800">Buka Loket B cadangan</span>
                        </div>
                        <button type="button" onclick="alert('Permintaan alokasi staf bantuan loket B telah dikirim.')" class="bg-white hover:bg-slate-100 border border-slate-300 text-slate-800 text-xs font-bold px-3 py-1.5 rounded-xl transition shadow-2xs active:scale-95">
                            Alokasikan Staf
                        </button>
                    </div>
                </div>

                <!-- Hourly Blocks Visual Strip -->
                <div class="pt-2">
                    <div class="grid grid-cols-6 sm:grid-cols-12 gap-2 text-center">
                        @foreach($hourlyDistribution as $h)
                        <div class="flex flex-col items-center gap-1.5">
                            <div class="w-full h-10 rounded-xl flex items-center justify-center font-mono font-bold text-xs transition {{ !empty($h['is_peak']) ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                {{ !empty($h['is_peak']) ? '🔥' : '•' }}
                            </div>
                            <span class="text-[10px] font-mono font-semibold {{ !empty($h['is_peak']) ? 'text-slate-900 font-black' : 'text-slate-400' }}">
                                {{ $h['jam'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- BOTTOM SECTION: TRANSAKSI TERAKHIR GERAI   -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-10">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- Section Header -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Transaksi Terakhir Gerai</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Log pesanan walk-in real-time loket Bintaro</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500 font-medium">
                        <span>Menampilkan <strong>{{ $orders->count() }}</strong> dari <strong>{{ $totalNotaPesanan }}</strong> nota</span>
                        <a href="{{ route('admin.walkin.index') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                            <span>Lihat Semua</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                                <th class="py-3 px-4">NO. NOTA</th>
                                <th class="py-3 px-3">WAKTU &amp; TANGGAL</th>
                                <th class="py-3 px-3">PELANGGAN</th>
                                <th class="py-3 px-3">LAYANAN &amp; SPESIFIKASI</th>
                                <th class="py-3 px-3">METODE BAYAR</th>
                                <th class="py-3 px-3">TOTAL (RP)</th>
                                <th class="py-3 px-4">KASIR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($orders as $o)
                            <tr class="hover:bg-slate-50/60 transition group">
                                <!-- No. Nota -->
                                <td class="py-3.5 px-4 font-mono font-extrabold text-blue-600 whitespace-nowrap">
                                    {{ $o->nomor_nota }}
                                </td>

                                <!-- Waktu & Tanggal -->
                                <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap font-mono text-[11px]">
                                    {{ $o->waktu_pesan ? $o->waktu_pesan->format('d M, H:i') : now()->format('d M, H:i') }} WIB
                                </td>

                                <!-- Pelanggan -->
                                <td class="py-3.5 px-3 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $o->display_name }}
                                </td>

                                <!-- Layanan & Spesifikasi -->
                                <td class="py-3.5 px-3">
                                    <div class="font-extrabold text-slate-900 leading-snug">
                                        {{ $o->display_judul }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $o->display_spesifikasi }}
                                    </div>
                                </td>

                                <!-- Metode Bayar -->
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @php
                                        $m = strtolower((string)$o->metode_pembayaran);
                                    @endphp
                                    @if(str_contains($m, 'qris'))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-bold font-mono">
                                        <span>📲</span>
                                        <span>{{ $o->metode_pembayaran }}</span>
                                    </span>
                                    @elseif(str_contains($m, 'bca') || str_contains($m, 'debit'))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold font-mono">
                                        <span>💳</span>
                                        <span>{{ $o->metode_pembayaran }}</span>
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold font-mono">
                                        <span>💵</span>
                                        <span>{{ $o->metode_pembayaran ?: 'Tunai' }}</span>
                                    </span>
                                    @endif
                                </td>

                                <!-- Total (RP) -->
                                <td class="py-3.5 px-3 whitespace-nowrap font-mono font-extrabold text-slate-900 text-xs">
                                    Rp {{ number_format($o->total_harga, 0, ',', '.') }}
                                </td>

                                <!-- Kasir -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 text-xs font-semibold">
                                    {{ $o->kasir_nama ?: 'Feri Anto' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-2xs font-bold text-xl">
                                        📊
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm">Belum Ada Riwayat Transaksi Omzet</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Semua transaksi masuk dari kasir walk-in maupun pesanan online akan otomatis terakumulasi di sini secara real-time.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer with Shift Omzet & Pagination -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                    <div>
                        Halaman 1 dari 37 • Total omzet shift saat ini: <strong class="text-slate-900 font-mono">Rp {{ number_format($totalShiftSaatIni, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC                           -->
    <!-- ========================================== -->
    <script>
        // Live Clock
        setInterval(() => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            document.getElementById('liveClockDisplay').textContent = `${hours}:${minutes} WIB • ${now.getDate()} ${months[now.getMonth()]}`;
        }, 1000);
    </script>
</body>
</html>
