<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Member & Program Hadiah Kunjungan - Matahari Fotocopy</title>
    
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
        .font-mono-numbers {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbars */
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
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900 bg-[#F4F7FC]">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'member', 'activeAntreanCount' => $activeAntreanCount])


    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA                          -->
    <!-- ========================================== -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#F4F7FC]">
        
        <!-- TOPBAR NAVIGATION -->
        <header class="bg-white/95 backdrop-blur border-b border-slate-200/80 px-4 md:px-8 py-3 sticky top-0 z-30 flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <!-- Search bar -->
            <form action="{{ route('admin.member.index') }}" method="GET" class="relative w-full sm:w-96">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari no. antrean, nota, nama..." 
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition placeholder:text-slate-400 font-medium"
                >
            </form>

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
                <button class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" onclick="alert('Notifikasi: Program Hadiah Kunjungan aktif dengan 14 member siap klaim hari ini.')">
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

        <!-- Flash Alert -->
        @if(session('success'))
        <div id="flashAlert" class="mx-4 md:mx-8 mt-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flashAlert').remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">✕</button>
        </div>
        @endif

        <!-- SUBHEADER & TOP ACTIONS -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Title & Breadcrumb -->
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">
                        ADMIN POS GERAI &gt; <span class="text-amber-500">LOYALITAS & PELANGGAN</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                        Data Member & Program Hadiah Kunjungan
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1 max-w-3xl leading-relaxed">
                        Kelola database pelanggan setia, frekuensi kunjungan cetak fisik/online, akumulasi saldo poin, tingkatan loyalty tier, dan verifikasi klaim reward langsung di loket kasir Bintaro Sektor 3.
                    </p>
                </div>

                <!-- Top Right Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Atur Program Hadiah -->
                    <button type="button" onclick="openModalProgramHadiah()" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <span>Atur Program Hadiah</span>
                    </button>

                    <!-- + Daftarkan Member Baru (Amber Button) -->
                    <button type="button" onclick="openModalMemberBaru()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>+ Daftarkan Member Baru</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 4 KPI SUMMARY METRIC CARDS                 -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: TOTAL MEMBER AKTIF -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL MEMBER AKTIF</span>
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ $totalMemberAktif }}</span>
                        </div>
                        <p class="text-[11px] text-amber-600 font-semibold mt-1 flex items-center gap-1">
                            <span class="font-bold">📈 +18</span>
                            <span>member terdaftar bulan ini</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: HADIAH TERKLAIM -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">HADIAH TERKLAIM</span>
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ $hadiahTerklaim }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-semibold mt-1 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>Bulan berjalan (Target tercapai 112%)</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: MEMBER PALING LOYAL -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">MEMBER PALING LOYAL</span>
                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ $memberPalingLoyal }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-semibold mt-1">
                            <span class="text-blue-600 font-bold">Tier Emas &amp; VIP</span> • Frekuensi &gt; 10x cetak
                        </p>
                    </div>
                </div>

                <!-- Card 4: TOTAL POIN BEREDAR -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL POIN BEREDAR</span>
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ number_format($totalPoinBeredar, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-semibold mt-1">
                            <strong class="text-slate-700">Rp 1.485.000</strong> estimasi cadangan nilai reward
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- PROGRAM HADIAH STAMP KUNJUNGAN BANNER      -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-5">
            <div class="bg-[#FFFDF7] rounded-3xl p-5 border border-amber-200/80 shadow-xs space-y-4">
                
                <!-- Banner Title & Info -->
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-xs font-bold text-sm">
                        ★
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm font-extrabold text-slate-900">
                                Program Hadiah Stamp Kunjungan &amp; Poin Cetak
                            </h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                Aktif • Versi Bintaro 2024
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 font-medium mt-0.5 max-w-4xl">
                            Tiap transaksi di loket kasir / kirim WA otomatis tercatat <strong>1 Kunjungan Cetak + Poin (Rp 1.000 = 1 Poin)</strong>. Member berhak klaim hadiah instan di kasir saat mencapai target kunjungan.
                        </p>
                    </div>
                </div>

                <!-- 4 Tier Milestone Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 pt-1">
                    
                    <!-- Tier 1: Pemula -->
                    <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">Tier 1 • Pemula</span>
                                <span class="font-extrabold text-slate-900 text-xs">3x Kunjungan</span>
                            </div>
                            <div class="flex items-start gap-2.5 mt-2.5">
                                <span class="text-lg">📁</span>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900 leading-snug">Gratis Jilid Lakban Mika</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Atau 1 Map Folio Bening Dokumen</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium pt-3 mt-2 border-t border-slate-100">
                            <span>Stok Hadiah: 48 pcs</span>
                            <span class="text-amber-600 font-bold">Instan di Kasir</span>
                        </div>
                    </div>

                    <!-- Tier 2: Reguler -->
                    <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">Tier 2 • Reguler</span>
                                <span class="font-extrabold text-slate-900 text-xs">5x Kunjungan</span>
                            </div>
                            <div class="flex items-start gap-2.5 mt-2.5">
                                <span class="text-lg">🎨</span>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900 leading-snug">Gratis 5 Halaman Warna</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Atau Diskon 15% Cetak Poster/Stiker</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium pt-3 mt-2 border-t border-slate-100">
                            <span>Stok Hadiah: Unlimited</span>
                            <span class="text-amber-600 font-bold">Auto-potong Nota</span>
                        </div>
                    </div>

                    <!-- Tier 3: Emas (Favorit) -->
                    <div class="bg-white rounded-2xl p-3.5 border-2 border-amber-300 shadow-xs flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute -right-6 top-1 bg-amber-400 text-amber-950 font-black text-[9px] px-6 py-0.5 rotate-12 uppercase tracking-widest shadow-2xs">
                            Favorit
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-xs pr-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 font-mono">Tier 3 • Emas</span>
                                <span class="font-black text-amber-600 text-xs">10x Kunjungan</span>
                            </div>
                            <div class="flex items-start gap-2.5 mt-2.5">
                                <span class="text-lg">🎁</span>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900 leading-snug">Voucher Rp 25.000 + Pulpen</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Opsi: Gratis Hardcover Skripsi 1 Eks</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium pt-3 mt-2 border-t border-amber-100">
                            <span class="text-slate-600 font-semibold">Pilihan Utama STAN/UPJ</span>
                            <span class="text-rose-600 font-extrabold">Paling Populer</span>
                        </div>
                    </div>

                    <!-- Tier 4: VIP + Matahari Club -->
                    <div class="bg-white rounded-2xl p-3.5 border border-blue-200 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 font-mono">Tier VIP • Matahari Club</span>
                                <span class="font-black text-blue-600 text-xs">20x Kunjungan</span>
                            </div>
                            <div class="flex items-start gap-2.5 mt-2.5">
                                <span class="text-lg">💾</span>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900 leading-snug">Flashdisk Sandisk 32GB</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Atau Saldo Nota Tunai Rp 50.000</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium pt-3 mt-2 border-t border-slate-100">
                            <span>Stok Flashdisk: 12 pcs</span>
                            <span class="text-blue-600 font-bold">Reward Premium</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- MAIN CONTENT: TABLE & SIDE LOG/SCANNER     -->
        <!-- ========================================== -->
        <div class="px-4 md:px-8 pb-10">
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <!-- LEFT 2/3 COLUMN: MEMBER TABLE & TABS -->
                <div class="w-full lg:w-2/3 space-y-4">
                    
                    <!-- Tabs Navigation Bar -->
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2 flex-wrap gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <!-- Tab 1: Semua Member -->
                            <a href="{{ route('admin.member.index', ['tab' => 'semua', 'q' => $search, 'sort' => $sort]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $tab === 'semua' ? 'bg-white text-slate-900 shadow-2xs border border-slate-200' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>Semua Member</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'semua' ? 'bg-slate-100 text-slate-700' : 'bg-slate-200/60 text-slate-500' }} font-mono">({{ $countSemua }})</span>
                            </a>

                            <!-- Tab 2: Member Umum -->
                            <a href="{{ route('admin.member.index', ['tab' => 'umum', 'q' => $search, 'sort' => $sort]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $tab === 'umum' ? 'bg-white text-slate-900 shadow-2xs border border-slate-200' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>Member Umum</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'umum' ? 'bg-slate-100 text-slate-700' : 'bg-slate-200/60 text-slate-500' }} font-mono">({{ $countUmum }})</span>
                            </a>

                            <!-- Tab 5: Siap Klaim (Amber) -->
                            <a href="{{ route('admin.member.index', ['tab' => 'siap_klaim', 'q' => $search, 'sort' => $sort]) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $tab === 'siap_klaim' ? 'bg-amber-100/80 text-amber-900 border border-amber-300 font-extrabold' : 'bg-amber-50/60 text-amber-700 hover:bg-amber-100' }}">
                                <span>Siap Klaim</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-400 text-white font-mono font-black">({{ $countSiapKlaim }})</span>
                            </a>
                        </div>

                        <!-- Sinkronisasi Kasir Aktif indicator -->
                        <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Sinkronisasi Kasir Aktif</span>
                        </div>
                    </div>

                    <!-- Search and Sort Bar -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-slate-200/80 shadow-2xs">
                        <!-- Search input -->
                        <div class="relative w-full sm:w-80">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="tableSearchInput"
                                value="{{ $search }}"
                                placeholder="Cari nama pelanggan, nomor WhatsApp, atau ID Member (MTH-...)" 
                                class="w-full pl-9 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium"
                                onkeyup="if(event.key === 'Enter') { window.location.href = `{{ route('admin.member.index') }}?tab={{ $tab }}&sort={{ $sort }}&q=` + encodeURIComponent(this.value); }"
                            >
                        </div>

                        <!-- Sort dropdown & Export Button -->
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <select 
                                onchange="window.location.href = `{{ route('admin.member.index') }}?tab={{ $tab }}&q={{ $search }}&sort=` + this.value"
                                class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20"
                            >
                                <option value="kunjungan_desc" {{ $sort === 'kunjungan_desc' ? 'selected' : '' }}>Urut: Frekuensi Kunjungan Tertinggi</option>
                                <option value="poin_desc" {{ $sort === 'poin_desc' ? 'selected' : '' }}>Urut: Poin Tertinggi</option>
                                <option value="belanja_desc" {{ $sort === 'belanja_desc' ? 'selected' : '' }}>Urut: Akumulasi Belanja</option>
                                <option value="id_asc" {{ $sort === 'id_asc' ? 'selected' : '' }}>Urut: ID Member Terlama</option>
                            </select>

                            <button type="button" onclick="alert('Data member diekspor ke format CSV/Excel.')" class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition" title="Export Member CSV">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Member Table Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                                        <th class="py-3 px-4">ID &amp; NAMA MEMBER</th>
                                        <th class="py-3 px-3">KONTAK WHATSAPP</th>
                                        <th class="py-3 px-3">KUNJUNGAN &amp; PROGRESS</th>
                                        <th class="py-3 px-3">POIN</th>
                                        <th class="py-3 px-3">AKUMULASI BELANJA</th>
                                        <th class="py-3 px-3">STATUS REWARD</th>
                                        <th class="py-3 px-4 text-center">AKSI KASIR</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                    @forelse($members as $m)
                                    @php
                                        $prog = $m->progress_info;
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition group">
                                        <!-- ID & Nama Member -->
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-extrabold flex items-center justify-center text-xs flex-shrink-0 group-hover:bg-amber-100 group-hover:text-amber-800 transition">
                                                    {{ $m->initials }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <h4 class="font-extrabold text-slate-900 leading-tight">{{ $m->user?->nama ?? 'Member Gerai' }}</h4>
                                                        @if($m->badge_label)
                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold tracking-wide uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                            {{ $m->badge_label }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                                        <span class="font-bold text-slate-500">{{ $m->display_kode_member }}</span> • {{ $m->instansi_detail }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Kontak Whatsapp -->
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $m->display_whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 text-slate-600 hover:text-emerald-600 font-mono text-[11px] font-bold group-hover:underline">
                                                <span>💬</span>
                                                <span>{{ $m->display_whatsapp }}</span>
                                            </a>
                                        </td>

                                        <!-- Kunjungan & Progress -->
                                        <td class="py-3 px-3 min-w-[170px]">
                                            <div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="font-extrabold text-slate-900">{{ $prog['label'] }}</span>
                                                    <span class="text-[10px] font-bold {{ $prog['percent'] >= 100 ? 'text-amber-600 font-mono' : 'text-slate-400 font-mono' }}">{{ $prog['target_badge'] }}</span>
                                                </div>
                                                <!-- Progress Bar -->
                                                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $prog['percent'] >= 100 ? 'bg-amber-500' : 'bg-amber-400' }}" style="width: {{ $prog['percent'] }}%"></div>
                                                </div>
                                                <p class="text-[10px] text-slate-400 mt-1 font-medium truncate max-w-[180px]">
                                                    {{ $prog['sub'] }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Poin -->
                                        <td class="py-3 px-3 whitespace-nowrap font-mono">
                                            <span class="font-bold text-slate-900 text-xs">{{ number_format($m->saldo_poin, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-slate-400 font-sans">pts</span>
                                        </td>

                                        <!-- Akumulasi Belanja -->
                                        <td class="py-3 px-3 whitespace-nowrap font-mono font-bold text-slate-800 text-xs">
                                            Rp {{ number_format($m->total_belanja, 0, ',', '.') }}
                                        </td>

                                        <!-- Status Reward -->
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            @if($prog['is_claimable'])
                                                @if($prog['claim_type'] === 'vip')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                                                    <span>🎁</span>
                                                    <span>Siap: {{ $prog['reward_name'] }}</span>
                                                </span>
                                                @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                                    <span>⭐</span>
                                                    <span>Siap Klaim: {{ $prog['reward_name'] }}</span>
                                                </span>
                                                @endif
                                            @elseif($m->hadiah_terklaim_count > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                                    <span>✓</span>
                                                    <span>Hadiah Terklaim</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 text-[10px] font-medium">
                                                    <span>Dalam Progres</span>
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi Kasir -->
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($prog['is_claimable'])
                                                @if($prog['claim_type'] === 'vip')
                                                <button type="button" onclick="openModalKlaim('{{ $m->pelanggan_id }}', '{{ addslashes($m->user?->nama) }}', 'Flashdisk Sandisk 32GB', 'Milestone 20x VIP')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] px-3 py-1.5 rounded-xl shadow-xs transition active:scale-95">
                                                    Klaim VIP
                                                </button>
                                                @else
                                                <button type="button" onclick="openModalKlaim('{{ $m->pelanggan_id }}', '{{ addslashes($m->user?->nama) }}', '{{ addslashes($prog['reward_name']) }}', 'Milestone Kunjungan')" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] px-3 py-1.5 rounded-xl shadow-xs transition active:scale-95">
                                                    Klaim Hadiah
                                                </button>
                                                @endif
                                            @else
                                                <button type="button" onclick="viewMemberDetail('{{ $m->pelanggan_id }}', '{{ addslashes($m->user?->nama) }}', '{{ $m->display_kode_member }}', '{{ $m->saldo_poin }}', '{{ $m->total_kunjungan }}')" class="bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-[11px] px-3 py-1.5 rounded-xl transition flex items-center gap-1 mx-auto">
                                                    <span>Detail</span>
                                                    <span>&gt;</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-400">
                                            <p class="font-bold text-sm">Tidak ada member yang sesuai dengan pencarian atau filter tab.</p>
                                            <a href="{{ route('admin.member.index') }}" class="inline-block mt-2 text-xs font-bold text-amber-600 hover:underline">Reset Semua Filter</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                            <div>
                                Menampilkan <strong>{{ $members->count() }}</strong> dari <strong>{{ $totalMemberAktif }}</strong> member terdaftar
                            </div>
                            <div>
                                {{ $members->links() }}
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT 1/3 COLUMN: LOG KLAIM & BARCODE SCANNER -->
                <div class="w-full lg:w-1/3 space-y-5">
                    
                    <!-- WIDGET 1: LOG KLAIM HADIAH HARI INI -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📋</span>
                                <div>
                                    <h3 class="text-xs font-black text-slate-900">Log Klaim Hadiah Hari Ini</h3>
                                    <p class="text-[10px] text-slate-400">Loket A &amp; B • Shift Siang</p>
                                </div>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-amber-100"></span>
                        </div>

                        <!-- Claim List -->
                        <div class="space-y-3.5">
                            @forelse($todayClaims as $c)
                            <div class="p-3 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-1.5 hover:bg-slate-50 transition">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $c->nama_member }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $c->waktu_formatted }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] bg-white p-2 rounded-xl border border-slate-100">
                                    <span class="font-bold text-slate-700 flex items-center gap-1">
                                        <span>🎁</span>
                                        <span>{{ $c->nama_hadiah }}</span>
                                    </span>
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md font-mono">
                                        {{ $c->tipe_klaim }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5 font-mono">
                                    <span>Kasir: {{ $c->nama_kasir }}</span>
                                    <span class="text-slate-500 font-semibold">{{ $c->no_nota }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="py-6 text-center text-slate-400 text-xs">
                                Belum ada klaim hadiah pada shift ini.
                            </div>
                            @endforelse
                        </div>

                        <!-- View All Button -->
                        <button type="button" onclick="alert('Riwayat seluruh klaim reward bulan ini tersimpan aman di database.')" class="w-full py-2.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Buka Semua Riwayat Klaim Bulan Ini</span>
                        </button>
                    </div>

                    <!-- WIDGET 2: SCAN MEMBER CARD / QR WA -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-2xs space-y-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-2xs">
                                ⛶
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-slate-900">Scan Member Card / QR WA</h3>
                                <p class="text-[10px] text-slate-400">Ketik cepat ID atau tempel barcode scanner</p>
                            </div>
                        </div>

                        <!-- Barcode Search Input & Button -->
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                id="scannerInput"
                                placeholder="Scan Barcode / ID..." 
                                class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-mono font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                                onkeyup="if(event.key === 'Enter') handleScanMember();"
                            >
                            <button type="button" onclick="handleScanMember()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition active:scale-95">
                                Cek
                            </button>
                        </div>

                        <!-- Scanner Result Box (Hidden by Default) -->
                        <div id="scannerResultBox" class="hidden p-3 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs space-y-2">
                            <!-- Injected by JS -->
                        </div>

                        <div class="flex items-start gap-2 text-[11px] text-slate-400 bg-slate-50 p-3 rounded-2xl">
                            <span class="text-amber-500 font-bold">ℹ</span>
                            <p class="leading-relaxed">
                                Pelanggan baru otomatis dibuatkan ID saat memasukkan no. HP pada transaksi kasir walk-in.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </main>

    <!-- ========================================== -->
    <!-- MODAL 1: DAFTARKAN MEMBER BARU             -->
    <!-- ========================================== -->
    <div id="modalMemberBaru" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✨</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Daftarkan Member Baru Gerai</h3>
                </div>
                <button type="button" onclick="closeModalMemberBaru()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            
            <form action="{{ route('admin.member.store') }}" method="POST" class="p-6 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Pelanggan</label>
                    <input type="text" name="nama" placeholder="Contoh: Dimas Prasetyo" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="nomor_telepon" placeholder="Contoh: 0812-8902-1277" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori Member</label>
                        <select name="kategori_member" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                            <option value="Mahasiswa UPJ/STAN">Mahasiswa UPJ/STAN</option>
                            <option value="Instansi/Dosen">Instansi/Dosen</option>
                            <option value="Member Umum" selected>Member Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Badge Singkat</label>
                        <input type="text" name="badge_label" placeholder="Contoh: PKN STAN" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium uppercase">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keterangan Instansi / Catatan</label>
                    <input type="text" name="instansi_detail" placeholder="Contoh: Mahasiswa Akuntansi / Dosen UPJ" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bonus Poin Selamat Datang</label>
                    <input type="number" name="saldo_poin" value="50" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-sm">
                        Simpan &amp; Aktifkan Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: KLAIM HADIAH KASIR                -->
    <!-- ========================================== -->
    <div id="modalKlaimHadiah" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🎁</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Verifikasi Klaim Hadiah</h3>
                </div>
                <button type="button" onclick="closeModalKlaim()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            
            <form action="{{ route('admin.member.klaim-hadiah') }}" method="POST" class="p-6 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" id="klaimPelangganId" name="pelanggan_id" value="">
                
                <div class="bg-amber-50 p-3 rounded-2xl border border-amber-200">
                    <p class="text-[11px] text-amber-800 font-bold">Pelanggan:</p>
                    <p id="klaimNamaMember" class="text-sm font-black text-slate-900 mt-0.5">-</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Hadiah / Reward</label>
                    <input type="text" id="klaimNamaHadiah" name="nama_hadiah" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tipe Klaim</label>
                    <input type="text" id="klaimTipe" name="tipe_klaim" value="Milestone Kunjungan" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-sm">
                        Konfirmasi Serahkan Hadiah ke Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: ATUR PROGRAM HADIAH               -->
    <!-- ========================================== -->
    <div id="modalProgramHadiah" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⚙</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Konfigurasi Program Hadiah Gerai</h3>
                </div>
                <button type="button" onclick="closeModalProgramHadiah()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            
            <div class="p-6 space-y-3.5 text-xs">
                <p class="text-slate-500 leading-relaxed">
                    Aturan penambahan stamp &amp; poin saat ini:
                </p>
                <div class="space-y-2 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 font-medium">
                    <div class="flex justify-between">
                        <span>Konversi Belanja ke Poin:</span>
                        <strong class="font-mono text-slate-900">Rp 1.000 = 1 Poin</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Stamp Kunjungan per Transaksi:</span>
                        <strong class="font-mono text-slate-900">1 Transaksi = 1 Stamp</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Masa Berlaku Poin:</span>
                        <strong class="font-mono text-slate-900">12 Bulan</strong>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="closeModalProgramHadiah(); alert('Aturan program hadiah aktif.');" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC                           -->
    <!-- ========================================== -->
    <script>
        function openModalMemberBaru() {
            document.getElementById('modalMemberBaru').classList.remove('hidden');
        }
        function closeModalMemberBaru() {
            document.getElementById('modalMemberBaru').classList.add('hidden');
        }

        function openModalKlaim(pelangganId, namaMember, namaHadiah, tipeKlaim) {
            document.getElementById('klaimPelangganId').value = pelangganId;
            document.getElementById('klaimNamaMember').textContent = namaMember;
            document.getElementById('klaimNamaHadiah').value = namaHadiah;
            document.getElementById('klaimTipe').value = tipeKlaim;
            document.getElementById('modalKlaimHadiah').classList.remove('hidden');
        }
        function closeModalKlaim() {
            document.getElementById('modalKlaimHadiah').classList.add('hidden');
        }

        function openModalProgramHadiah() {
            document.getElementById('modalProgramHadiah').classList.remove('hidden');
        }
        function closeModalProgramHadiah() {
            document.getElementById('modalProgramHadiah').classList.add('hidden');
        }

        function viewMemberDetail(id, nama, kode, poin, kunjungan) {
            alert(`Member: ${nama} (${kode})\nFrekuensi Kunjungan: ${kunjungan}x\nSaldo Poin: ${poin} pts`);
        }

        function handleScanMember() {
            const input = document.getElementById('scannerInput');
            const code = input.value.trim();
            if (!code) {
                alert('Silakan masukkan ID Member atau nomor WhatsApp.');
                return;
            }

            fetch(`{{ route('admin.member.scan-ajax') }}?code=` + encodeURIComponent(code))
                .then(res => res.json())
                .then(data => {
                    const box = document.getElementById('scannerResultBox');
                    if (data.status === 'success') {
                        const m = data.member;
                        box.classList.remove('hidden');
                        box.innerHTML = `
                            <div class="flex items-center justify-between border-b border-amber-200/60 pb-2">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">${m.nama}</h4>
                                    <p class="text-[10px] text-slate-500 font-mono">${m.kode_member} • ${m.instansi}</p>
                                </div>
                                <span class="font-mono font-black text-amber-700 bg-amber-100 px-2 py-0.5 rounded-lg text-xs">${m.poin} pts</span>
                            </div>
                            <div class="flex justify-between text-[11px] pt-1">
                                <span>Kunjungan: <strong>${m.kunjungan}x</strong></span>
                                <span>Total Belanja: <strong>${m.belanja}</strong></span>
                            </div>
                            ${m.progress.is_claimable ? `
                                <button type="button" onclick="openModalKlaim('${m.pelanggan_id}', '${m.nama}', '${m.progress.reward_name}', 'Milestone Kunjungan')" class="w-full mt-2 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition">
                                    Klaim Hadiah: ${m.progress.reward_name}
                                </button>
                            ` : `
                                <p class="text-[10px] text-slate-500 mt-1 italic">${m.progress.sub}</p>
                            `}
                        `;
                    } else {
                        box.classList.remove('hidden');
                        box.innerHTML = `<p class="text-rose-600 font-bold">${data.message}</p>`;
                    }
                })
                .catch(err => {
                    alert('Gagal mencari member.');
                });
        }

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
