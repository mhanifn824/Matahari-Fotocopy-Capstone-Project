<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventaris Bahan & Stok Gerai - Matahari Fotocopy</title>
    
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F4F7FC;
        }
        .sidebar-bg {
            background: linear-gradient(180deg, #d3e6fe 0%, #bfdbfe 35%, #a3cdfe 70%, #96c7fe 100%);
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'stok'])

    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA                          -->
    <!-- ========================================== -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#F4F7FC]">
        
        <!-- ====================================== -->
        <!-- TOPBAR NAVIGATION                      -->
        <!-- ====================================== -->
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
                    id="searchStockInput"
                    placeholder="Cari no. antrean, nota, nama..." 
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition placeholder:text-slate-400 font-medium"
                    onkeyup="filterStockTable(this.value)"
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
                <button class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" onclick="alert('Pemberitahuan stok dan logistik telah diperbarui real-time.')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($stokMenipis > 0 || $stokKritis > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                    @endif
                </button>

                <!-- Profile Circle -->
                <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xs ring-2 ring-amber-200">
                    FA
                </div>
            </div>
        </header>

        <!-- Flash Message Notification -->
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

        <!-- ====================================== -->
        <!-- SUBHEADER & TOP ACTIONS                -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Title & Breadcrumb -->
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">
                        LOGISTIK & OPERASIONAL • <span class="text-amber-500">GUDANG UTAMA BINTARO</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                        Inventaris Bahan & Stok Gerai
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Pantau persediaan kertas, toner tinta, mika, kawat jilid, dan catat mutasi masuk/keluar
                    </p>
                </div>

                <!-- Top Right Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- + Catat Bahan Masuk (Amber Button) -->
                    <button type="button" onclick="openModalCatatMasuk()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Catat Bahan Masuk</span>
                    </button>

                    <!-- + Tambah Item Baru (White Button with Blue Border) -->
                    <button type="button" onclick="openModalTambahItem()" class="bg-white hover:bg-blue-50/50 border border-blue-200 text-blue-700 font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-1.5 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Item Baru</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- ====================================== -->
        <!-- WORKSPACE CONTENT                      -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 pb-10 space-y-6">

            <!-- CRITICAL STOCK ALERT BANNER (Matching Screenshot) -->
            @if($criticalBannerItem)
            <div class="bg-[#fee2e2]/70 border border-rose-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-2xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-extrabold text-rose-900">Peringatan Stok Kritis!</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wider bg-rose-600 text-white font-mono uppercase">
                                SEGERA RESTOCK
                            </span>
                        </div>
                        <p class="text-xs text-rose-700 mt-0.5">
                            {{ $criticalBannerItem->nama_bahan }} tersisa <strong class="underline font-bold">{{ $criticalBannerItem->jumlah_stok }} {{ $criticalBannerItem->satuan }}</strong> (Batas Minimum: {{ $criticalBannerItem->batas_minimum }} {{ $criticalBannerItem->satuan }}) — Segera restock untuk pesanan skripsi hari ini!
                        </p>
                    </div>
                </div>

                <button type="button" onclick="openPesanSupplier('{{ addslashes($criticalBannerItem->nama_bahan) }}')" class="bg-rose-900 hover:bg-rose-950 text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition flex-shrink-0 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Pesan ke Supplier</span>
                </button>
            </div>
            @endif

            <!-- ================================== -->
            <!-- 4 KPI SUMMARY METRIC CARDS         -->
            <!-- ================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: TOTAL ITEM TERDAFTAR -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">TOTAL ITEM TERDAFTAR</span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ $totalItems }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Jenis Bahan</span>
                        </div>
                        <p class="text-[11px] text-blue-600 font-semibold mt-1 flex items-center gap-1">
                            <span>✓</span>
                            <span>4 Kategori Material Operasional</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: STOK AMAN -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">STOK AMAN</span>
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-slate-900 font-mono">{{ $stokAman }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Bahan</span>
                        </div>
                        <p class="text-[11px] text-amber-600 font-medium mt-1">
                            ● {{ $persenAman }}% persediaan di atas kuota
                        </p>
                    </div>
                </div>

                <!-- Card 3: STOK MENIPIS -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">STOK MENIPIS</span>
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-amber-500 font-mono">{{ $stokMenipis }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Bahan</span>
                        </div>
                        <p class="text-[11px] text-amber-700/80 font-medium mt-1 truncate" title="{{ $menipisNames }}">
                            📄 {{ $menipisNames }}
                        </p>
                    </div>
                </div>

                <!-- Card 4: STOK KRITIS / HABIS -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">STOK KRITIS / HABIS</span>
                        <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-rose-600 font-mono">{{ $stokKritis }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1.5">Bahan</span>
                        </div>
                        <p class="text-[11px] text-rose-600 font-bold mt-1 truncate" title="{{ $kritisSummary }}">
                            ⚠️ {{ $kritisSummary }}
                        </p>
                    </div>
                </div>

            </div>

            <!-- ================================== -->
            <!-- TAB BAR & FILTERS                  -->
            <!-- ================================== -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                
                <!-- Left Tabs -->
                <div class="flex items-center gap-2">
                    <button type="button" class="bg-white border border-slate-200/90 text-slate-900 font-bold text-xs px-4 py-2 rounded-xl shadow-2xs flex items-center gap-2">
                        <span>📋 Daftar Stok Bahan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 font-mono text-slate-700">{{ $totalItems }}</span>
                    </button>
                    <button type="button" onclick="scrollToMutasi()" class="text-slate-600 hover:text-slate-900 font-semibold text-xs px-3 py-2 rounded-xl hover:bg-white/60 transition flex items-center gap-1.5">
                        <span>⏱️ Riwayat Mutasi Bahan</span>
                    </button>
                </div>

                <!-- Right Filters -->
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Dropdown Kategori -->
                    <select id="filterKategori" onchange="applyFilters()" class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>

                    <!-- Dropdown Status -->
                    <select id="filterStatus" onchange="applyFilters()" class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($statuses as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>

                    <!-- Export Button -->
                    <button type="button" onclick="exportStockCSV()" title="Ekspor Rekap Inventaris CSV" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 shadow-2xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- ================================== -->
            <!-- TABLE: DAFTAR STOK BAHAN           -->
            <!-- ================================== -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                                <th class="py-3 px-4">Kode Bahan</th>
                                <th class="py-3 px-4">Nama Bahan & Gramatur</th>
                                <th class="py-3 px-4 text-center">Kategori</th>
                                <th class="py-3 px-4 text-center">Sisa Stok</th>
                                <th class="py-3 px-4 text-center">Batas Min.</th>
                                <th class="py-3 px-4">Indikator Stok</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Terakhir Restock</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody" class="divide-y divide-slate-100">
                            @foreach($items as $item)
                            <tr class="hover:bg-slate-50/60 transition group font-medium text-slate-800" data-kategori="{{ $item->kategori }}" data-status="{{ $item->status_stok }}">
                                
                                <!-- Kode Bahan -->
                                <td class="py-3.5 px-4 font-mono font-extrabold text-slate-900 whitespace-nowrap">
                                    {{ $item->display_kode }}
                                </td>

                                <!-- Nama Bahan & Gramatur with Visual Image -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 p-1 flex-shrink-0 border border-slate-200/80 flex items-center justify-center overflow-hidden shadow-2xs group-hover:scale-105 transition-transform duration-150">
                                            <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_bahan }}" class="w-full h-full object-contain" loading="lazy">
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-900 leading-snug">{{ $item->nama_bahan }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->gramatur_detail ?: ($item->satuan . ' standard') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $item->kategori }}
                                    </span>
                                </td>

                                <!-- Sisa Stok -->
                                <td class="py-3.5 px-4 text-center font-mono font-extrabold whitespace-nowrap {{ $item->status_stok === 'Menipis' || $item->status_stok === 'Habis' ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $item->jumlah_stok }} {{ $item->satuan }}
                                </td>

                                <!-- Batas Min -->
                                <td class="py-3.5 px-4 text-center font-mono text-slate-500 whitespace-nowrap">
                                    Min {{ $item->batas_minimum }} {{ $item->satuan }}
                                </td>

                                <!-- Indikator Stok Bar -->
                                <td class="py-3.5 px-4 min-w-[160px]">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $item->indikator_bar_color }} h-1.5 rounded-full" style="width: {{ $item->persentase_kapasitas }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-mono mt-1 block {{ $item->status_stok === 'Menipis' || $item->status_stok === 'Habis' ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                        {{ $item->persentase_kapasitas }}% {{ $item->status_stok === 'Menipis' ? 'dari kapasitas aman' : 'dari kapasitas' }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->status_badge_class }}">
                                        ● {{ $item->status_stok }}
                                    </span>
                                </td>

                                <!-- Terakhir Restock -->
                                <td class="py-3.5 px-4 text-center text-slate-500 whitespace-nowrap">
                                    {{ $item->terakhir_restock_formatted }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="quickAddStockModal({{ $item->stok_id }}, '{{ addslashes($item->nama_bahan) }}', '{{ $item->satuan }}')" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-2.5 py-1 rounded-lg transition shadow-2xs">
                                            + Stok
                                        </button>
                                        <button type="button" onclick="editStockModal({{ $item->stok_id }}, '{{ addslashes($item->nama_bahan) }}', {{ $item->jumlah_stok }}, {{ $item->batas_minimum }})" class="p-1 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition">
                                            ✏️
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer & Pagination -->
                <div class="px-5 py-3.5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800">{{ $items->count() }}</span> dari <span class="font-bold text-slate-800">{{ $totalItems }}</span> total item inventaris gerai
                    </div>
                    <div class="flex items-center gap-1">
                        {{ $items->links() }}
                    </div>
                </div>

            </div>

            <!-- ================================== -->
            <!-- RIWAYAT BAHAN MASUK TERBARU        -->
            <!-- ================================== -->
            <div id="sectionMutasi" class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Riwayat Bahan Masuk Terbaru</h3>
                            <p class="text-xs text-slate-400">Faktur & Penerimaan resmi dari mitra CV Sinar Abadi Bintaro</p>
                        </div>
                    </div>

                    <a href="javascript:void(0)" onclick="alert('Arsip seluruh mutasi bahan gudang')" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition">
                        <span>Lihat Semua Arsip Mutasi</span>
                        <span>→</span>
                    </a>
                </div>

                <!-- 5 Horizontal Cards matching screenshot -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @foreach($recentMutasi as $mut)
                    <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-slate-50 transition flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-mono text-slate-400 text-[11px] font-bold">{{ $mut->no_surat_jalan }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800">Masuk</span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-slate-900 leading-snug">{{ $mut->nama_item }}</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $mut->spesifikasi }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                            <span>{{ $mut->waktu_formatted }}</span>
                            <span class="text-emerald-600 font-bold">✓✓</span>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

        </div>

    </main>

    <!-- ========================================== -->
    <!-- MODAL 1: CATAT BAHAN MASUK                 -->
    <!-- ========================================== -->
    <div id="modalCatatMasuk" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📦</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Catat Bahan Masuk dari Supplier</h3>
                </div>
                <button type="button" onclick="closeModalCatatMasuk()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            <form action="{{ route('admin.stok.catat-masuk') }}" method="POST" class="p-6 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Surat Jalan / Faktur</label>
                    <input type="text" name="no_surat_jalan" value="SJ-{{ date('Y-m') }}-{{ rand(10, 99) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Item Bahan Baku</label>
                    <select name="stok_id" id="catatStokSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        @foreach(App\Models\InventarisStok::orderBy('nama_bahan', 'asc')->get() as $stk)
                        <option value="{{ $stk->stok_id }}">{{ $stk->display_kode }} - {{ $stk->nama_bahan }} (Saat ini: {{ $stk->jumlah_stok }} {{ $stk->satuan }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jumlah Masuk</label>
                        <input type="number" name="jumlah_masuk" value="10" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Satuan</label>
                        <input type="text" name="satuan" value="Rim" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mitra Supplier / Distributor</label>
                    <input type="text" name="mitra_supplier" value="CV Sinar Abadi Bintaro" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-sm">
                        Simpan Penerimaan Bahan Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: TAMBAH ITEM BARU                  -->
    <!-- ========================================== -->
    <div id="modalTambahItem" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✨</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Tambah Item Bahan Baku Baru</h3>
                </div>
                <button type="button" onclick="closeModalTambahItem()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            <form action="{{ route('admin.stok.tambah-item') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Bahan</label>
                        <input type="text" name="kode_bahan" id="tambahKodeBahan" placeholder="K-A4-80" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono uppercase">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori</label>
                        <select name="kategori" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                            <option value="Kertas & Amplop">Kertas & Amplop</option>
                            <option value="Alat Tulis & Koreksi">Alat Tulis & Koreksi</option>
                            <option value="Ordner & Filing Document">Ordner & Filing Document</option>
                            <option value="Perlengkapan Packing & Jilid">Perlengkapan Packing & Jilid</option>
                            <option value="Kertas">Kertas</option>
                            <option value="Kertas Khusus">Kertas Khusus</option>
                            <option value="Tinta & Toner">Tinta & Toner</option>
                            <option value="Bahan Jilid">Bahan Jilid</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Bahan Baku</label>
                    <input type="text" name="nama_bahan" placeholder="Contoh: Kertas HVS A4 80gr PaperOne" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Gramatur & Keterangan Spesifikasi</label>
                    <input type="text" name="gramatur_detail" placeholder="210 × 297 mm • Putih Cerah" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>

                <!-- Upload Gambar Produk (Terintegrasi ke Home Customer) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Upload Gambar Produk (Tampil di Home Customer)</label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="gambar" id="inputGambarStok" accept="image/png,image/jpeg,image/webp,image/svg+xml" onchange="previewStockImage(this)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                        <div id="previewGambarContainer" class="hidden w-11 h-11 rounded-xl border border-slate-200 overflow-hidden bg-slate-50 flex-shrink-0 p-0.5">
                            <img id="previewGambarImg" src="" alt="Preview" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">
                        Disimpan otomatis ke folder publik produk (<span class="font-mono text-slate-600">images/stok/[kode_bahan].[ext]</span>) untuk integrasi etalase Home.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jumlah Awal</label>
                        <input type="number" name="jumlah_stok" value="10" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Batas Minimum</label>
                        <input type="number" name="batas_minimum" value="3" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Satuan</label>
                        <input type="text" name="satuan" value="Rim" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm">
                        Daftarkan Item ke Inventaris
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC                           -->
    <!-- ========================================== -->
    <script>
        function openModalCatatMasuk(stokId = null) {
            if (stokId) {
                document.getElementById('catatStokSelect').value = stokId;
            }
            document.getElementById('modalCatatMasuk').classList.remove('hidden');
        }
        function closeModalCatatMasuk() {
            document.getElementById('modalCatatMasuk').classList.add('hidden');
        }

        function openModalTambahItem() {
            document.getElementById('modalTambahItem').classList.remove('hidden');
        }
        function closeModalTambahItem() {
            document.getElementById('modalTambahItem').classList.add('hidden');
        }

        function previewStockImage(input) {
            const container = document.getElementById('previewGambarContainer');
            const img = document.getElementById('previewGambarImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                container.classList.add('hidden');
            }
        }

        function quickAddStockModal(stokId, nama, satuan) {
            const jumlah = prompt(`Tambah stok untuk "${nama}".\nMasukkan jumlah ${satuan} yang diterima:`, "5");
            if (jumlah && !isNaN(jumlah) && parseInt(jumlah) > 0) {
                fetch(`/admin/stok/${stokId}/quick-add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ jumlah: parseInt(jumlah) })
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.message);
                    window.location.reload();
                })
                .catch(err => alert('Gagal menambah stok.'));
            }
        }

        function editStockModal(stokId, nama, currentStok, minStok) {
            const newMin = prompt(`Ubah batas minimum untuk "${nama}":`, minStok);
            if (newMin && !isNaN(newMin)) {
                // quick feedback
                alert(`Batas minimum "${nama}" disesuaikan.`);
            }
        }

        function openPesanSupplier(namaItem) {
            const msg = `Halo Mitra CV Sinar Abadi Bintaro, kami dari Matahari Fotocopy ingin memesan restock bahan: ${namaItem}. Mohon kirimkan ketersediaan dan surat jalan terbaru. Terima kasih!`;
            const waUrl = `https://wa.me/6281234567890?text=${encodeURIComponent(msg)}`;
            window.open(waUrl, '_blank');
        }

        function scrollToMutasi() {
            document.getElementById('sectionMutasi').scrollIntoView({ behavior: 'smooth' });
        }

        function filterStockTable(query) {
            const term = query.toLowerCase().trim();
            const rows = document.querySelectorAll('#stockTableBody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(term) ? '' : 'none';
            });
        }

        function applyFilters() {
            const kat = document.getElementById('filterKategori').value;
            const stat = document.getElementById('filterStatus').value;
            const rows = document.querySelectorAll('#stockTableBody tr');

            rows.forEach(r => {
                const rKat = r.getAttribute('data-kategori');
                const rStat = r.getAttribute('data-status');

                let matchKat = (kat === 'Semua Kategori' || rKat === kat);
                let matchStat = (stat === 'Semua Status' || rStat === stat || (stat === 'Kritis / Habis' && (rStat === 'Kritis' || rStat === 'Habis')));

                r.style.display = (matchKat && matchStat) ? '' : 'none';
            });
        }

        function exportStockCSV() {
            window.location.href = "{{ route('admin.stok.live-data') }}";
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
