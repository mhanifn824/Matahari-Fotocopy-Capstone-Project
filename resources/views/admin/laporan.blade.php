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
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 md:px-8 py-3 flex items-center justify-end min-h-[60px]">
            @include('admin.partials.topbar-right')
        </header>

        <!-- SUBHEADER & TOP ACTIONS -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Title & Breadcrumb -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Laporan Penjualan &amp; Analitik Gerai
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Rekapitulasi pendapatan dan tren transaksi Bintaro
                    </p>
                </div>

                <!-- Top Right Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Ekspor Laporan Excel -->
                    <a href="{{ route('admin.laporan.export', request()->query()) }}" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-2xs transition active:scale-95">
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

        <!-- ============================================================ -->
        <!-- 1. FILTER TANGGAL (POSISI PALING ATAS)                        -->
        <!-- ============================================================ -->
        <div class="px-4 md:px-8 pb-5">
            <div class="bg-white px-5 py-3 sm:px-7 sm:py-3.5 rounded-2xl border border-slate-200/90 shadow-2xs">
                <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3.5">
                    
                    <!-- Quick Filter Buttons -->
                    <div class="flex items-center gap-2 overflow-x-auto shrink-0">
                        <a href="{{ route('admin.laporan.index') }}" class="h-9 px-4 inline-flex items-center justify-center rounded-xl text-xs font-bold transition whitespace-nowrap {{ $rentang === 'semua' || empty($rentang) ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                            Semua Transaksi
                        </a>
                        <a href="{{ route('admin.laporan.index', ['rentang' => 'hari_ini']) }}" class="h-9 px-4 inline-flex items-center justify-center rounded-xl text-xs font-bold transition whitespace-nowrap {{ $rentang === 'hari_ini' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                            Hari Ini
                        </a>
                        <a href="{{ route('admin.laporan.index', ['rentang' => '7_hari']) }}" class="h-9 px-4 inline-flex items-center justify-center rounded-xl text-xs font-bold transition whitespace-nowrap {{ $rentang === '7_hari' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
                            7 Hari Terakhir
                        </a>
                    </div>

                    <!-- Date Range Filter Form -->
                    <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex items-center gap-2.5 flex-nowrap shrink-0 overflow-x-auto sm:mr-3">
                        <span class="text-xs text-slate-500 font-bold whitespace-nowrap shrink-0">Rentang:</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <input 
                                type="date" 
                                name="tanggal_mulai" 
                                value="{{ $tanggalMulaiInput }}" 
                                class="h-9 px-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-mono text-slate-700 bg-slate-50/70 w-32 sm:w-36 shrink-0"
                                required
                            >
                            <span class="text-xs text-slate-400 font-semibold shrink-0">s/d</span>
                            <input 
                                type="date" 
                                name="tanggal_akhir" 
                                value="{{ $tanggalAkhirInput }}" 
                                class="h-9 px-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-mono text-slate-700 bg-slate-50/70 w-32 sm:w-36 shrink-0"
                                required
                            >
                        </div>

                        <button type="submit" class="h-9 px-4 inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-2xs shrink-0 whitespace-nowrap">
                            Terapkan
                        </button>

                        @if($rentang && $rentang !== 'semua')
                        <a href="{{ route('admin.laporan.index') }}" class="h-9 px-2 inline-flex items-center justify-center text-xs font-bold text-rose-600 hover:underline shrink-0 whitespace-nowrap">
                            Reset
                        </a>
                        @endif
                    </form>

                </div>

                <!-- Pemberitahuan jika tidak ada pesanan pada tanggal/periode yang dipilih -->
                @if($totalNotaPesanan === 0)
                <div class="mt-2.5 p-3 bg-amber-50 border border-amber-200/80 rounded-xl flex items-center gap-2.5 text-amber-800 text-xs font-medium">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tidak ada transaksi atau pembelian pada tanggal tersebut.</span>
                </div>
                @endif
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- 2. DUA KOLOM: TREN PENDAPATAN 7 HARI & KARTU STATISTIK VERTIKAL -->
        <!-- ============================================================ -->
        <div class="px-4 md:px-8 pb-5">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                
                <!-- Kolom Kiri: Tren Pendapatan 7 Hari (lg:col-span-8 untuk lebar ideal) -->
                <div class="lg:col-span-8 flex flex-col">
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs flex-1 flex flex-col justify-between">
                        <!-- Header Grafik -->
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Tren Pendapatan 7 Hari</h3>

                            </div>
                            <!-- Legend -->
                            <div class="flex items-center gap-2 text-[11px] sm:text-xs text-slate-600 font-semibold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200/60">
                                <span class="w-2.5 h-2.5 rounded bg-amber-500"></span>
                                <span>Omzet Harian</span>
                            </div>
                        </div>

                        <!-- Bar Chart Graphic Area -->
                        <div class="pt-4 pb-1 flex-1 flex flex-col justify-end">
                            <!-- Bar Tracks & Values -->
                            <div class="flex items-end justify-between gap-1 sm:gap-2 px-1">
                                @foreach($trend7Hari as $t)
                                <div class="flex-1 flex flex-col items-center justify-end group">
                                    <!-- Value tag above bar -->
                                    <div class="h-5 flex items-center justify-center mb-1">
                                        <span class="text-[10px] sm:text-[11px] font-bold font-mono {{ !empty($t['is_peak']) ? 'text-amber-600 font-black' : ($t['total'] > 0 ? 'text-slate-600' : 'text-slate-400') }} whitespace-nowrap">
                                            {{ $t['label'] }}
                                        </span>
                                    </div>
                                    
                                    <!-- Bar Track & Fill -->
                                    <div class="w-full max-w-[28px] sm:max-w-[34px] h-28 sm:h-32 bg-slate-100/70 rounded-t-lg flex items-end p-0.5 overflow-hidden">
                                        @if($t['total'] > 0)
                                        <div 
                                            class="w-full rounded-t-md transition-all duration-500 {{ !empty($t['is_peak']) ? 'bg-amber-500 shadow-sm' : 'bg-amber-400 group-hover:bg-amber-500' }}" 
                                            style="height: {{ max(10, $t['height']) }}%;"
                                        ></div>
                                        @else
                                        <div class="w-full h-1 bg-slate-200 rounded-t-sm"></div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Baseline Divider -->
                            <div class="border-b border-slate-200 my-1.5"></div>

                            <!-- Day & Orders count underneath baseline -->
                            <div class="flex items-start justify-between gap-1 sm:gap-2 px-1 pt-0.5">
                                @foreach($trend7Hari as $t)
                                <div class="flex-1 flex flex-col items-center text-center min-w-0">
                                    <span class="text-[9px] sm:text-[10px] xl:text-[11px] font-bold {{ !empty($t['is_peak']) ? 'text-slate-900 font-black' : 'text-slate-700' }} leading-tight block truncate max-w-full">
                                        {{ $t['hari_tanggal'] }}
                                    </span>
                                    <span class="text-[9px] sm:text-[10px] font-medium text-slate-400 font-mono mt-0.5 block truncate max-w-full">
                                        {{ $t['count'] }} pesanan
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Dua Kartu Statistik Disusun Vertikal (lg:col-span-4) -->
                <div class="lg:col-span-4 flex flex-col gap-4">
                    
                    <!-- Card 1 (Atas): TOTAL PESANAN -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs flex-1 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL PESANAN</span>
                            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2.5">
                            <div class="flex items-baseline">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">Rp {{ number_format($totalPesananOmzet, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium mt-1">
                                Rata-rata <strong class="text-slate-700 font-mono">Rp {{ number_format($rataRataOmzetPerHari, 0, ',', '.') }}</strong> / hari
                            </p>
                        </div>
                    </div>

                    <!-- Card 2 (Bawah): TOTAL NOTA PESANAN -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs flex-1 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL NOTA PESANAN</span>
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2.5">
                            <div class="flex items-baseline">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">{{ $totalNotaPesanan }}</span>
                                <span class="text-xs text-slate-500 font-bold ml-1.5">Nota</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium mt-1">
                                Rata-rata <strong class="text-slate-700 font-mono">{{ number_format($rataRataNotaPerHari, 1, ',', '.') }}</strong> nota / hari
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- ============================================================ -->
        <!-- 3. DATA TRANSAKSI TERAKHIR GERAI                             -->
        <!-- ============================================================ -->
        <div class="px-4 md:px-8 pb-10">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- Section Header -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Transaksi Terakhir Gerai</h3>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500 font-medium">
                        <span>Menampilkan <strong>{{ $orders->count() }}</strong> dari <strong>{{ $totalNotaPesanan }}</strong> nota</span>
                        <a href="{{ route('admin.laporan.transaksi') }}" class="font-bold text-amber-600 hover:text-amber-700 hover:underline flex items-center gap-1">
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
                                    {{ ($o->waktu_pesan ? $o->waktu_pesan->copy() : now())->timezone('Asia/Jakarta')->format('d M, H:i') }} WIB
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
                                    <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-2xs font-bold text-xl">
                                        📊
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm">Tidak ada transaksi atau pembelian pada tanggal tersebut</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Silakan ubah rentang tanggal atau pilih filter Semua Transaksi untuk melihat riwayat data lainnya.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
