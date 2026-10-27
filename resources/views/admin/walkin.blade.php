<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir POS Walk-in - Matahari Fotocopy</title>
    
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
                    id="searchPosInput"
                    placeholder="Cari no. antrean, nota, nama..." 
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition placeholder:text-slate-400 font-medium"
                    onkeyup="filterPosTransactions(this.value)"
                >
            </div>

            <!-- Topbar Badges & Quick Toggles -->
            <div class="flex items-center gap-3 flex-wrap justify-end w-full sm:w-auto">
                
                <!-- Demo Toggle Buttons -->
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs">
                    <form action="{{ route('admin.walkin.seed-demo') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 transition flex items-center gap-1">
                            <span>⚡ Isi Demo 34 Trx</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.walkin.clear-demo') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-600 hover:bg-white hover:text-slate-800 transition flex items-center gap-1">
                            <span>Kosongkan</span>
                        </button>
                    </form>
                </div>

                <!-- Shift Capsule -->
                <div class="hidden md:flex items-center gap-2 bg-amber-50 border border-amber-200/80 px-3 py-1.5 rounded-full text-xs font-semibold text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
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
                <button class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" onclick="showToast('Semua notifikasi transaksi telah sinkron real-time.')">
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
        <!-- POS WALK-IN HEADER & ACTION STRIP      -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 pt-6 pb-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- Title & Pills -->
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kasir POS Walk-in</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Loket A05 • Kasir Buka
                        </span>
                        <span id="headerNotaBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-blue-100 text-blue-800 border border-blue-200">
                            No. Nota {{ $nextNotaCode }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        Penjualan langsung: ATK, fotokopi kilat, jilid, materai, map, & cetak dokumen walk-in
                    </p>
                </div>

                <!-- Info Strip & Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Dynamic Omset Loket Pill -->
                    <div class="bg-white border border-slate-200/90 shadow-2xs rounded-xl px-3.5 py-2 flex items-center gap-2 text-xs font-bold text-slate-800">
                        <span class="text-base">💵</span>
                        <span>Omset Loket: <strong id="headerOmsetVal" class="text-slate-900">Rp {{ number_format($todayOmset, 0, ',', '.') }}</strong> <span id="headerCountVal" class="text-slate-500 font-normal">({{ $todayCount }} Transaksi)</span></span>
                    </div>

                    <!-- Open Cash Drawer Button -->
                    <button type="button" onclick="triggerOpenDrawer()" class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Buka Laci Kasir (F12)</span>
                    </button>

                    <!-- Clear Current Cart Button -->
                    <button type="button" onclick="clearCurrentCart()" class="text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100/80 border border-red-200 font-semibold px-3 py-2 rounded-xl text-xs flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Kosongkan Nota</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ====================================== -->
        <!-- MAIN TWO-COLUMN WORKSPACE              -->
        <!-- ====================================== -->
        <div class="px-4 md:px-8 pb-10 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ================================== -->
            <!-- LEFT 8 COLS: CATALOG & TRX TABLE   -->
            <!-- ================================== -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- Category Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                    @foreach($categories as $index => $cat)
                    <button 
                        type="button" 
                        onclick="filterCategory('{{ $cat }}', this)" 
                        class="category-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $index === 0 ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
                    >
                        @if($cat === 'Semua Item')
                            <span>🔲</span>
                        @elseif($cat === 'Fotokopi & Print')
                            <span>🖨️</span>
                        @elseif($cat === 'Alat Tulis & Map')
                            <span>✏️</span>
                        @elseif($cat === 'Jilid & Finishing')
                            <span>📑</span>
                        @else
                            <span>•••</span>
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
                        <span class="text-[11px] text-slate-400 font-mono">{{ $catalogProducts->count() }} Item Tersedia</span>
                    </div>

                    <div id="catalogGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 max-h-56 overflow-y-auto pr-1">
                        @foreach($catalogProducts as $prod)
                        <div 
                            onclick="addItemToCart('{{ addslashes($prod->nama_item) }}', {{ (float)$prod->harga_dasar }}, '{{ $prod->kategori }}', {{ $prod->produk_id }})"
                            data-category="{{ $prod->kategori }}"
                            class="catalog-item-card p-2.5 rounded-xl border border-slate-200/90 hover:border-amber-400 hover:bg-amber-50/40 bg-slate-50/40 cursor-pointer transition transform active:scale-95 flex flex-col justify-between group"
                        >
                            <div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                    <span class="truncate">{{ $prod->kategori }}</span>
                                    <span class="text-amber-600 font-bold opacity-0 group-hover:opacity-100 transition">+ Nota</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-amber-900">
                                    {{ $prod->nama_item }}
                                </h4>
                            </div>
                            <div class="mt-2 pt-1 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-slate-900 group-hover:text-amber-700">
                                    Rp {{ number_format($prod->harga_dasar, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] px-1 rounded bg-slate-200/60 text-slate-600">+</span>
                            </div>
                        </div>
                        @endforeach
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
                                <tr class="hover:bg-slate-50/60 transition group font-medium text-slate-800">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================================== -->
            <!-- RIGHT 4 COLS: NOTA POS REGISTER    -->
            <!-- ================================== -->
            <div class="lg:col-span-4 sticky top-20">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-5 space-y-4">
                    
                    <!-- Cart Card Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                👜
                            </div>
                            <div>
                                <h3 id="cartNotaTitle" class="text-sm font-extrabold text-slate-900 font-mono">
                                    Nota POS Walk-in {{ $nextNotaCode }}
                                </h3>
                                <p id="cartCustomerSubtitle" class="text-[11px] text-slate-500">
                                    Pelanggan: <span id="cartCustomerNameText">Pelanggan Umum (Walk-in)</span>
                                </p>
                            </div>
                        </div>
                        <span id="cartItemsCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white font-mono">
                            0 Item
                        </span>
                    </div>

                    <!-- Customer Selector Capsule -->
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                👤
                            </div>
                            <div class="min-w-0">
                                <h4 id="currentCustomerName" class="text-xs font-bold text-slate-900 truncate">
                                    Pelanggan Umum
                                </h4>
                                <span id="currentCustomerBadge" class="text-[10px] font-bold text-slate-600 bg-slate-200 px-1.5 py-0.2 rounded font-mono inline-block mt-0.5">
                                    Tamu Reguler / Non-Member
                                </span>
                            </div>
                        </div>
                        <button type="button" onclick="openCustomerModal()" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-2.5 py-1.5 rounded-lg shadow-2xs transition">
                            Ganti
                        </button>
                    </div>

                    <!-- Cart Item Rows Container -->
                    <div id="cartItemsList" class="space-y-2.5 max-h-64 overflow-y-auto pr-1 divide-y divide-slate-100">
                        <!-- Populated by JavaScript -->
                        <div id="cartEmptyPlaceholder" class="p-8 text-center text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-xs font-bold text-slate-600">Keranjang Nota Masih Kosong</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Klik item pada katalog layanan di samping untuk menambahkan ke nota ini.</p>
                        </div>
                    </div>

                    <!-- Summary & Discount -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-500 font-medium">
                            <span id="subtotalItemLabel">Subtotal Item (0 item)</span>
                            <span id="subtotalValText" class="font-mono text-slate-800 font-bold">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-amber-700 font-bold">
                            <span class="flex items-center gap-1">
                                <span>🏷️</span>
                                <span>Diskon Member Matahari (Point Tier)</span>
                            </span>
                            <span id="diskonValText" class="font-mono">- Rp 0</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 uppercase tracking-tight">Total Tagihan Kasir</span>
                            <span id="totalTagihanValText" class="text-2xl font-black font-mono text-slate-900">Rp 0</span>
                        </div>
                    </div>

                    <!-- Payment Method Quick Selector -->
                    <div class="pt-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Pilihan Cepat Metode Pembayaran
                        </label>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" onclick="selectPaymentMethod('QRIS', this)" class="payment-method-btn active py-2 rounded-xl border border-sky-300 bg-sky-50 text-sky-800 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">📱</span>
                                <span class="text-[10px] font-mono">QRIS</span>
                            </button>
                            <button type="button" onclick="selectPaymentMethod('TUNAI', this)" class="payment-method-btn py-2 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">💵</span>
                                <span class="text-[10px] font-mono">Tunai</span>
                            </button>
                            <button type="button" onclick="selectPaymentMethod('EDC', this)" class="payment-method-btn py-2 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">💳</span>
                                <span class="text-[10px] font-mono">EDC</span>
                            </button>
                            <button type="button" onclick="selectPaymentMethod('TRANSFER', this)" class="payment-method-btn py-2 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition">
                                <span class="text-xs">🏦</span>
                                <span class="text-[10px] font-mono">Transfer</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cash Tender / Uang Diterima -->
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
                            <span>UANG DITERIMA (PECAHAN PAS / BULAT)</span>
                            <span class="text-amber-700 font-mono font-bold" id="kembalianText">Kembalian: Rp 0</span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5 text-xs">
                            <button type="button" onclick="setTenderAmount('pas')" id="tenderPasBtn" class="tender-btn py-1.5 px-1 rounded-lg border border-slate-200 bg-white font-bold text-slate-800 hover:bg-slate-100 text-[11px] truncate">
                                Rp Pas
                            </button>
                            <button type="button" onclick="setTenderAmount(50000)" class="tender-btn py-1.5 px-1 rounded-lg border border-amber-400 bg-amber-500 text-white font-bold text-[11px] truncate">
                                Rp 50.000
                            </button>
                            <button type="button" onclick="setTenderAmount(100000)" class="tender-btn py-1.5 px-1 rounded-lg border border-slate-200 bg-white font-bold text-slate-800 hover:bg-slate-100 text-[11px] truncate">
                                Rp 100.000
                            </button>
                            <button type="button" onclick="promptCustomTender()" class="tender-btn py-1.5 px-1 rounded-lg border border-slate-200 bg-white font-bold text-slate-800 hover:bg-slate-100 text-[11px] truncate">
                                Custom...
                            </button>
                        </div>
                    </div>

                    <!-- Primary Checkout Button -->
                    <button 
                        type="button" 
                        id="checkoutReviewBtn"
                        onclick="openReviewModal()" 
                        class="w-full py-3.5 px-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-md transition active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Selesaikan Transaksi (Enter)</span>
                    </button>

                    <!-- Secondary Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" onclick="sendReceiptWhatsapp()" class="py-2.5 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.769.814 2.791.814 3.179 0 5.769-2.587 5.769-5.766.001-3.18-2.587-5.766-5.769-5.766zm9.969 5.766c0 5.514-4.486 10-10 10-1.823 0-3.535-.494-5.011-1.353l-5.989 1.567 1.6-5.834c-.958-1.528-1.5-3.329-1.5-5.38 0-5.514 4.486-10 10-10s10 4.486 10 10z"/>
                            </svg>
                            <span>Nota via WhatsApp</span>
                        </button>
                        <button type="button" onclick="holdCurrentCart()" class="py-2.5 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Tahan Nota (Pending)</span>
                        </button>
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
                        <span id="rcptMetode">Metode (QRIS):</span>
                        <span id="rcptTender">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Kembalian:</span>
                        <span id="rcptKembalian">Rp 0</span>
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
        let currentTender = 50000;
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
                    <div id="cartEmptyPlaceholder" class="p-8 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-xs font-bold text-slate-600">Keranjang Nota Masih Kosong</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Klik item pada katalog layanan di samping untuk menambahkan ke nota ini.</p>
                    </div>
                `;
                if (itemsCountBadge) itemsCountBadge.textContent = '0 Item';
                if (subtotalLabel) subtotalLabel.textContent = 'Subtotal Item (0 item)';
                if (subtotalText) subtotalText.textContent = 'Rp 0';
                if (diskonText) diskonText.textContent = '- Rp 0';
                if (totalText) totalText.textContent = 'Rp 0';
                if (submitBtn) submitBtn.disabled = true;
                updateKembalian(0);
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
                            <!-- Stepper -->
                            <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50 overflow-hidden font-mono text-xs">
                                <button type="button" onclick="updateItemQty(${idx}, -1)" class="px-2 py-0.5 hover:bg-slate-200 text-slate-600 transition">-</button>
                                <span class="px-2 font-bold text-slate-800">${item.kuantitas}</span>
                                <button type="button" onclick="updateItemQty(${idx}, 1)" class="px-2 py-0.5 hover:bg-slate-200 text-slate-600 transition">+</button>
                            </div>
                            <!-- Price & Delete -->
                            <span class="font-mono font-bold text-xs text-slate-900 w-20 text-right">
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

            // Update tender button for "Rp Pas"
            const tenderPasBtn = document.getElementById('tenderPasBtn');
            if (tenderPasBtn) {
                tenderPasBtn.textContent = `Rp ${formatNumber(finalTotal)} (Pas)`;
            }

            updateKembalian(finalTotal);
        }

        // Set Tender Amount
        function setTenderAmount(amount) {
            const subtotal = cart.reduce((acc, it) => acc + it.sub_total, 0);
            const rawDiscount = currentCustomer.discount || 0;
            const appliedDiscount = Math.min(rawDiscount, subtotal);
            const finalTotal = Math.max(0, subtotal - appliedDiscount);

            if (amount === 'pas') {
                currentTender = finalTotal;
            } else {
                currentTender = parseFloat(amount) || finalTotal;
            }

            // Update tender active styling
            document.querySelectorAll('.tender-btn').forEach(btn => {
                btn.classList.remove('bg-amber-500', 'text-white', 'border-amber-400');
                btn.classList.add('bg-white', 'text-slate-800', 'border-slate-200');
            });
            if (window.event && window.event.target && window.event.target.classList) {
                const targetBtn = window.event.target.closest('.tender-btn');
                if (targetBtn) {
                    targetBtn.classList.add('bg-amber-500', 'text-white', 'border-amber-400');
                    targetBtn.classList.remove('bg-white', 'text-slate-800', 'border-slate-200');
                }
            }

            updateKembalian(finalTotal);
        }

        function promptCustomTender() {
            const val = prompt('Masukkan nominal uang yang diterima:', currentTender);
            if (val !== null && !isNaN(val)) {
                setTenderAmount(parseFloat(val));
            }
        }

        function updateKembalian(finalTotal) {
            const kembalianEl = document.getElementById('kembalianText');
            const change = Math.max(0, currentTender - finalTotal);
            kembalianEl.textContent = `Kembalian: Rp ${formatNumber(change)}`;
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

        // Filter Category in Catalog Grid
        function filterCategory(cat, btn) {
            document.querySelectorAll('.category-filter-btn').forEach(b => {
                b.classList.remove('bg-slate-900', 'text-white', 'shadow-xs');
                b.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            });
            btn.classList.add('bg-slate-900', 'text-white', 'shadow-xs');
            btn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');

            const cards = document.querySelectorAll('.catalog-item-card');
            cards.forEach(card => {
                const itemCat = card.getAttribute('data-category');
                if (cat === 'Semua Item' || itemCat === cat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
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
            const change = Math.max(0, currentTender - finalTotal);

            const now = new Date();
            const timeStr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')} WIB`;

            document.getElementById('rcptNotaCode').textContent = nextNota;
            document.getElementById('rcptTime').textContent = timeStr;
            document.getElementById('rcptCustomer').textContent = currentCustomer.name;
            document.getElementById('rcptSubtotal').textContent = `Rp ${formatNumber(subtotal)}`;
            document.getElementById('rcptDiskon').textContent = `- Rp ${formatNumber(appliedDiscount)}`;
            document.getElementById('rcptTotal').textContent = `Rp ${formatNumber(finalTotal)}`;
            document.getElementById('rcptMetode').textContent = `Metode (${selectedPayment}):`;
            document.getElementById('rcptTender').textContent = `Rp ${formatNumber(currentTender)}`;
            document.getElementById('rcptKembalian').textContent = `Rp ${formatNumber(change)}`;

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
                uang_diterima: currentTender,
                kembalian: Math.max(0, currentTender - finalTotal)
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
                document.getElementById('headerOmsetVal').textContent = data.todayOmsetFormatted;
                document.getElementById('headerCountVal').textContent = `(${data.todayCount} Transaksi)`;
                document.getElementById('headerNotaBadge').textContent = `No. Nota ${data.nextNotaCode}`;
                document.getElementById('cartNotaTitle').textContent = `Nota POS Walk-in ${data.nextNotaCode}`;
                nextNota = data.nextNotaCode;
                document.getElementById('trxTableCountBadge').textContent = `${data.todayCount} Transaksi Selesai • Real-time`;

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
                            <tr class="hover:bg-slate-50/60 transition group font-medium text-slate-800">
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
                    tbody.innerHTML = rowsHtml;
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

        // Table search filter
        function filterPosTransactions(query) {
            const term = query.toLowerCase().trim();
            const rows = document.querySelectorAll('#walkInTableBody tr');
            rows.forEach(r => {
                if (r.id === 'emptyTableRow') return;
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(term) ? '' : 'none';
            });
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
