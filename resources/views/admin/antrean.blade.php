<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembelian Online - Matahari Fotocopy</title>
    
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
                        sidebar: {
                            DEFAULT: '#dce8f7',
                        },
                        amberbrand: {
                            500: '#f59e0b',
                            600: '#ea580c',
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
        @media print {
            aside, header, .no-print { display: none !important; }
            main { padding: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- ========================================== -->
    <!-- LEFT SIDEBAR                               -->
    <!-- ========================================== -->
    @include('admin.partials.sidebar', ['active' => 'antrean', 'activeAntreanCount' => $workshopData['total']])


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
                <input type="text" id="header-search-input" onkeyup="filterQueueClientSide()" placeholder="Cari no. antrean, nota, nama..." class="w-full pl-10 pr-4 py-2 text-xs bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-slate-200/80 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-slate-800 placeholder-slate-400">
            </div>

            <!-- Right Info & Actions -->
            <div class="flex items-center gap-3 sm:gap-4">
                


                <!-- Live Clock & Date -->
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200/60 font-mono">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="live-clock">14:32 WIB • 24 Okt</span>
                </div>

                <!-- Notification Bell -->
                <div class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition relative cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </div>

                <!-- User Profile Avatar (Warm Brown/Caramel Circle) -->
                <div class="w-8 h-8 rounded-full bg-[#92400e] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <svg class="w-4 h-4 text-amber-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>

            </div>

        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

            <!-- PAGE SUB-HEADER TITLE & VIEW TOGGLES -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider font-mono bg-amber-100 text-amber-800 border border-amber-200">
                            LOKET DIGITAL GERAI
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Pembelian Online
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        Manajemen Antrean Online (Printing Workshop) &amp; Pembelian Retail ATK Gerai
                    </p>
                </div>

                <!-- Right View Mode & Print Buttons -->
                <div class="flex items-center gap-2 sm:gap-2.5 flex-nowrap shrink-0">
                    
                    <!-- View Mode Toggle Capsule (Khusus Printing) -->
                    <div id="printing-view-toggles" class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 border border-slate-200/80 text-xs shrink-0">
                        <button type="button" id="btn-view-table" onclick="setViewMode('table')" class="px-3.5 py-1.5 rounded-lg bg-white shadow-2xs font-bold text-slate-800 flex items-center gap-1.5 transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Tampilan Tabel</span>
                        </button>
                        <button type="button" id="btn-view-kanban" onclick="setViewMode('kanban')" class="px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                            <span>Papan Kanban</span>
                        </button>
                    </div>

                    <!-- Print Button -->
                    <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-blue-50/80 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200/80 shadow-2xs flex items-center gap-1.5 transition whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak</span>
                    </button>

                </div>

            </div>

            <!-- ========================================== -->
            <!-- DUA TABEL PEMBELIAN ONLINE: PRINT VS ATK  -->
            <!-- ========================================== -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5 overflow-x-auto scrollbar-none">
                <button type="button" id="tab-btn-printing" onclick="switchOnlineTab('printing')" class="px-5 py-2.5 rounded-2xl font-black text-xs transition flex items-center gap-2.5 bg-[#f59e0b] text-white shadow-sm active:scale-95 shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>1. Pembelian Khusus Printing (Antrean Workshop)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/25 text-white">
                        {{ $workshopData['total'] ?? 0 }}
                    </span>
                </button>

                <button type="button" id="tab-btn-atk" onclick="switchOnlineTab('atk')" class="px-5 py-2.5 rounded-2xl font-bold text-xs transition flex items-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs active:scale-95 shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>2. Pembelian Khusus ATK (Alat Tulis Kantor)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-100 text-blue-700">
                        {{ $activeAtkCount ?? 0 }}
                    </span>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- SECTION 1: TABEL PEMBELIAN KHUSUS PRINTING (EXISTING)   -->
            <!-- ======================================================== -->
            <div id="section-printing" class="space-y-6">


            <!-- ========================================== -->
            <!-- FILTER PILLS ROW                           -->
            <!-- ========================================== -->
            <div class="bg-white rounded-2xl p-2.5 sm:p-3 border border-slate-200/90 shadow-2xs flex items-center gap-1.5 text-xs font-semibold overflow-x-auto scrollbar-none whitespace-nowrap">
                <a href="{{ route('admin.antrean.index', ['tab' => 'masuk']) }}" class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ in_array($filter, ['masuk', 'semua']) ? 'bg-amber-50 text-amber-800 font-bold border border-amber-300 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>Order Masuk</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ in_array($filter, ['masuk', 'semua']) ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $workshopData['masuk'] ?? 0 }}</span>
                </a>

                <a href="{{ route('admin.antrean.index', ['tab' => 'diterima']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $filter === 'diterima' ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>Diterima</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'diterima' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $workshopData['diterima'] }}</span>
                </a>

                <a href="{{ route('admin.antrean.index', ['tab' => 'proses']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $filter === 'proses' ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>Sedang Diproses</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'proses' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $workshopData['proses'] }}</span>
                </a>

                <a href="{{ route('admin.antrean.index', ['tab' => 'siap']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $filter === 'siap' ? 'bg-blue-50 text-blue-700 font-bold border border-blue-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>Siap Diambil</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'siap' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $workshopData['siap'] }}</span>
                </a>

                <a href="{{ route('admin.antrean.index', ['tab' => 'selesai']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shrink-0 {{ $filter === 'selesai' ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-300 shadow-2xs' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>Selesai</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filter === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $workshopData['selesai'] ?? 0 }}</span>
                </a>
            </div>

            <!-- ========================================== -->
            <!-- TABLE VIEW: MANAJEMEN ANTREAN ONLINE       -->
            <!-- ========================================== -->
            <div id="view-table-container" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-xs" id="antrean-table">
                        <thead class="bg-[#f0f6ff]/70 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3.5 px-4">ANTREAN</th>
                                <th class="py-3.5 px-4">NO. NOTA</th>
                                <th class="py-3.5 px-4">PELANGGAN</th>
                                <th class="py-3.5 px-4">DOKUMEN & SPESIFIKASI</th>
                                <th class="py-3.5 px-4">MESIN / OPERATOR</th>
                                <th class="py-3.5 px-4">TENGGAT</th>
                                <th class="py-3.5 px-4">PEMBAYARAN</th>
                                <th class="py-3.5 px-4">STATUS ALUR</th>
                                <th class="py-3.5 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="antrean-table-body">
                            @forelse($antreanList as $q)
                                @php
                                    $pesanan = $q->pesanan;
                                    $badgeColor = $q->badge_alur_color;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition antrean-row" data-search="{{ strtolower($q->kode_antrean . ' ' . ($pesanan?->nomor_nota ?? '') . ' ' . ($pesanan?->display_name ?? '') . ' ' . ($pesanan?->display_judul ?? '')) }}">
                                    
                                    <!-- ANTREAN -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold font-mono text-xs shadow-2xs {{ $loop->first ? 'bg-[#f59e0b] text-white' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                            {{ $q->kode_antrean }}
                                        </span>
                                    </td>

                                    <!-- NO. NOTA -->
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#b45309]">
                                        {{ $pesanan?->nomor_nota ?? '#MF-'.str_pad($q->pesanan_id, 3, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- PELANGGAN -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-extrabold text-slate-900 leading-tight">
                                            {{ $pesanan?->display_name ?? 'Pelanggan' }}
                                        </div>
                                        @php $tipe = trim($pesanan?->display_tipe ?? ''); @endphp
                                        @if(!empty($tipe) && !str_contains($tipe, 'Order Web'))
                                            <div class="mt-1">
                                                @if(str_contains($tipe, 'Platinum') || str_contains($tipe, 'Gold') || str_contains($tipe, 'Member'))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        {{ $tipe }}
                                                    </span>
                                                @elseif(str_contains($tipe, 'STAN') || str_contains($tipe, 'Mahasiswa'))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                        {{ $tipe }}
                                                    </span>
                                                @elseif(str_contains($tipe, 'Reguler'))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        {{ $tipe }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                                        {{ $tipe }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- DOKUMEN & SPESIFIKASI -->
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="font-bold text-slate-800 truncate">
                                            {{ $pesanan?->display_judul ?? 'Dokumen Cetak' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate mt-0.5">
                                            {{ $pesanan?->display_spesifikasi ?? 'A4 HVS Standard' }}
                                        </div>
                                    </td>

                                    <!-- MESIN / OPERATOR -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 leading-tight">
                                            {{ $q->nama_mesin ?? 'Mesin Konica 1' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $q->nama_operator ?? 'Operator: Feri' }}
                                        </div>
                                    </td>

                                    <!-- TENGGAT -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono font-bold leading-tight {{ str_contains($q->keterangan_tenggat ?? '', 'Mendesak') ? 'text-rose-600' : 'text-slate-800' }}">
                                            {{ $pesanan?->display_tenggat ?? '15:30 WIB' }}
                                        </div>
                                        <div class="text-[10px] font-medium mt-0.5 {{ str_contains($q->keterangan_tenggat ?? '', 'Mendesak') ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                                            {{ $q->keterangan_tenggat ?? 'Sesuai Jadwal' }}
                                        </div>
                                    </td>

                                    <!-- PEMBAYARAN -->
                                    <td class="py-3.5 px-4">
                                        @if(($q->status_pembayaran ?? 'lunas') === 'belum_bayar')
                                            <div>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                                    Belum Bayar
                                                </span>
                                                <span class="block font-mono font-bold text-slate-900 mt-1">
                                                    Rp {{ number_format($pesanan?->total_harga ?? 180000, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @else
                                            <div>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    ✓ Lunas
                                                </span>
                                                <span class="block text-[11px] font-medium text-slate-500 mt-0.5">
                                                    {{ strtoupper($pesanan?->metode_pembayaran ?? 'QRIS') }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- STATUS ALUR -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badgeColor['bg'] }} {{ $badgeColor['text'] }} border {{ $badgeColor['border'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $badgeColor['dot'] }}"></span>
                                            <span>{{ $q->status_alur ?? 'Diterima' }}</span>
                                        </span>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="py-3.5 px-4 text-center">
                                        @php
                                            $alur = strtolower($q->status_alur ?? 'menunggu');
                                            $statusAntrean = strtolower($q->status_antrean ?? 'menunggu');
                                            $berkasDok = $pesanan?->detailPesanan?->firstWhere('berkas_id', '!=', null)?->berkasDokumen;
                                            $isSelesai = in_array($alur, ['selesai']) || ($statusAntrean === 'selesai') || ($pesanan?->status_pesanan === 'selesai');
                                            $isSiap = in_array($alur, ['siap diambil', 'siap', 'siap_pickup']) || ($statusAntrean === 'siap');
                                            $isSedangDiproses = in_array($alur, ['sedang diproses', 'sedang cetak', 'sedang jilid', 'cetak', 'salin', 'finishing', 'proses']) || in_array($statusAntrean, ['cetak', 'salin', 'finishing']);
                                            $isDiterima = in_array($alur, ['diterima']) || ($statusAntrean === 'diterima');
                                        @endphp
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            @if($berkasDok && !$isSiap && !$isSelesai)
                                                <a href="{{ route('admin.berkas.download', $berkasDok->berkas_id) }}" title="Unduh Berkas Asli ({{ $berkasDok->nama_file }})" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs">
                                                    <span>⬇</span>
                                                    <span>Unduh</span>
                                                </a>
                                            @endif

                                            @if($isSelesai)
                                                <span class="text-xs font-semibold text-slate-400">—</span>
                                            @elseif($isSiap)
                                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'selesai')" title="Tandai pesanan Selesai / Diserahkan" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-2xs transition active:scale-95 flex items-center gap-1">
                                                    <span>✓</span>
                                                    <span>Selesai</span>
                                                </button>
                                            @elseif($isSedangDiproses)
                                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'siap')" title="Ubah status menjadi Siap Diambil" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-2xs transition active:scale-95 flex items-center gap-1">
                                                    <span>📦</span>
                                                    <span>Siap Diambil</span>
                                                </button>
                                            @elseif($isDiterima)
                                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'proses')" title="Ubah status antrean menjadi Sedang Diproses" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-2xs transition active:scale-95 flex items-center gap-1">
                                                    <span>⚙️</span>
                                                    <span>Proses</span>
                                                </button>
                                            @else
                                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'terima')" title="Terima pesanan antrean" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-2xs transition active:scale-95 flex items-center gap-1">
                                                    <span>✓</span>
                                                    <span>Terima</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-state-antrean">
                                    <td colspan="9" class="py-16 px-4 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-md mx-auto space-y-3">
                                            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-xs">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-extrabold text-slate-800 text-base">Belum Ada Antrean Online Berjalan</h4>
                                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                                    Sistem antrean online terintegrasi secara otomatis. Setiap pesanan baru dari pelanggan (Online via Website) akan langsung masuk ke antrean online ini secara real-time.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Counter Footer -->
                <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Menampilkan <strong class="text-slate-800 font-mono">{{ $antreanList->firstItem() ?? 0 }}-{{ $antreanList->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-800 font-mono">{{ $antreanList->total() }}</strong> antrean
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center gap-1">
                        @if($antreanList->onFirstPage())
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center font-bold">‹</span>
                        @else
                            <a href="{{ $antreanList->previousPageUrl() }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">‹</a>
                        @endif

                        @for ($i = 1; $i <= max(1, $antreanList->lastPage()); $i++)
                            @if ($i == $antreanList->currentPage())
                                <span class="w-7 h-7 rounded-lg bg-[#f59e0b] text-white font-bold flex items-center justify-center shadow-2xs font-mono">{{ $i }}</span>
                            @else
                                <a href="{{ $antreanList->url($i) }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition font-mono">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($antreanList->hasMorePages())
                            <a href="{{ $antreanList->nextPageUrl() }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold transition">›</a>
                        @else
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center font-bold">›</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KANBAN BOARD VIEW (OPTIONAL ALTERNATIVE)   -->
            <!-- ========================================== -->
            <div id="view-kanban-container" class="hidden grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Col 1: Diterima -->
                <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-xs text-slate-700">
                        <span>DITERIMA / ORDER MASUK</span>
                        <span class="bg-slate-200 px-2 py-0.5 rounded-full text-[10px] font-mono">{{ $workshopData['diterima'] }}</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($antreanList->where('status_alur', 'Diterima') as $q)
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs space-y-1.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-blue-700 font-mono">{{ $q->kode_antrean }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $q->pesanan?->nomor_nota }}</span>
                                </div>
                                <h5 class="font-bold text-slate-800 leading-tight">{{ $q->pesanan?->display_name }}</h5>
                                <p class="text-[11px] text-slate-500 truncate">{{ $q->pesanan?->display_judul }}</p>
                                <div class="grid grid-cols-2 gap-1.5 mt-2">
                                    <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'proses_cetak')" class="py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold transition">
                                        Proses Cetak
                                    </button>
                                    <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'selesai')" class="py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs italic">Tidak ada order menunggu</div>
                        @endforelse
                    </div>
                </div>

                <!-- Col 2: Sedang Cetak -->
                <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-200/60 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-blue-200 font-bold text-xs text-blue-900">
                        <span>SEDANG CETAK</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-[10px] font-mono">Mesin Aktif</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($antreanList->where('status_alur', 'Sedang Cetak') as $q)
                            <div class="bg-white p-3.5 rounded-xl border border-blue-200 shadow-2xs space-y-1.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-blue-700 font-mono">{{ $q->kode_antrean }}</span>
                                    <span class="text-[10px] text-rose-500 font-bold font-mono">{{ $q->keterangan_tenggat }}</span>
                                </div>
                                <h5 class="font-bold text-slate-800 leading-tight">{{ $q->pesanan?->display_name }}</h5>
                                <p class="text-[11px] text-slate-500 truncate">{{ $q->nama_mesin }} • {{ $q->nama_operator }}</p>
                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'selesai')" class="mt-2 w-full py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition">
                                    Selesai Cetak →
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs italic">Mesin sedang standby</div>
                        @endforelse
                    </div>
                </div>

                <!-- Col 3: Finishing / Jilid -->
                <div class="bg-amber-50/50 rounded-2xl p-4 border border-amber-200/60 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-amber-200 font-bold text-xs text-amber-900">
                        <span>FINISHING / JILID</span>
                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-[10px] font-mono">Meja Jilid</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($antreanList->where('status_alur', 'Sedang Jilid') as $q)
                            <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-2xs space-y-1.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-amber-700 font-mono">{{ $q->kode_antrean }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $q->pesanan?->nomor_nota }}</span>
                                </div>
                                <h5 class="font-bold text-slate-800 leading-tight">{{ $q->pesanan?->display_name }}</h5>
                                <p class="text-[11px] text-slate-500 truncate">{{ $q->pesanan?->display_spesifikasi }}</p>
                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'selesai')" class="mt-2 w-full py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-[11px] font-bold transition">
                                    Siap Rak Loket →
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs italic">Tidak ada finishing aktif</div>
                        @endforelse
                    </div>
                </div>

                <!-- Col 4: Siap Diambil -->
                <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-200/60 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-emerald-200 font-bold text-xs text-emerald-900">
                        <span>SIAP DI RAK LOKET</span>
                        <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-mono">{{ $workshopData['siap'] }}</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($antreanList->where('status_alur', 'Siap Diambil') as $q)
                            <div class="bg-white p-3.5 rounded-xl border border-emerald-200 shadow-2xs space-y-1.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-emerald-700 font-mono">{{ $q->kode_antrean }}</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Siap Pickup</span>
                                </div>
                                <h5 class="font-bold text-slate-800 leading-tight">{{ $q->pesanan?->display_name }}</h5>
                                <p class="text-[11px] text-slate-500 truncate">{{ $q->pesanan?->display_judul }}</p>
                                <button type="button" onclick="actionAntrean({{ $q->antrean_id }}, 'serahkan')" class="mt-2 w-full py-1.5 rounded-lg bg-[#78350f] hover:bg-slate-900 text-white text-[11px] font-bold transition">
                                    Serahkan ke Pelanggan ✓
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs italic">Rak loket kosong</div>
                        @endforelse
                    </div>
                </div>

            </div>
            <!-- End of #section-printing -->
            </div>

            <!-- ======================================================== -->
            <!-- SECTION 2: TABEL PEMBELIAN KHUSUS ATK (RETAIL)          -->
            <!-- ======================================================== -->
            <div id="section-atk" class="space-y-4 hidden">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    
                    <!-- Table Header Card -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-blue-50/60 to-indigo-50/40">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                🛍️
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">Tabel Pembelian Retail ATK Online</h3>
                                <p class="text-xs text-slate-500">Daftar pesanan perlengkapan alat tulis kantor dari keranjang online pelanggan. Siap disiapkan dan diserahkan di loket kasir.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-xl bg-white border border-blue-200 text-blue-700 text-xs font-bold font-mono shadow-2xs">
                                Total: {{ $atkOrdersList->count() }} Pesanan
                            </span>
                        </div>
                    </div>

                    <!-- ATK Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-xs" id="atk-table">
                            <thead class="bg-[#f0f6ff]/70 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3.5 px-4">TIKET &amp; NOTA</th>
                                    <th class="py-3.5 px-4">PELANGGAN</th>
                                    <th class="py-3.5 px-4">DAFTAR ITEM ATK &amp; CATATAN</th>
                                    <th class="py-3.5 px-4">TOTAL BELANJA</th>
                                    <th class="py-3.5 px-4">PEMBAYARAN</th>
                                    <th class="py-3.5 px-4">STATUS PENGAMBILAN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="atk-table-body">
                                @forelse($atkOrdersList as $atk)
                                    @php
                                        $pesanan = $atk->pesanan;
                                        $isSelesai = in_array(strtolower($atk->status_alur ?? $atk->status_antrean ?? ''), ['selesai'], true) || ($pesanan?->status_pesanan === 'selesai');
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition atk-row" data-search="{{ strtolower($atk->kode_antrean . ' ' . ($pesanan?->nomor_nota ?? '') . ' ' . ($pesanan?->display_name ?? '') . ' ' . ($pesanan?->spesifikasi ?? '')) }}">
                                        
                                        <!-- TIKET & NOTA -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold font-mono text-xs bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                                    {{ $atk->kode_antrean }}
                                                </span>
                                                <span class="font-mono font-extrabold text-[#b45309]">
                                                    {{ $pesanan?->nomor_nota ?? '#MF-'.str_pad((string)$atk->pesanan_id, 3, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-1">
                                                {{ ($pesanan?->waktu_pesan ?? $atk->created_at)->translatedFormat('d M • H:i') }} WIB
                                            </div>
                                        </td>

                                        <!-- PELANGGAN -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-extrabold text-slate-900 leading-tight">
                                                {{ $pesanan?->display_name ?? 'Pelanggan Toko' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                                {{ $pesanan?->display_tipe ?? 'Member Pelanggan' }}
                                            </div>
                                        </td>

                                        <!-- DAFTAR ITEM ATK & CATATAN -->
                                        <td class="py-3.5 px-4 max-w-sm">
                                            <div class="font-bold text-slate-800 leading-snug">
                                                {{ $pesanan?->spesifikasi ?? $pesanan?->display_judul }}
                                            </div>
                                            <div class="text-[10px] text-blue-600 font-semibold mt-1 flex items-center gap-1">
                                                <span>🏪 Loket Kasir &amp; Rak ATK</span>
                                            </div>
                                        </td>

                                        <!-- TOTAL BELANJA -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="font-mono font-black text-blue-600 text-xs block">
                                                Rp {{ number_format($pesanan?->total_harga ?? 0, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $pesanan?->volume_halaman ?? 1 }} jenis barang
                                            </span>
                                        </td>

                                        <!-- PEMBAYARAN -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if(($atk->status_pembayaran ?? 'lunas') === 'belum_lunas' || strtolower($pesanan?->metode_pembayaran ?? '') === 'tunai')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Bayar di Kasir (Tunai)
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ✓ Lunas ({{ strtoupper($pesanan?->metode_pembayaran ?? 'QRIS') }})
                                                </span>
                                            @endif
                                        </td>

                                        <!-- STATUS PENGAMBILAN -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($isSelesai)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    <span>Sudah Diserahkan</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                                    <span>Siap Diambil di Loket</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-state-atk">
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-2">
                                                🛍️
                                            </div>
                                            <p class="font-bold text-slate-700 text-sm">Belum Ada Pembelian ATK Online</p>
                                            <p class="text-xs text-slate-400 mt-0.5">Pesanan ATK yang dibeli pelanggan melalui keranjang online akan muncul di tabel ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-t border-slate-200 py-3.5 px-6 text-center text-xs text-slate-400 flex flex-wrap items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} Matahari Fotocopy • Workshop Dispatch Control System</span>
            <span class="flex items-center gap-1.5 font-mono text-[11px] text-emerald-600 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Machine Sync: AKTIF (5s Polling)</span>
            </span>
        </footer>

    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>

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
        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'px-4 py-2.5 rounded-xl shadow-lg border border-emerald-500 bg-emerald-600 text-xs font-bold text-white transition-all transform duration-300 translate-y-2 opacity-0 pointer-events-auto flex items-center gap-2';
            toast.innerHTML = `<span>✓</span><span>${message}</span>`;
            container.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-y-2', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // View Mode Switcher
        function setViewMode(mode) {
            const tableContainer = document.getElementById('view-table-container');
            const kanbanContainer = document.getElementById('view-kanban-container');
            const btnTable = document.getElementById('btn-view-table');
            const btnKanban = document.getElementById('btn-view-kanban');

            if (mode === 'kanban') {
                tableContainer.classList.add('hidden');
                kanbanContainer.classList.remove('hidden');
                btnKanban.className = 'px-3.5 py-1.5 rounded-lg bg-white shadow-2xs font-bold text-slate-800 flex items-center gap-1.5 transition';
                btnTable.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 transition';
            } else {
                kanbanContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                btnTable.className = 'px-3.5 py-1.5 rounded-lg bg-white shadow-2xs font-bold text-slate-800 flex items-center gap-1.5 transition';
                btnKanban.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5 transition';
            }
        }

        // Online Purchase Tab Switcher (Printing vs ATK)
        function switchOnlineTab(tab) {
            const btnPrint = document.getElementById('tab-btn-printing');
            const btnAtk = document.getElementById('tab-btn-atk');
            const secPrint = document.getElementById('section-printing');
            const secAtk = document.getElementById('section-atk');
            const viewToggles = document.getElementById('printing-view-toggles');

            if (tab === 'atk') {
                if (btnPrint) btnPrint.className = "px-5 py-2.5 rounded-2xl font-bold text-xs transition flex items-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs active:scale-95 shrink-0";
                if (btnAtk) btnAtk.className = "px-5 py-2.5 rounded-2xl font-black text-xs transition flex items-center gap-2.5 bg-[#f59e0b] text-white shadow-sm active:scale-95 shrink-0";
                if (secPrint) secPrint.classList.add('hidden');
                if (secAtk) secAtk.classList.remove('hidden');
                if (viewToggles) viewToggles.classList.add('hidden');
            } else {
                if (btnPrint) btnPrint.className = "px-5 py-2.5 rounded-2xl font-black text-xs transition flex items-center gap-2.5 bg-[#f59e0b] text-white shadow-sm active:scale-95 shrink-0";
                if (btnAtk) btnAtk.className = "px-5 py-2.5 rounded-2xl font-bold text-xs transition flex items-center gap-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs active:scale-95 shrink-0";
                if (secPrint) secPrint.classList.remove('hidden');
                if (secAtk) secAtk.classList.add('hidden');
                if (viewToggles) viewToggles.classList.remove('hidden');
            }
        }

        // Check URL parameter on load
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sectionParam = urlParams.get('section') || window.location.hash.replace('#', '');
            if (sectionParam === 'atk') {
                switchOnlineTab('atk');
            }
        });

        // Client Side Filter on Both Antrean & ATK Tables
        function filterQueueClientSide() {
            const query = (document.getElementById('header-search-input')?.value || '').toLowerCase().trim();
            
            // Filter Printing table
            const printRows = document.querySelectorAll('#antrean-table-body tr.antrean-row');
            let printVisible = 0;
            printRows.forEach(r => {
                const text = r.getAttribute('data-search') || '';
                if (text.includes(query)) {
                    r.style.display = '';
                    printVisible++;
                } else {
                    r.style.display = 'none';
                }
            });
            const emptyPrintRow = document.getElementById('empty-state-antrean');
            if (emptyPrintRow && printRows.length > 0) {
                emptyPrintRow.style.display = (printVisible === 0 && query !== '') ? '' : 'none';
            }

            // Filter ATK table
            const atkRows = document.querySelectorAll('#atk-table-body tr.atk-row');
            let atkVisible = 0;
            atkRows.forEach(r => {
                const text = r.getAttribute('data-search') || '';
                if (text.includes(query)) {
                    r.style.display = '';
                    atkVisible++;
                } else {
                    r.style.display = 'none';
                }
            });
            const emptyAtkRow = document.getElementById('empty-state-atk');
            if (emptyAtkRow && atkRows.length > 0) {
                emptyAtkRow.style.display = (atkVisible === 0 && query !== '') ? '' : 'none';
            }
        }

        // Action Button Handler
        async function actionAntrean(antreanId, action) {
            try {
                const response = await fetch(`/admin/antrean/${antreanId}/action`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: action })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    showToast(data.message);
                    fetchAntreanLiveData();
                    setTimeout(() => window.location.reload(), 500);
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Real-Time Poller for Antrean Online
        async function fetchAntreanLiveData() {
            try {
                const res = await fetch("{{ route('admin.antrean.live-data') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;

                const data = await res.json();
                const workshop = data.workshopData;


                // Update sidebar badge
                const badge = document.getElementById('sidebar-queue-badge');
                if (badge) {
                    if (workshop && workshop.total > 0) {
                        badge.textContent = workshop.total;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

            } catch (err) {
                console.error("Antrean live poll error:", err);
            }
        }

        // Start live polling every 5000ms
        setInterval(fetchAntreanLiveData, 5000);
    </script>
</body>
</html>
