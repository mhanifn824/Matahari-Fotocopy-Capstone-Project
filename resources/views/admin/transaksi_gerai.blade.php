<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi Terakhir Gerai - Matahari Fotocopy</title>
    
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
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 md:px-8 py-3 flex items-center justify-between gap-4 min-h-[60px]">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('admin.laporan.index') }}" class="hover:text-amber-600 transition">Laporan Omzet</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Transaksi Terakhir Gerai</span>
            </div>

            @include('admin.partials.topbar-right')
        </header>

        <!-- SUBHEADER & ACTIONS -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Transaksi Terakhir Gerai
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Daftar lengkap seluruh nota transaksi gerai dari Kasir Walk-In dan Pembelian Online
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Kembali ke Laporan -->
                    <a href="{{ route('admin.laporan.index') }}" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali ke Laporan Omzet</span>
                    </a>

                    <!-- Ekspor CSV -->
                    <a href="{{ route('admin.laporan.export', request()->query()) }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Ekspor Data (.csv)</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="px-4 md:px-8 pb-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Total Transaksi -->
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">TOTAL TRANSAKSI</span>
                    <div class="text-xl sm:text-2xl font-black text-slate-900 font-mono mt-0.5">{{ $totalCount }}</div>
                    <span class="text-[11px] text-slate-500 font-medium">Nota Transaksi</span>
                </div>

                <!-- Total Omzet -->
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">TOTAL OMZET</span>
                    <div class="text-xl sm:text-2xl font-black text-amber-600 font-mono mt-0.5">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-slate-500 font-medium">Akumulasi Pendapatan</span>
                </div>

                <!-- Kasir Walk-In -->
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">KASIR WALK-IN</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-600 font-mono mt-0.5">{{ $totalWalkIn }}</div>
                    <span class="text-[11px] text-slate-500 font-medium">Transaksi Gerai</span>
                </div>

                <!-- Pembelian Online -->
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider font-mono">PEMBELIAN ONLINE</span>
                    <div class="text-xl sm:text-2xl font-black text-blue-600 font-mono mt-0.5">{{ $totalOnline }}</div>
                    <span class="text-[11px] text-slate-500 font-medium">Order Printing &amp; ATK</span>
                </div>
            </div>
        </div>

        <!-- FILTER & SEARCH BAR -->
        <div class="px-4 md:px-8 pb-4">
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-2xs">
                <form method="GET" action="{{ route('admin.laporan.transaksi') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                    
                    <!-- Search input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $search }}" 
                            placeholder="Cari nomor nota, nama pelanggan, dokumen..." 
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium"
                        >
                    </div>

                    <!-- Jenis Filter Buttons -->
                    <div class="flex items-center gap-1.5 overflow-x-auto">
                        <a href="{{ route('admin.laporan.transaksi', array_merge(request()->except('jenis', 'page'), ['jenis' => 'semua'])) }}" class="px-3 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $jenis === 'semua' || empty($jenis) ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Tipe
                        </a>
                        <a href="{{ route('admin.laporan.transaksi', array_merge(request()->except('jenis', 'page'), ['jenis' => 'walk-in'])) }}" class="px-3 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $jenis === 'walk-in' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Kasir Walk-In
                        </a>
                        <a href="{{ route('admin.laporan.transaksi', array_merge(request()->except('jenis', 'page'), ['jenis' => 'online'])) }}" class="px-3 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $jenis === 'online' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Pembelian Online
                        </a>
                    </div>

                    <!-- Date range inputs -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            value="{{ $tanggalMulaiInput }}" 
                            class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-mono text-slate-700"
                        >
                        <span class="text-xs text-slate-400 font-medium">s/d</span>
                        <input 
                            type="date" 
                            name="tanggal_akhir" 
                            value="{{ $tanggalAkhirInput }}" 
                            class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-mono text-slate-700"
                        >

                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition">
                            Filter
                        </button>

                        @if(!empty($search) || ($jenis && $jenis !== 'semua') || !empty($tanggalMulaiInput))
                        <a href="{{ route('admin.laporan.transaksi') }}" class="text-xs text-rose-600 hover:underline font-bold px-2 py-1">
                            Reset
                        </a>
                        @endif
                    </div>

                </form>
            </div>
        </div>

        <!-- TRANSACTIONS TABLE -->
        <div class="px-4 md:px-8 pb-10">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                                <th class="py-3 px-4">NO. NOTA</th>
                                <th class="py-3 px-3">WAKTU &amp; TANGGAL</th>
                                <th class="py-3 px-3 text-center">TIPE TRANSAKSI</th>
                                <th class="py-3 px-3">PELANGGAN</th>
                                <th class="py-3 px-3">LAYANAN &amp; SPESIFIKASI</th>
                                <th class="py-3 px-3 text-center">METODE BAYAR</th>
                                <th class="py-3 px-3 text-right">TOTAL (RP)</th>
                                <th class="py-3 px-4">KASIR / OPERATOR</th>
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
                                    {{ ($o->waktu_pesan ? $o->waktu_pesan->copy() : now())->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </td>

                                <!-- Tipe Transaksi Badge -->
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @if($o->jenis_pesanan === 'walk-in')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>🚶</span>
                                        <span>WALK-IN</span>
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider font-mono bg-blue-50 text-blue-700 border border-blue-200">
                                        <span>🌐</span>
                                        <span>ONLINE</span>
                                    </span>
                                    @endif
                                </td>

                                <!-- Pelanggan -->
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $o->display_name }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">{{ $o->display_tipe }}</div>
                                </td>

                                <!-- Layanan & Spesifikasi -->
                                <td class="py-3.5 px-3 max-w-xs">
                                    <div class="font-extrabold text-slate-900 leading-snug truncate">
                                        {{ $o->display_judul }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                        {{ $o->display_spesifikasi }}
                                    </div>
                                </td>

                                <!-- Metode Bayar -->
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @php
                                        $m = strtolower((string)$o->metode_pembayaran);
                                    @endphp
                                    @if(str_contains($m, 'qris'))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-bold font-mono">
                                        <span>📲</span>
                                        <span>{{ strtoupper($o->metode_pembayaran) }}</span>
                                    </span>
                                    @elseif(str_contains($m, 'bca') || str_contains($m, 'debit') || str_contains($m, 'edc'))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold font-mono">
                                        <span>💳</span>
                                        <span>{{ strtoupper($o->metode_pembayaran) }}</span>
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold font-mono">
                                        <span>💵</span>
                                        <span>{{ strtoupper($o->metode_pembayaran ?: 'Tunai') }}</span>
                                    </span>
                                    @endif
                                </td>

                                <!-- Total (RP) -->
                                <td class="py-3.5 px-3 text-right whitespace-nowrap font-mono font-extrabold text-slate-900 text-xs">
                                    Rp {{ number_format($o->total_harga, 0, ',', '.') }}
                                </td>

                                <!-- Kasir -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 text-xs font-semibold">
                                    {{ $o->kasir_nama ?: 'Kasir Gerai' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 px-4 text-center">
                                    <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-2xs font-bold text-xl">
                                        📋
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Transaksi Ditemukan</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Tidak ada transaksi yang cocok dengan kriteria pencarian atau filter yang dipilih.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($orders->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                    <div>
                        Menampilkan <strong class="text-slate-800">{{ $orders->firstItem() }}</strong> sampai <strong class="text-slate-800">{{ $orders->lastItem() }}</strong> dari <strong class="text-slate-800">{{ $orders->total() }}</strong> nota
                    </div>
                    <div>
                        {{ $orders->links() }}
                    </div>
                </div>
                @endif

            </div>
        </div>

    </main>

    <!-- JAVASCRIPT -->
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

