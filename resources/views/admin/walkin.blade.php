<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir Walk-in - Matahari Fotocopy</title>
    
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
        /* Custom scrollbar for cart and catalog */
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
        .animate-bounce-subtle {
            animation: bounceSubtle 1.5s infinite;
        }
        @keyframes bounceSubtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #receiptPrintArea, #receiptPrintArea * {
                visibility: visible;
            }
            #receiptPrintArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'walkin', 'activeAntreanCount' => $activeAntreanCount])

    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA                          -->
    <!-- ========================================== -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#F4F7FC]">
        
        <!-- ====================================== -->
        <!-- TOPBAR NAVIGATION                      -->
        <!-- ====================================== -->
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 md:px-8 py-3 flex items-center justify-between gap-4 min-h-[60px]">
            
            <!-- Search bar -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchPosInput"
                    placeholder="Cari nomor nota atau nama pelanggan..." 
                    class="w-full pl-10 pr-4 py-2 text-xs bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-slate-200/80 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-slate-800 placeholder-slate-400 font-medium"
                    onkeyup="filterPosTransactions(this.value)"
                    oninput="filterPosTransactions(this.value)"
                >
            </div>

            @include('admin.partials.topbar-right')

        </header>

        <!-- Flash message notification -->
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
        <!-- POS WALK-IN SUBHEADER & ACTION STRIP   -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                
                <!-- Title & Pills -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Kasir Walk-in
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        Penjualan langsung: ATK, fotokopi, jilid &amp; cetak dokumen walk-in
                    </p>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-2.5 flex-nowrap shrink-0">
                    <!-- Dynamic Omset Loket Pill -->
                    <div class="bg-white border border-slate-200/90 shadow-2xs rounded-xl px-3 py-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-800 shrink-0">
                        <span>💵</span>
                        <span>Omset: <strong id="headerOmsetVal" class="text-slate-900">Rp {{ number_format($todayOmset, 0, ',', '.') }}</strong> <span id="headerCountVal" class="text-slate-500 font-normal">({{ $todayCount }} Transaksi)</span></span>
                    </div>

                </div>
            </div>
        </div>

        <!-- ====================================== -->
        <!-- MAIN TWO-COLUMN WORKSPACE              -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 pb-10 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ================================== -->
            <!-- LEFT: CATALOG & TRX TABLE          -->
            <!-- ================================== -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-5">
                
                <!-- Category Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none flex-nowrap">
                    @foreach($categories as $index => $cat)
                    <button 
                        type="button" 
                        onclick="filterCategory('{{ $cat }}', this)" 
                        class="category-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap shrink-0 {{ $index === 0 ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
                    >
                        @if($cat === 'Semua Item')
                            <span>🔲</span>
                        @elseif($cat === 'Fotokopi & Print')
                            <span>🖨️</span>
                        @elseif($cat === 'Alat Tulis & Map')
                            <span>✏️</span>
                        @elseif($cat === 'Jilid & Finishing')
                            <span>📑</span>
                        @elseif($cat === 'Jasa & Lainnya')
                            <span>⚡</span>
                        @else
                            <span>📦</span>
                        @endif
                        <span>{{ $cat }}</span>
                    </button>
                    @endforeach
                </div>

                <!-- Quick Product Catalog Grid (Clickable cards to add to Cart) -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Pilih Layanan & ATK Cepat (Klik untuk Masuk Nota)</h3>
                        </div>
                        <span id="catalogItemCountBadge" class="text-[11px] text-slate-400 font-mono">{{ $catalogProducts->count() }} Item Tersedia</span>
                    </div>

                    <!-- Input Pencarian Barang & Layanan ATK -->
                    <div class="relative mb-3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="catalogSearchInput"
                            placeholder="Cari barang atau layanan ATK..." 
                            class="w-full pl-9 pr-8 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-slate-800 placeholder-slate-400 font-medium"
                            onkeyup="filterCatalogProducts()"
                            oninput="filterCatalogProducts()"
                        >
                        <button 
                            type="button" 
                            id="catalogSearchClearBtn" 
                            onclick="clearCatalogSearch()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden text-xs font-bold"
                            title="Hapus pencarian"
                        >
                            ✕
                        </button>
                    </div>

                    <div id="catalogGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($catalogProducts as $prod)
                        @php
                            $stokItem = $inventoryStockMap->get($prod->nama_item) ?? null;
                            if (!$stokItem) {
                                $stokItem = $inventoryStockMap->first(function ($s) use ($prod) {
                                    return str_contains(strtolower($prod->nama_item), strtolower($s->nama_bahan)) ||
                                           str_contains(strtolower($s->nama_bahan), strtolower($prod->nama_item));
                                });
                            }
                        @endphp
                        <div 
                            onclick="addItemToCart('{{ addslashes($prod->nama_item) }}', {{ (float)$prod->harga_dasar }}, '{{ $prod->kategori }}', {{ $prod->produk_id }})"
                            data-category="{{ $prod->kategori }}"
                            data-name="{{ strtolower($prod->nama_item) }}"
                            class="catalog-item-card p-2.5 rounded-xl border border-slate-200/90 hover:border-amber-400 hover:bg-amber-50/40 bg-slate-50/40 cursor-pointer transition transform active:scale-95 flex flex-col justify-between group min-h-[105px]"
                        >
                            <div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                    <span class="truncate font-semibold">{{ $prod->kategori }}</span>
                                    <span class="text-amber-600 font-bold opacity-0 group-hover:opacity-100 transition text-[10px]">+ Nota</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-amber-900">
                                    {{ $prod->nama_item }}
                                </h4>
                                <div class="mt-1.5 flex items-center">
                                    @if($stokItem)
                                        @if($stokItem->jumlah_stok > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Stok: {{ $stokItem->jumlah_stok }} {{ $stokItem->satuan }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Stok Habis
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                            Layanan Siap
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 pt-1 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-slate-900 group-hover:text-amber-700">
                                    Rp {{ number_format($prod->harga_dasar, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] w-5 h-5 rounded-md bg-slate-200/70 group-hover:bg-amber-500 group-hover:text-white flex items-center justify-center font-bold text-slate-600 transition">+</span>
                            </div>
                        </div>
                        @endforeach

                        <!-- Empty State Pencarian Barang / Layanan -->
                        <div id="catalogEmptyState" class="col-span-full py-8 text-center" style="display: none;">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-700 text-xs">Barang atau layanan tidak tersedia</p>
                            <p class="text-[11px] text-slate-400 mt-0.5" id="catalogEmptyText">Tidak ditemukan barang atau layanan yang sesuai dengan kata kunci.</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card: Transaksi Walk-In Kasir Hari Ini -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
                    
                    <!-- Table Header -->
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                                📋
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">Transaksi Walk-In Kasir Hari Ini</h3>
                            </div>
                        </div>
                        <span id="trxTableCountBadge" class="text-xs font-semibold text-slate-500 font-mono">
                            {{ $todayCount }} Transaksi Selesai • Real-time
                        </span>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                                    <th class="py-3 px-4">No. Nota / Waktu</th>
                                    <th class="py-3 px-4">Pelanggan</th>
                                    <th class="py-3 px-4">Item & Rincian</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                    <th class="py-3 px-4 text-center">Metode</th>
                                </tr>
                            </thead>
                            <tbody id="walkInTableBody" class="divide-y divide-slate-100">
                                @forelse($todayWalkInOrders as $order)
                                <tr class="walkin-order-row hover:bg-slate-50/60 transition group font-medium text-slate-800"
                                    data-nota="{{ strtolower($order->kode_nota_walkin . ' ' . str_replace('#', '', $order->kode_nota_walkin)) }}"
                                    data-nama="{{ strtolower($order->display_name) }}">
                                    <!-- No. Nota / Waktu -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-extrabold font-mono text-slate-900">{{ $order->kode_nota_walkin }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $order->waktu_transaksi_format }}</div>
                                    </td>
                                    <!-- Pelanggan -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $order->display_name }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium">{{ $order->display_tipe }}</div>
                                    </td>
                                    <!-- Item & Rincian -->
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-xs text-slate-700 leading-snug line-clamp-2">
                                            {{ $order->spesifikasi ?: $order->display_judul }}
                                        </div>
                                    </td>
                                    <!-- Total -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <span class="font-mono font-extrabold text-slate-900 text-xs">
                                            Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <!-- Metode Pembayaran Badge -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider font-mono {{ $order->metode_badge_class }}">
                                            {{ strtoupper($order->metode_pembayaran) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <!-- Empty State -->
                                <tr id="emptyTableRow">
                                    <td colspan="5" class="py-12 px-4 text-center">
                                        <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </div>
                                        <h4 class="font-extrabold text-slate-800 text-sm">Belum Ada Transaksi Walk-In Kasir Hari Ini</h4>
                                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                                            Kondisi sistem berada dalam keadaan awal (Empty State). Silakan pilih item dari katalog untuk menambahkan transaksi baru atau klik tombol demo di kanan atas.
                                        </p>
                                    </td>
                                </tr>
                                @endforelse

                                <!-- Search Not Found State -->
                                <tr id="searchNotFoundRow" style="display: none;">
                                    <td colspan="5" class="py-12 px-4 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-2">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                            <p class="font-bold text-slate-700 text-sm">Data tidak tersedia</p>
                                            <p class="text-xs text-slate-400 not-found-text">Nomor nota atau nama pelanggan tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================================== -->
            <!-- RIGHT: NOTA POS REGISTER           -->
            <!-- ================================== -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-16">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-3.5 sm:p-4 space-y-3">
                    
                    <!-- Cart Card Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-lg">👜</span>
                            <div class="min-w-0">
                                <h3 id="cartNotaTitle" class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight whitespace-nowrap">
                                    Nota Walk-in {{ $nextNotaCode }}
                                </h3>
                                <span id="cartCustomerNameText" class="hidden">Pelanggan Umum (Walk-in)</span>
                            </div>
                        </div>
                        <span id="cartItemsCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white font-mono whitespace-nowrap shrink-0">
                            0 Item
                        </span>
                    </div>

                    <!-- Customer Selector Capsule -->
                    <div class="bg-slate-50/80 rounded-2xl p-2 sm:p-2.5 border border-slate-200/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs shrink-0">
                                👤
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 id="currentCustomerName" class="text-xs font-bold text-slate-900 truncate">
                                        Pelanggan Umum
                                    </h4>
                                    <span id="currentCustomerBadge" class="text-[10px] font-semibold text-slate-600 bg-slate-200/70 px-1.5 py-0.5 rounded-md inline-block whitespace-nowrap">
                                        Non-Member
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="openCustomerModal()" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-xl shadow-2xs transition shrink-0">
                            Ganti
                        </button>
                    </div>

                    <!-- Cart Item Rows Container -->
                    <div id="cartItemsList" class="space-y-2 max-h-44 overflow-y-auto pr-1 divide-y divide-slate-100 custom-scrollbar">
                        <!-- Populated by JavaScript -->
                        <div id="cartEmptyPlaceholder" class="py-4 px-3 text-center text-slate-400">
                            <svg class="w-7 h-7 mx-auto mb-1.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-xs font-bold text-slate-600">Keranjang Nota Masih Kosong</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Klik item katalog di samping untuk menambahkan ke nota</p>
                        </div>
                    </div>

                    <!-- Summary & Discount -->
                    <div class="pt-2 border-t border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-500 font-medium">
                            <span id="subtotalItemLabel" class="truncate">Subtotal Item (0 item)</span>
                            <span id="subtotalValText" class="font-mono text-slate-800 font-bold whitespace-nowrap shrink-0 ml-2">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-amber-700 font-bold">
                            <span class="flex items-center gap-1 min-w-0 truncate">
                                <span class="shrink-0">🏷️</span>
                                <span class="truncate">Diskon Member (Tier Point)</span>
                            </span>
                            <span id="diskonValText" class="font-mono whitespace-nowrap shrink-0 ml-2">- Rp 0</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 uppercase tracking-tight truncate">Total Tagihan Kasir</span>
                            <span id="totalTagihanValText" class="text-xl font-black font-mono text-slate-900 whitespace-nowrap shrink-0 ml-2">Rp 0</span>
                        </div>
                    </div>

                    <!-- Payment Method Quick Selector -->
                    <div class="pt-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Pilihan Cepat Metode Pembayaran
                        </label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" onclick="selectPaymentMethod('QRIS', this)" class="payment-method-btn active py-1.5 rounded-xl border border-sky-300 bg-sky-50 text-sky-800 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">📱</span>
                                <span class="text-[10px] font-mono">QRIS</span>
                            </button>
                            <button type="button" onclick="selectPaymentMethod('TUNAI', this)" class="payment-method-btn py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">💵</span>
                                <span class="text-[10px] font-mono">Tunai</span>
                            </button>
                            <button type="button" onclick="selectPaymentMethod('TRANSFER', this)" class="payment-method-btn py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">🏦</span>
                                <span class="text-[10px] font-mono">Transfer</span>
                            </button>
                        </div>
                    </div>

                    <!-- Primary Checkout Button -->
                    <button 
                        type="button" 
                        id="checkoutReviewBtn"
                        onclick="openReviewModal()" 
                        class="w-full py-3 px-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-md transition active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Selesaikan Transaksi (Enter)</span>
                    </button>
                </div>
            </div>



                </div>
            </div>

        </div>

    </main>

    <!-- ========================================== -->
    <!-- MODAL: GANTI / PILIH PELANGGAN             -->
    <!-- ========================================== -->
    <div id="customerModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">👤</span>
                    <h3 class="text-sm font-extrabold text-slate-900">Pilih Data Pelanggan Walk-in</h3>
                </div>
                <button type="button" onclick="closeCustomerModal()" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pelanggan / Tamu</label>
                    <input type="text" id="custInputName" value="Pelanggan Umum" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="custInputPhone" value="-" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Member & Diskon</label>
                    <select id="custInputTier" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        <option value="non_member" data-disc="0" selected>Tamu Reguler / Non-Member (Tanpa Diskon)</option>
                        <option value="member_aktif" data-disc="2500">Member Aktif: ID MTH-8821 (Diskon Rp 2.500)</option>
                        <option value="member_bisnis" data-disc="5000">Member premium(Diskon Rp 5.000)</option>
                    </select>
                </div>
                <div class="pt-2">
                    <button type="button" onclick="applyCustomerChoice()" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition">
                        Terapkan ke Nota Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: KONFIRMASI RINGKASAN TRANSAKSI      -->
    <!-- ========================================== -->
    <div id="receiptModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Konfirmasi Transaksi Walk-In</h3>
                    <p class="text-[11px] text-slate-500">Periksa rincian nota & pembayaran sebelum submit</p>
                </div>
                <button type="button" onclick="closeReceiptModal()" class="text-slate-400 hover:text-slate-600 font-bold text-sm">✕</button>
            </div>

            <div class="p-6 space-y-4" id="receiptPrintArea">
                <!-- Receipt Header -->
                <div class="text-center space-y-1 border-b border-dashed border-slate-300 pb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Matahari Fotocopy" class="h-10 mx-auto object-contain mb-2">
                    <h3 class="font-extrabold text-slate-900 text-sm">MATAHARI FOTOCOPY</h3>
                    <p class="text-[10px] text-slate-500 leading-tight">Jl. Kaliurang KM 5.5 No. 12, Sleman, Yogyakarta<br>Telp / WA: 0812-3456-7890</p>
                    <div class="pt-2 text-[10px] font-mono text-slate-600 flex justify-between">
                        <span id="rcptNotaCode">#W-1048</span>
                        <span id="rcptTime">14:32 WIB</span>
                    </div>
                    <div class="text-[10px] font-mono text-slate-600 flex justify-between">
                        <span>Kasir: {{ $user->nama ?? 'Feri Anto' }}</span>
                        <span id="rcptCustomer">Rian Pratama</span>
                    </div>
                </div>

                <!-- Receipt Items -->
                <div id="rcptItemsList" class="space-y-1.5 text-[11px] font-mono border-b border-dashed border-slate-300 pb-3">
                    <!-- Populated via JS -->
                </div>

                <!-- Receipt Total -->
                <div class="text-xs space-y-1 border-b border-dashed border-slate-300 pb-3 font-mono">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span id="rcptSubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-amber-700">
                        <span>Diskon Member:</span>
                        <span id="rcptDiskon">- Rp 0</span>
                    </div>
                    <div class="flex justify-between font-black text-slate-900 text-sm pt-1">
                        <span>TOTAL TAGIHAN:</span>
                        <span id="rcptTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-600 pt-1">
                        <span>Metode Pembayaran:</span>
                        <span id="rcptMetode" class="font-bold">QRIS</span>
                    </div>
                </div>

                <!-- Receipt Footer -->
                <div class="text-center text-[10px] text-slate-400 font-mono space-y-0.5">
                    <p>Matahari Fotocopy & Percetakan Digital</p>
                    <p>Dokumen tersimpan aman dan akurat.</p>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="bg-slate-50 p-4 border-t border-slate-200 flex items-center gap-2">
                <button type="button" id="modalSubmitBtn" onclick="submitCheckout()" class="flex-1 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="modalSubmitBtnText">Submit Transaksi (Enter)</span>
                </button>
                <button type="button" onclick="closeReceiptModal()" class="px-4 py-3 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: SUKSES TRANSAKSI DISIMPAN           -->
    <!-- ========================================== -->
    <div id="successModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-200 p-6 text-center space-y-4">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold shadow-xs">
                ✓
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Data Transaksi Berhasil Disimpan!</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Nota <span id="successNotaCode" class="font-bold text-slate-900 font-mono">#W-1049</span> sebesar <span id="successTotalText" class="font-bold text-amber-600 font-mono">Rp 0</span> telah masuk ke Tabel Transaksi Walk-In Kasir Hari Ini.
                </p>
            </div>
            <div class="pt-2">
                <button type="button" onclick="closeSuccessModal()" class="w-full py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-md transition active:scale-[0.98]">
                    OK / Transaksi Baru (Enter)
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TOAST NOTIFICATION CONTAINER               -->
    <!-- ========================================== -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT POS CONTROLLER & CART LOGIC     -->
    <!-- ========================================== -->
    <script>
        // Active POS state
        let cart = [];
        let currentCustomer = {
            name: 'Pelanggan Umum',
            phone: '-',
            tier: 'Tamu Reguler / Non-Member',
            discount: 0,
            pelanggan_id: null
        };
        let selectedPayment = 'QRIS';
        let nextNota = '{{ $nextNotaCode }}';

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            renderCart();
            startLivePolling();
            startLiveClock();

            // Keyboard shortcut support
            document.addEventListener('keydown', function(e) {
                if (e.key === 'F12') {
                    e.preventDefault();
                    triggerOpenDrawer();
                } else if (e.key === 'Escape') {
                    closeSuccessModal();
                    closeReceiptModal();
                    closeCustomerModal();
                } else if (e.key === 'Enter' && !e.target.matches('input, textarea, select')) {
                    const succModal = document.getElementById('successModal');
                    if (succModal && !succModal.classList.contains('hidden')) {
                        e.preventDefault();
                        closeSuccessModal();
                        return;
                    }
                    const rcptModal = document.getElementById('receiptModal');
                    if (rcptModal && !rcptModal.classList.contains('hidden')) {
                        e.preventDefault();
                        submitCheckout();
                        return;
                    }
                    if (cart.length > 0) {
                        e.preventDefault();
                        openReviewModal();
                    }
                }
            });
        });

        // Add Item to Cart
        function addItemToCart(nama, harga, kategori, produkId = null) {
            const existingIndex = cart.findIndex(item => item.nama_item === nama);
            if (existingIndex > -1) {
                cart[existingIndex].kuantitas += 1;
                cart[existingIndex].sub_total = cart[existingIndex].kuantitas * cart[existingIndex].harga_satuan;
            } else {
                cart.push({
                    nama_item: nama,
                    rincian: `@ Rp ${formatNumber(harga)}`,
                    harga_satuan: harga,
                    kuantitas: 1,
                    sub_total: harga,
                    kategori: kategori,
                    produk_id: produkId
                });
            }
            renderCart();
            showToast(`Item "${nama}" ditambahkan ke nota.`);
        }

        // Adjust Item Quantity
        function updateItemQty(index, delta) {
            if (!cart[index]) return;
            cart[index].kuantitas += delta;
            if (cart[index].kuantitas <= 0) {
                cart.splice(index, 1);
            } else {
                cart[index].sub_total = cart[index].kuantitas * cart[index].harga_satuan;
            }
            renderCart();
        }

        // Live manual typing in quantity input
        function handleQtyInput(index, inputEl) {
            if (!cart[index]) return;
            const val = parseInt(inputEl.value, 10);
            if (!isNaN(val) && val > 0) {
                cart[index].kuantitas = val;
                cart[index].sub_total = cart[index].kuantitas * cart[index].harga_satuan;
                updateOrderSummaryOnly();
            }
        }

        // Finalize manual quantity on blur
        function handleQtyBlur(index, inputEl) {
            if (!cart[index]) return;
            let val = parseInt(inputEl.value, 10);
            if (isNaN(val) || val <= 0) {
                val = 1;
                inputEl.value = 1;
            }
            cart[index].kuantitas = val;
            cart[index].sub_total = cart[index].kuantitas * cart[index].harga_satuan;
            renderCart();
        }

        // Lightweight live totals update while user is typing
        function updateOrderSummaryOnly() {
            const subtotalText = document.getElementById('subtotalValText');
            const diskonText = document.getElementById('diskonValText');
            const totalText = document.getElementById('totalTagihanValText');

            let subtotal = 0;
            cart.forEach((item, idx) => {
                subtotal += item.sub_total;
                const rowSubtotalEl = document.getElementById(`cart-item-subtotal-${idx}`);
                if (rowSubtotalEl) {
                    rowSubtotalEl.textContent = `Rp ${formatNumber(item.sub_total)}`;
                }
            });

            if (subtotalText) subtotalText.textContent = `Rp ${formatNumber(subtotal)}`;

            const rawDiscount = currentCustomer.discount || 0;
            const appliedDiscount = Math.min(rawDiscount, subtotal);
            const finalTotal = Math.max(0, subtotal - appliedDiscount);

            if (diskonText) diskonText.textContent = `- Rp ${formatNumber(appliedDiscount)}`;
            if (totalText) totalText.textContent = `Rp ${formatNumber(finalTotal)}`;
        }

        // Remove item from Cart
        function removeItemFromCart(index) {
            if (!cart[index]) return;
            const removed = cart.splice(index, 1);
            renderCart();
            showToast(`"${removed[0].nama_item}" dihapus dari nota.`);
        }

        // Clear Cart
        function clearCurrentCart() {
            if (cart.length === 0) {
                showToast('Nota sudah dalam keadaan kosong.');
                return;
            }
            cart = [];
            renderCart();
            showToast('Nota kasir telah dikosongkan.');
        }

        // Render Cart DOM
        function renderCart() {
            const listContainer = document.getElementById('cartItemsList');
            const itemsCountBadge = document.getElementById('cartItemsCountBadge');
            const subtotalLabel = document.getElementById('subtotalItemLabel');
            const subtotalText = document.getElementById('subtotalValText');
            const diskonText = document.getElementById('diskonValText');
            const totalText = document.getElementById('totalTagihanValText');
            const submitBtn = document.getElementById('checkoutReviewBtn');

            if (cart.length === 0) {
                listContainer.innerHTML = `
                    <div id="cartEmptyPlaceholder" class="py-4 px-3 text-center text-slate-400">
                        <svg class="w-7 h-7 mx-auto mb-1.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-xs font-bold text-slate-600">Keranjang Nota Masih Kosong</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Klik item katalog di samping untuk menambahkan ke nota</p>
                    </div>
                `;
                if (itemsCountBadge) itemsCountBadge.textContent = '0 Item';
                if (subtotalLabel) subtotalLabel.textContent = 'Subtotal Item (0 item)';
                if (subtotalText) subtotalText.textContent = 'Rp 0';
                if (diskonText) diskonText.textContent = '- Rp 0';
                if (totalText) totalText.textContent = 'Rp 0';
                if (submitBtn) submitBtn.disabled = true;
                return;
            }

            if (submitBtn) submitBtn.disabled = false;
            let subtotal = 0;
            let totalItemsCount = 0;
            let html = '';

            cart.forEach((item, idx) => {
                subtotal += item.sub_total;
                totalItemsCount += item.kuantitas;

                html += `
                    <div class="py-2.5 flex items-center justify-between gap-3 group">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-1">${item.nama_item}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">${item.rincian}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Stepper with manual typing input -->
                            <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50 overflow-hidden font-mono text-xs">
                                <button type="button" onclick="updateItemQty(${idx}, -1)" class="px-2 py-0.5 hover:bg-slate-200 text-slate-600 transition font-bold select-none">-</button>
                                <input 
                                    type="number" 
                                    min="1" 
                                    value="${item.kuantitas}" 
                                    oninput="handleQtyInput(${idx}, this)" 
                                    onblur="handleQtyBlur(${idx}, this)" 
                                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); this.blur(); }" 
                                    class="w-12 text-center font-bold text-slate-800 bg-white border-x border-slate-200 py-0.5 focus:outline-none focus:ring-1 focus:ring-amber-400 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                    title="Ketik jumlah item manual"
                                >
                                <button type="button" onclick="updateItemQty(${idx}, 1)" class="px-2 py-0.5 hover:bg-slate-200 text-slate-600 transition font-bold select-none">+</button>
                            </div>
                            <!-- Price & Delete -->
                            <span id="cart-item-subtotal-${idx}" class="font-mono font-bold text-xs text-slate-900 w-20 text-right">
                                Rp ${formatNumber(item.sub_total)}
                            </span>
                            <button type="button" onclick="removeItemFromCart(${idx})" class="text-slate-300 hover:text-red-500 transition p-1">
                                🗑️
                            </button>
                        </div>
                    </div>
                `;
            });

            listContainer.innerHTML = html;
            itemsCountBadge.textContent = `${cart.length} Item`;
            subtotalLabel.textContent = `Subtotal Item (${cart.length} item)`;
            subtotalText.textContent = `Rp ${formatNumber(subtotal)}`;

            // Calculate Discount (Discount cannot exceed subtotal)
            const rawDiscount = currentCustomer.discount || 0;
            const appliedDiscount = Math.min(rawDiscount, subtotal);
            const finalTotal = Math.max(0, subtotal - appliedDiscount);

            diskonText.textContent = `- Rp ${formatNumber(appliedDiscount)}`;
            totalText.textContent = `Rp ${formatNumber(finalTotal)}`;
        }

        // Select Payment Method
        function selectPaymentMethod(method, btn) {
            selectedPayment = method;
            document.querySelectorAll('.payment-method-btn').forEach(b => {
                b.classList.remove('active', 'border-sky-300', 'bg-sky-50', 'text-sky-800');
                b.classList.add('border-slate-200', 'text-slate-700');
            });
            btn.classList.add('active', 'border-sky-300', 'bg-sky-50', 'text-sky-800');
            btn.classList.remove('border-slate-200', 'text-slate-700');
        }

        let activeCatalogCategory = 'Semua Item';

        // Filter Category in Catalog Grid
        function filterCategory(cat, btn) {
            activeCatalogCategory = cat;
            document.querySelectorAll('.category-filter-btn').forEach(b => {
                b.classList.remove('active', 'bg-slate-900', 'text-white', 'shadow-xs');
                b.classList.add('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
            });
            if (btn) {
                btn.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-xs');
                btn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
            }
            applyCatalogFilter();
        }

        // Filter Products by keyword search in Catalog
        function filterCatalogProducts() {
            const input = document.getElementById('catalogSearchInput');
            const clearBtn = document.getElementById('catalogSearchClearBtn');
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', !input || !input.value.trim());
            }
            applyCatalogFilter();
        }

        // Clear Catalog Search
        function clearCatalogSearch() {
            const input = document.getElementById('catalogSearchInput');
            if (input) {
                input.value = '';
                input.focus();
            }
            const clearBtn = document.getElementById('catalogSearchClearBtn');
            if (clearBtn) {
                clearBtn.classList.add('hidden');
            }
            applyCatalogFilter();
        }

        // Unified Catalog Filter (Category + Search Keyword)
        function applyCatalogFilter() {
            const keyword = (document.getElementById('catalogSearchInput')?.value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.catalog-item-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const itemCat = card.getAttribute('data-category') || '';
                const itemName = (card.getAttribute('data-name') || card.querySelector('h4')?.textContent || '').toLowerCase();

                const matchesCat = (activeCatalogCategory === 'Semua' || activeCatalogCategory === 'Semua Item' || itemCat === activeCatalogCategory);
                const matchesKeyword = (keyword === '' || itemName.includes(keyword));

                if (matchesCat && matchesKeyword) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Empty state handling
            const emptyState = document.getElementById('catalogEmptyState');
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
                const emptyText = document.getElementById('catalogEmptyText');
                if (emptyText) {
                    if (keyword !== '') {
                        emptyText.textContent = `Tidak ditemukan barang atau layanan untuk kata kunci "${keyword}".`;
                    } else {
                        emptyText.textContent = `Tidak ada item pada kategori "${activeCatalogCategory}".`;
                    }
                }
            }

            // Update badge count
            const countBadge = document.getElementById('catalogItemCountBadge');
            if (countBadge) {
                countBadge.textContent = `${visibleCount} Item Tersedia`;
            }
        }

        // Customer Selection Modal Handling
        function openCustomerModal() {
            document.getElementById('customerModal').classList.remove('hidden');
        }
        function closeCustomerModal() {
            document.getElementById('customerModal').classList.add('hidden');
        }
        function applyCustomerChoice() {
            const name = document.getElementById('custInputName').value || 'Pelanggan Walk-in';
            const phone = document.getElementById('custInputPhone').value || '-';
            const tierSelect = document.getElementById('custInputTier');
            const tierText = tierSelect.options[tierSelect.selectedIndex].text;
            const discount = parseFloat(tierSelect.options[tierSelect.selectedIndex].getAttribute('data-disc')) || 0;

            currentCustomer.name = name;
            currentCustomer.phone = phone;
            currentCustomer.tier = tierText;
            currentCustomer.discount = discount;

            document.getElementById('currentCustomerName').textContent = `${name} • ${phone}`;
            document.getElementById('currentCustomerBadge').textContent = tierText;
            document.getElementById('cartCustomerNameText').textContent = `${name} (Walk-in)`;

            closeCustomerModal();
            renderCart();
            showToast(`Data pelanggan diatur ke "${name}".`);
        }

        // Open Cash Drawer Simulation
        function triggerOpenDrawer() {
            // Visual & audio feedback
            showToast('🖨️ Laci Kasir (Cash Drawer) Loket A05 Telah Terbuka.');
        }

        // Open Review / Confirmation Modal
        function openReviewModal() {
            if (cart.length === 0) {
                showToast('Tambahkan item terlebih dahulu sebelum menyelesaikan transaksi.');
                return;
            }

            const subtotal = cart.reduce((acc, it) => acc + it.sub_total, 0);
            const rawDiscount = currentCustomer.discount || 0;
            const appliedDiscount = Math.min(rawDiscount, subtotal);
            const finalTotal = Math.max(0, subtotal - appliedDiscount);
            const now = new Date();
            const timeStr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')} WIB`;

            document.getElementById('rcptNotaCode').textContent = nextNota;
            document.getElementById('rcptTime').textContent = timeStr;
            document.getElementById('rcptCustomer').textContent = currentCustomer.name;
            document.getElementById('rcptSubtotal').textContent = `Rp ${formatNumber(subtotal)}`;
            document.getElementById('rcptDiskon').textContent = `- Rp ${formatNumber(appliedDiscount)}`;
            document.getElementById('rcptTotal').textContent = `Rp ${formatNumber(finalTotal)}`;
            const rcptMetodeEl = document.getElementById('rcptMetode');
            if (rcptMetodeEl) rcptMetodeEl.textContent = selectedPayment;

            let itemsHtml = '';
            cart.forEach(it => {
                itemsHtml += `
                    <div class="flex justify-between items-start">
                        <div class="truncate pr-2">
                            <div>${it.nama_item}</div>
                            <div class="text-[9px] text-slate-400">${it.kuantitas}x @ Rp ${formatNumber(it.harga_satuan)}</div>
                        </div>
                        <span class="font-bold">Rp ${formatNumber(it.sub_total)}</span>
                    </div>
                `;
            });
            document.getElementById('rcptItemsList').innerHTML = itemsHtml;
            document.getElementById('receiptModal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
        }

        // Submit Checkout Transaction to Database
        async function submitCheckout() {
            if (cart.length === 0) {
                showToast('Tambahkan item terlebih dahulu sebelum menyelesaikan transaksi.');
                return;
            }

            const subtotal = cart.reduce((acc, it) => acc + it.sub_total, 0);
            const rawDiscount = currentCustomer.discount || 0;
            const appliedDiscount = Math.min(rawDiscount, subtotal);
            const finalTotal = Math.max(0, subtotal - appliedDiscount);

            const payload = {
                nama_pemesan: currentCustomer.name,
                tipe_pelanggan: currentCustomer.tier,
                pelanggan_id: currentCustomer.pelanggan_id,
                items: cart,
                subtotal: subtotal,
                diskon: appliedDiscount,
                total_harga: finalTotal,
                metode_pembayaran: selectedPayment,
                uang_diterima: finalTotal,
                kembalian: 0,
                kirim_workshop: false
            };

            const submitBtn = document.getElementById('modalSubmitBtn');
            const submitBtnText = document.getElementById('modalSubmitBtnText');
            if (submitBtn) submitBtn.disabled = true;
            if (submitBtnText) submitBtnText.textContent = 'Menyimpan ke Tabel Transaksi...';

            try {
                const response = await fetch("{{ route('admin.walkin.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.status === 'success') {
                    // Close confirmation modal
                    closeReceiptModal();

                    // Open Success Modal
                    document.getElementById('successNotaCode').textContent = data.pesanan.no_nota;
                    document.getElementById('successTotalText').textContent = data.pesanan.total_formatted;
                    document.getElementById('successModal').classList.remove('hidden');

                    showToast(`Transaksi nota ${data.pesanan.no_nota} berhasil disimpan ke tabel.`);
                    
                    // Reset cart
                    cart = [];
                    renderCart();
                    
                    // Refresh data table
                    fetchLiveWalkInData();
                } else {
                    alert('Gagal menyelesaikan transaksi: ' + (data.message || 'Error'));
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menyimpan transaksi.');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (submitBtnText) submitBtnText.textContent = 'Submit Transaksi (Enter)';
            }
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        function sendReceiptWhatsapp() {
            const phone = currentCustomer.phone || prompt('Masukkan nomor WhatsApp pelanggan:');
            if (phone) {
                showToast(`Struk PDF berhasil dijadwalkan kirim ke WhatsApp ${phone}.`);
            }
        }

        function holdCurrentCart() {
            if (cart.length === 0) {
                showToast('Tidak ada nota aktif yang dapat ditahan.');
                return;
            }
            showToast('Nota berhasil ditahan (Pending Slot #01).');
            cart = [];
            renderCart();
        }

        // Live polling for table updates
        function startLivePolling() {
            setInterval(fetchLiveWalkInData, 5000);
        }

        async function fetchLiveWalkInData() {
            try {
                const res = await fetch("{{ route('admin.walkin.live-data') }}");
                if (!res.ok) return;
                const data = await res.json();

                // Update Header Badges
                if (document.getElementById('headerOmsetVal')) document.getElementById('headerOmsetVal').textContent = data.todayOmsetFormatted;
                if (document.getElementById('headerCountVal')) document.getElementById('headerCountVal').textContent = `(${data.todayCount} Transaksi)`;
                if (document.getElementById('cartNotaTitle')) document.getElementById('cartNotaTitle').textContent = `Nota Walk-in ${data.nextNotaCode}`;
                nextNota = data.nextNotaCode;
                if (document.getElementById('trxTableCountBadge')) document.getElementById('trxTableCountBadge').textContent = `${data.todayCount} Transaksi Selesai • Real-time`;

                const queueBadge = document.getElementById('sidebar-queue-badge');
                if (queueBadge) queueBadge.textContent = data.activeAntreanCount;

                // Update Table Rows
                const tbody = document.getElementById('walkInTableBody');
                if (data.orders.length === 0) {
                    tbody.innerHTML = `
                        <tr id="emptyTableRow">
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-slate-800 text-sm">Belum Ada Transaksi Walk-In Kasir Hari Ini</h4>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                                    Kondisi sistem berada dalam keadaan awal (Empty State). Silakan pilih item dari katalog untuk menambahkan transaksi baru atau klik tombol demo di kanan atas.
                                </p>
                            </td>
                        </tr>
                    `;
                } else {
                    let rowsHtml = '';
                    data.orders.forEach(ord => {
                        rowsHtml += `
                            <tr class="walkin-order-row hover:bg-slate-50/60 transition group font-medium text-slate-800"
                                data-nota="${(ord.no_nota || '').toLowerCase()} ${(ord.no_nota || '').replace('#', '').toLowerCase()}"
                                data-nama="${(ord.nama || '').toLowerCase()}">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-extrabold font-mono text-slate-900">${ord.no_nota}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">${ord.waktu}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">${ord.nama}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">${ord.tipe}</div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="text-xs text-slate-700 leading-snug line-clamp-2">${ord.items_summary}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <span class="font-mono font-extrabold text-slate-900 text-xs">${ord.total_formatted}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider font-mono ${ord.metode_badge_class}">
                                        ${ord.metode}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                    rowsHtml += `
                        <tr id="searchNotFoundRow" style="display: none;">
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Data tidak tersedia</p>
                                    <p class="text-xs text-slate-400 not-found-text">Nomor nota atau nama pelanggan tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML = rowsHtml;
                    const curSearch = document.getElementById('searchPosInput')?.value || '';
                    if (curSearch) {
                        filterPosTransactions(curSearch);
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }

        // Live Clock
        function startLiveClock() {
            setInterval(() => {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const day = now.getDate();
                const month = months[now.getMonth()];
                document.getElementById('liveClockDisplay').textContent = `${hours}:${minutes} WIB • ${day} ${month}`;
            }, 1000);
        }

        // Table search filter: strictly filters nomor nota and nama pelanggan only
        function filterPosTransactions(query) {
            const term = (query || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#walkInTableBody tr.walkin-order-row');
            let visibleCount = 0;

            rows.forEach(r => {
                const nota = r.getAttribute('data-nota') || '';
                const nama = r.getAttribute('data-nama') || '';
                if (term === '' || nota.includes(term) || nama.includes(term)) {
                    r.style.display = '';
                    visibleCount++;
                } else {
                    r.style.display = 'none';
                }
            });

            const emptyTableRow = document.getElementById('emptyTableRow');
            const searchNotFoundRow = document.getElementById('searchNotFoundRow');

            if (emptyTableRow) {
                emptyTableRow.style.display = (rows.length === 0) ? '' : 'none';
            }

            if (searchNotFoundRow) {
                if (rows.length > 0 && term !== '' && visibleCount === 0) {
                    searchNotFoundRow.style.display = '';
                    const notFoundText = searchNotFoundRow.querySelector('.not-found-text');
                    if (notFoundText) {
                        notFoundText.textContent = `Nomor nota atau nama pelanggan "${query}" tidak ditemukan.`;
                    }
                } else {
                    searchNotFoundRow.style.display = 'none';
                }
            }
        }

        // Helper: Number Formatter
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num));
        }

        // Helper: Toast Message
        function showToast(msg) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'bg-slate-900 text-white text-xs px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transform transition-all duration-300 translate-y-2 opacity-0 pointer-events-auto';
            toast.innerHTML = `<span>ℹ️</span><span>${msg}</span>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
