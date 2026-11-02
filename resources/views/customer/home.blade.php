<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Matahari Fotocopy & Percetakan Modern</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
                            700: '#0056d2',
                            800: '#0047b3',
                            900: '#00398f',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #ffffff;
            color: #0f172a;
        }
        .hero-glow {
            background: radial-gradient(circle at 50% -10%, #dbeafe 0%, #eff6ff 35%, #ffffff 80%);
        }
        .banner-glow {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        }
        /* Custom slim sleek scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Remove default number arrows for clean steppers */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col selection:bg-blue-100 selection:text-blue-900">

    <!-- ======================================================== -->
    <!-- TOP NAVIGATION BAR                                       -->
    <!-- ======================================================== -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            
            <!-- Left Branding -->
            <div class="flex items-center gap-6">
                <a href="{{ route('customer.home') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Matahari Fotocopy" class="h-10 w-auto object-contain transition transform group-hover:scale-105 duration-200">
                    <div class="hidden sm:block">
                        <span class="font-black text-slate-900 text-lg tracking-tight">Matahari<span class="text-blue-600">Fotocopy</span></span>
                        <p class="text-[10px] text-slate-400 font-medium">Digital Printing &amp; Retail ATK</p>
                    </div>
                </a>

                <!-- Nav Menu Links -->
                <nav class="hidden lg:flex items-center gap-1 text-xs font-bold text-slate-600" id="customerNav">
                    <a href="#home" data-target="home" class="nav-item px-3.5 py-2 rounded-xl text-blue-600 bg-blue-50 font-extrabold flex items-center gap-1.5 transition">
                        <span>Home</span>
                    </a>
                    <a href="#katalog-print" data-target="katalog-print" class="nav-item px-3.5 py-2 rounded-xl text-slate-600 font-bold hover:text-blue-600 hover:bg-slate-50 transition">
                        <span>Digital Printing</span>
                    </a>
                    <a href="#katalog-atk" data-target="katalog-atk" class="nav-item px-3.5 py-2 rounded-xl text-slate-600 font-bold hover:text-blue-600 hover:bg-slate-50 transition">
                        <span>Katalog ATK</span>
                    </a>
                    <a href="#promo-section" data-target="promo-section" class="nav-item px-3.5 py-2 rounded-xl text-slate-600 font-bold hover:text-blue-600 hover:bg-slate-50 transition">
                        <span>Promo &amp; Paket</span>
                    </a>
                    <a href="#pesanan-saya" data-target="pesanan-saya" class="nav-item px-3.5 py-2 rounded-xl text-slate-600 font-bold hover:text-blue-600 hover:bg-slate-50 transition flex items-center gap-1.5">
                        <span>Pesanan Saya</span>
                        @if($customerOrders->where('status_pesanan', '!=', 'selesai')->count() > 0)
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        @endif
                    </a>
                </nav>
            </div>

            <!-- Right Profile, Points & Logout -->
            <div class="flex items-center gap-3">
                <!-- Loyalty Points Badge -->
                <button type="button" onclick="openLoyaltyModal()" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100/80 border border-amber-200 text-amber-800 rounded-full text-xs font-bold font-mono transition shadow-2xs">
                    <span>⭐</span>
                    <span>{{ number_format($saldoPoin) }} Poin</span>
                </button>

                <!-- User Profile Pill -->
                <div class="hidden md:flex items-center gap-2 pl-2 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    </div>
                    <div class="text-left text-xs">
                        <p class="font-bold text-slate-900 leading-tight">{{ $user->nama }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">Member Pelanggan</p>
                    </div>
                </div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar dari akun" class="p-2 rounded-xl border border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 text-slate-500 text-xs transition flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- Flash Message Notification -->
    @if(session('success'))
    <div id="flashNotice" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flashNotice').remove()" class="text-emerald-700 hover:text-emerald-900 font-bold ml-4">✕</button>
        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- SECTION 1: HERO HEADER                                  -->
    <!-- ======================================================== -->
    <section id="home" class="hero-glow pt-14 pb-20 px-4 sm:px-6 lg:px-8 text-center border-b border-slate-100">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Top Solusi Capsule Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50/90 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-tight shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Solusi Percetakan Digital &amp; Belanja Alat Kantor Terlengkap</span>
            </div>

            <!-- Big Main Title -->
            <h1 class="text-3xl sm:text-5xl lg:text-[54px] font-black text-slate-900 tracking-tight leading-[1.15]">
                Cetak Cepat, Hasil Presisi &amp; Lengkap Kebutuhan ATK Anda
            </h1>

            <!-- Subtitle Description -->
            <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto font-normal leading-relaxed">
                Platform percetakan modern dan penyedia perlengkapan alat tulis kantor (ATK) terpercaya. Pesan online, pantau status antrean cetak secara live, atau belanja kebutuhan kantor dengan harga terbaik.
            </p>

            <!-- 3 CTA Action Buttons -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <!-- Button 1: Mulai Pesan Cetak -->
                <button type="button" onclick="openCustomerOrderModal()" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md shadow-blue-500/25 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Mulai Pesan Cetak</span>
                </button>

                <!-- Button 2: Katalog Alat Tulis (ATK) -->
                <a href="#katalog-atk" class="px-6 py-3.5 bg-white hover:bg-slate-50 border border-blue-200 text-blue-700 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-2xs active:scale-95">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Katalog Alat Tulis (ATK)</span>
                </a>

                <!-- Button 3: Daftar Member & Klaim Poin -->
                <button type="button" onclick="openLoyaltyModal()" class="px-5 py-3.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>Daftar Member &amp; Klaim Poin</span>
                </button>
            </div>

        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 2: VALUE PROPOSITIONS (MENGAPA MEMILIH KAMI)     -->
    <!-- ======================================================== -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="text-center space-y-2 mb-10">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Mengapa Memilih Matahari FotoCopy?
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
                Segala kemudahan untuk kebutuhan cetak personal, UMKM, hingga pengadaan korporasi berskala besar.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm mb-1.5">Pesan Online &amp; Cek Real-Time</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Upload file desain mandiri, pantau jumlah antrean cetak, serta pantau estimasi waktu cetak langsung dari gadget Anda 24 jam nonstop.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm mb-1.5">Kualitas Cetak Presisi Tinggi</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Didukung armada Konica Minolta Production &amp; Roland TrueVIS dengan akurasi warna ICC 99.4%, plus jaminan thermal hot/cold proofing.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition duration-200">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm mb-1.5">One-Stop Shop Cetak &amp; ATK</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Selain percetakan presisi, penuhi kebutuhan kertas HVS, ordner arsip kantor, tinta refill hingga lakban kurir dengan fasilitas tempo &amp; faktur pajak PPN.
                </p>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 3: PROMO BULAN INI & FLASH SALE                  -->
    <!-- ======================================================== -->
    <section id="promo-section" class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="banner-glow rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            
            <!-- Banner Header & Countdown -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1 bg-rose-600 text-white text-[11px] font-black rounded-lg uppercase tracking-wider font-mono">
                        PROMO BULAN INI &amp; FLASH SALE
                    </span>
                    <span class="text-xs font-bold text-slate-700">Paket Hemat Bisnis dan ATK Spesial</span>
                </div>

                <!-- Timer -->
                <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                    <span>Berakhir dalam:</span>
                    <div class="flex items-center gap-1 font-mono font-black text-slate-900">
                        <span id="cd-hours" class="px-2 py-1 bg-slate-900 text-white rounded-md text-[11px]">12</span>
                        <span>:</span>
                        <span id="cd-minutes" class="px-2 py-1 bg-slate-900 text-white rounded-md text-[11px]">45</span>
                        <span>:</span>
                        <span id="cd-seconds" class="px-2 py-1 bg-slate-900 text-white rounded-md text-[11px]">32</span>
                    </div>
                </div>
            </div>

            <!-- 4 Promo Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Promo 1 -->
                <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">PROMO UMKM</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700 font-mono">HEMAT 25%</span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Paket Branding UMKM</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            10 Box Kartu Nama Premium + X-Banner + 500 Lembar Brosur Art Paper
                        </p>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-100">
                        <div class="text-[10px] text-slate-400 line-through">Rp 245.000</div>
                        <div class="text-base font-black text-blue-600 font-mono">Rp 185.000 <span class="text-[11px] font-normal text-slate-500">/paket</span></div>
                        <button type="button" onclick="orderPromoDirect('Paket Branding UMKM', 185000)" class="mt-3 w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                            <span>🛒 Ambil Promo</span>
                        </button>
                    </div>
                </div>

                <!-- Promo 2 -->
                <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700">FLASH DEAL</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-50 text-amber-700 font-mono">SISA 14 DUS</span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">PaperOne A4 75gsm Box</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Kertas fotocopy ultra white kemasan box isi 5 rim. Stok fresh gudang distributor.
                        </p>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-100">
                        <div class="text-[10px] text-slate-400 line-through">Rp 48.000</div>
                        <div class="text-base font-black text-blue-600 font-mono">Rp 42.500 <span class="text-[11px] font-normal text-slate-500">/rim</span></div>
                        <button type="button" onclick="orderPromoDirect('PaperOne A4 75gsm Box', 42500)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border border-blue-200">
                            <span>⚡ Beli Sekarang</span>
                        </button>
                    </div>
                </div>

                <!-- Promo 3 -->
                <div class="bg-white rounded-2xl p-4.5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-cyan-50 text-cyan-700">EXPRESS 1 HARI</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600 font-mono">MIN. 2 RIM</span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Brosur Kilat Art Paper</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Cetak separasi resolusi 2400 dpi, jaminan warna akurat &amp; lipat mesin otomatis.
                        </p>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-100">
                        <div class="text-[10px] text-slate-400 line-through">Rp 310.000</div>
                        <div class="text-base font-black text-blue-600 font-mono">Rp 256.000 <span class="text-[11px] font-normal text-slate-500">/rim</span></div>
                        <button type="button" onclick="orderPromoDirect('Brosur Kilat Art Paper', 256000)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border border-blue-200">
                            <span>📄 Pesan Kilat</span>
                        </button>
                    </div>
                </div>

                <!-- Promo 4 (Member Benefit) -->
                <div class="bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 text-white rounded-2xl p-4.5 shadow-sm flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-white/20 text-white font-mono">MEMBER BARU</span>
                            <span class="text-amber-300 text-sm">👑</span>
                        </div>
                        <h4 class="font-extrabold text-white text-sm">Reward 500 Poin + Diskon Rp 25k</h4>
                        <p class="text-[11px] text-blue-100 leading-relaxed">
                            Klaim poin selamat datang &amp; potongan langsung untuk pemesanan pertama via website kami.
                        </p>
                    </div>
                    <div class="pt-4 mt-2 border-t border-white/10">
                        <div class="text-base font-black text-white font-mono">Gratis Gabung</div>
                        <button type="button" onclick="openLoyaltyModal()" class="mt-3 w-full py-2 bg-white hover:bg-blue-50 text-blue-800 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                            <span>★ Gabung Sekarang</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 4: KATALOG DIGITAL PRINT & JASA                  -->
    <!-- ======================================================== -->
    <section id="katalog-print" class="py-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="space-y-1 mb-8">
            <span class="text-xs font-black uppercase tracking-wider text-blue-600 font-mono">KATALOG DIGITAL PRINT &amp; JASA</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Produk Cetak Pilihan &amp; Custom
            </h2>
            <p class="text-xs sm:text-sm text-slate-500">
                Dikerjakan dengan mesin presisi, finishing presisi, dan jaminan warna tajam.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Item 1: Kartu Nama Exclusive -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg hover:border-blue-300 transition duration-200 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/products/kartu_nama.jpg') }}" alt="Kartu Nama Exclusive" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-blue-600 text-white font-mono shadow-2xs">
                            HOT ITEM
                        </span>
                    </div>
                    <div class="p-4 space-y-1.5">
                        <h3 class="font-black text-slate-900 text-sm">Kartu Nama Exclusive</h3>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            240/260/310gsm Matte/Glossy, sudut lengkung/emboss, Doff/Glosy 2 sisi.
                        </p>
                    </div>
                </div>
                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Mulai dari:</span>
                        <span class="text-xs font-black text-blue-600 font-mono">Rp 28.000 <span class="text-[10px] text-slate-400 font-normal">/box</span></span>
                    </div>
                    <button type="button" onclick="openOrderForProduct('Kartu Nama Exclusive', 28000)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <span>Custom &amp; Order</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- Item 2: Spanduk Flexi Korea 440g -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg hover:border-blue-300 transition duration-200 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/products/spanduk.jpg') }}" alt="Spanduk Flexi Korea 440g" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-emerald-600 text-white font-mono shadow-2xs">
                            BEST SELLER
                        </span>
                    </div>
                    <div class="p-4 space-y-1.5">
                        <h3 class="font-black text-slate-900 text-sm">Spanduk Flexi Korea 440g</h3>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Bahan tebal anti-robek, warna cerah tahan cuaca luar ruangan hingga 1.5 tahun. Finishing mata ayam.
                        </p>
                    </div>
                </div>
                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Mulai dari:</span>
                        <span class="text-xs font-black text-blue-600 font-mono">Rp 19.500 <span class="text-[10px] text-slate-400 font-normal">/m²</span></span>
                    </div>
                    <button type="button" onclick="openOrderForProduct('Spanduk Flexi Korea 440g', 19500)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <span>Custom &amp; Order</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- Item 3: Stiker Vinyl & Transparan -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg hover:border-blue-300 transition duration-200 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/products/stiker.jpg') }}" alt="Stiker Vinyl & Transparan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-cyan-600 text-white font-mono shadow-2xs">
                            READY PRINT
                        </span>
                    </div>
                    <div class="p-4 space-y-1.5">
                        <h3 class="font-black text-slate-900 text-sm">Stiker Vinyl &amp; Transparan</h3>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Cutting kiss-cut/die-cut, laminasi tahan gores. Tahan air &amp; dingin, cocok untuk botol/kemasan.
                        </p>
                    </div>
                </div>
                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Mulai dari:</span>
                        <span class="text-xs font-black text-blue-600 font-mono">Rp 12.000 <span class="text-[10px] text-slate-400 font-normal">/A3+</span></span>
                    </div>
                    <button type="button" onclick="openOrderForProduct('Stiker Vinyl & Transparan', 12000)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <span>Custom &amp; Order</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- Item 4: Buku Agenda & Kalender Meja -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-lg hover:border-blue-300 transition duration-200 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        <img src="{{ asset('images/products/agenda_kalender.svg') }}" alt="Buku Agenda & Kalender Meja" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-purple-600 text-white font-mono shadow-2xs">
                            PREMIUM
                        </span>
                    </div>
                    <div class="p-4 space-y-1.5">
                        <h3 class="font-black text-slate-900 text-sm">Buku Agenda &amp; Kalender Meja</h3>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Hardcover finishing foil emas, spiral kawat, opsi penanggalan custom &amp; penambahan branding logo.
                        </p>
                    </div>
                </div>
                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Mulai dari:</span>
                        <span class="text-xs font-black text-blue-600 font-mono">Rp 28.000 <span class="text-[10px] text-slate-400 font-normal">/buku</span></span>
                    </div>
                    <button type="button" onclick="openOrderForProduct('Buku Agenda & Kalender Meja', 28000)" class="mt-3 w-full py-2 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <span>Custom &amp; Order</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 5: PERALATAN & ATK TERLENGKAP (INTEGRASI STOK)   -->
    <!-- ======================================================== -->
    <section id="katalog-atk" class="py-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="space-y-4">
            
            <!-- Section Header -->
            <div class="space-y-1">
                <span class="inline-block px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-black rounded-full uppercase tracking-wider font-mono">
                    TERSEDIA DI TOKO &amp; BISA KIRIM INSTANT
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Peralatan &amp; Alat Tulis Kantor (ATK) Terlengkap
                </h2>
                <p class="text-xs sm:text-sm text-slate-500">
                    Penuhi kebutuhan operasional kantor, instansi, dan sekolah dengan harga grosir maupun grosir ATK. Terintegrasi langsung dengan stok fisik gerai admin.
                </p>
            </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 pt-2 scrollbar-none" id="atkCategoryFilter">
                <button type="button" onclick="filterAtkByCategory('Semua')" class="atk-filter-btn px-4 py-2 rounded-xl text-xs font-extrabold transition bg-blue-600 text-white shadow-2xs whitespace-nowrap" data-cat="Semua">
                    Semua ATK
                </button>
                <button type="button" onclick="filterAtkByCategory('Kertas & Amplop')" class="atk-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap" data-cat="Kertas & Amplop">
                    Kertas &amp; Amplop
                </button>
                <button type="button" onclick="filterAtkByCategory('Alat Tulis & Koreksi')" class="atk-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap" data-cat="Alat Tulis & Koreksi">
                    Alat Tulis &amp; Koreksi
                </button>
                <button type="button" onclick="filterAtkByCategory('Ordner & Filing Document')" class="atk-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap" data-cat="Ordner & Filing Document">
                    Ordner &amp; Filing Document
                </button>
                <button type="button" onclick="filterAtkByCategory('Perlengkapan Packing & Jilid')" class="atk-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap" data-cat="Perlengkapan Packing & Jilid">
                    Perlengkapan Packing &amp; Jilid
                </button>
            </div>

            <!-- Stock Product Grid (Dynamically Populated from InventarisStok) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 pt-2" id="atkGridContainer">
                @foreach($stokItems as $idx => $stok)
                    @php
                        // Normalize category tag for client-side filtering
                        $cat = $stok->kategori;
                        if (str_contains($cat, 'Kertas')) {
                            $filterTag = 'Kertas & Amplop';
                        } elseif (str_contains($cat, 'Alat Tulis')) {
                            $filterTag = 'Alat Tulis & Koreksi';
                        } elseif (str_contains($cat, 'Ordner')) {
                            $filterTag = 'Ordner & Filing Document';
                        } else {
                            $filterTag = 'Perlengkapan Packing & Jilid';
                        }
                    @endphp
                    <div class="atk-item-card bg-white rounded-2xl border border-slate-200/90 p-3 shadow-2xs hover:shadow-md hover:border-blue-300 transition duration-150 flex flex-col justify-between {{ $idx >= 6 ? 'hidden extra-atk-item' : '' }}" data-category="{{ $filterTag }}">
                        <div>
                            <!-- Visual Stock Image / SVG -->
                            <div class="w-full h-24 rounded-xl bg-slate-50 flex items-center justify-center p-2 mb-2.5 overflow-hidden border border-slate-100">
                                <img src="{{ $stok->gambar_url }}" alt="{{ $stok->nama_bahan }}" class="w-full h-full object-contain" loading="lazy">
                            </div>
                            
                            <!-- Category Badge -->
                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full inline-block truncate max-w-full font-mono uppercase">
                                {{ $filterTag }}
                            </span>

                            <!-- Title & Spec -->
                            <h4 class="font-extrabold text-slate-900 text-xs mt-1.5 leading-snug line-clamp-2" title="{{ $stok->nama_bahan }}">
                                {{ $stok->nama_bahan }}
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-0.5 truncate" title="{{ $stok->gramatur_detail }}">
                                {{ $stok->gramatur_detail ?: ($stok->satuan . ' standard') }}
                            </p>
                        </div>

                        <!-- Price & Stock Indicator -->
                        <div class="pt-2.5 mt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-blue-600 font-mono">
                                    Rp {{ number_format($stok->estimasi_harga, 0, ',', '.') }}
                                </span>
                                <span class="text-[9px] font-bold {{ $stok->jumlah_stok <= 3 ? 'text-rose-500' : 'text-slate-400' }}">
                                    Stok: {{ $stok->jumlah_stok }}
                                </span>
                            </div>

                            <button type="button" onclick="orderAtkItem('{{ addslashes($stok->nama_bahan) }}', {{ $stok->estimasi_harga }}, '{{ $stok->satuan }}')" class="mt-2 w-full py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 rounded-lg text-[11px] font-bold transition flex items-center justify-center gap-1">
                                <span>+ Pesan</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View More ATK Toggle Button -->
            <div class="text-center pt-6">
                <button type="button" id="btnToggleAllAtk" onclick="toggleAllAtkItems()" class="px-6 py-2.5 rounded-xl border border-blue-200 bg-white hover:bg-blue-50 text-blue-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-2">
                    <span id="txtToggleAtk">Lihat Semua Katalog ATK ({{ $stokItems->count() }} Produk Siap Kirim)</span>
                    <span id="iconToggleAtk">→</span>
                </button>
            </div>

        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 6: 3 LANGKAH ORDER TANPA ANTRE                   -->
    <!-- ======================================================== -->
    <section class="py-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="text-center space-y-1 mb-10">
            <span class="text-xs font-black uppercase tracking-wider text-amber-500 font-mono">CARA MUDAH &amp; CEPAT</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                3 Langkah Order Tanpa Antre
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Step 1 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm font-mono shadow-xs">
                    1
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Upload File / Data</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Login ke portal pelanggan, upload file dokumen PDF, DOCX, atau foto melalui sistem pesan online mandiri kami.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm font-mono shadow-xs">
                    2
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Konfirmasi &amp; Proses Cetak</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pilih spesifikasi kertas, jilid, dan kuantitas. Mesin berkecepatan tinggi kami langsung memproses seketika.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm font-mono shadow-xs">
                    3
                </div>
                <h3 class="font-extrabold text-slate-900 text-sm">Ambil di Tempat / Kirim</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Notifikasi otomatis terkirim begitu hasil cetak selesai. Anda cukup datang ambil langsung tanpa antre atau gunakan kurir instan.
                </p>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 7: PESANAN SAYA & LIVE TRACKING ANTREAN          -->
    <!-- ======================================================== -->
    <section id="pesanan-saya" class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div id="pesanan-saya-container" class="bg-slate-50/80 rounded-3xl p-6 sm:p-8 border border-slate-200/90 space-y-6 shadow-xs">
            
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Pesanan &amp; Antrean Cetak Saya</h3>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span>
                                Live Tracking
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Lacak status berkas secara waktu nyata: <strong class="text-slate-700">Diterima &rarr; Diproses &rarr; Selesai</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="refreshCustomerOrders(this)" title="Perbarui status antrean terkini" class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition flex items-center gap-1.5 active:scale-95">
                        <svg id="refreshIcon" class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Perbarui Status</span>
                    </button>
                    <button type="button" onclick="openCustomerOrderModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 active:scale-95">
                        <span>+ Buat Pesanan Baru</span>
                    </button>
                </div>
            </div>

            @if($customerOrders->isEmpty())
                <div class="bg-white rounded-2xl p-10 border border-slate-200 text-center space-y-3.5">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-2xl shadow-2xs">
                        📄
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800">Belum Ada Riwayat Pesanan Aktif</p>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                            Kirim berkas dokumen, skripsi, materi pelatihan, atau beli ATK Anda langsung secara online tanpa perlu antre di toko.
                        </p>
                    </div>
                    <button type="button" onclick="openCustomerOrderModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-95">
                        <span>Mulai Buat Pesanan Sekarang</span>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    @foreach($customerOrders->take(6) as $order)
                        @php
                            $alurRaw = strtolower((string) ($order->antreanMesin?->status_alur ?? $order->antreanMesin?->status_antrean ?? $order->status_pesanan ?? 'diterima'));
                            $statusPesananRaw = strtolower((string) $order->status_pesanan);

                            if (in_array($alurRaw, ['selesai'], true) || $statusPesananRaw === 'selesai') {
                                $currentStep = 3;
                                $statusStepLabel = 'Selesai';
                                $statusBadgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $statusDotBg = 'bg-emerald-500';
                                $statusDesc = 'Pesanan telah selesai diproses & diserahkan. Terima kasih atas pesanan Anda!';
                            } elseif (in_array($alurRaw, ['siap diambil', 'siap', 'siap_pickup'], true) || $statusPesananRaw === 'siap') {
                                $currentStep = 3;
                                $statusStepLabel = 'Siap Diambil';
                                $statusBadgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $statusDotBg = 'bg-emerald-500';
                                $statusDesc = 'Hasil cetak Anda telah selesai diproses & siap diambil di loket kasir gerai Bintaro Sektor 7.';
                            } elseif (in_array($alurRaw, ['diterima', 'menunggu', 'antrean'], true) || in_array($statusPesananRaw, ['menunggu', 'diterima'], true)) {
                                $currentStep = 1;
                                $statusStepLabel = ($alurRaw === 'menunggu' || $statusPesananRaw === 'menunggu') ? 'Menunggu Konfirmasi' : 'Diterima di Antrean';
                                $statusBadgeBg = ($alurRaw === 'menunggu' || $statusPesananRaw === 'menunggu') ? 'bg-orange-50 text-orange-800 border-orange-200' : 'bg-blue-50 text-blue-700 border-blue-200';
                                $statusDotBg = ($alurRaw === 'menunggu' || $statusPesananRaw === 'menunggu') ? 'bg-orange-500' : 'bg-blue-500';
                                $statusDesc = ($alurRaw === 'menunggu' || $statusPesananRaw === 'menunggu')
                                    ? 'Pesanan telah masuk antrean workshop gerai. Menunggu konfirmasi & pengecekan berkas oleh admin/kasir.'
                                    : 'Pesanan telah diterima gerai. Berkas siap dijadwalkan masuk mesin cetak.';
                            } elseif (in_array($alurRaw, ['sedang diproses', 'sedang cetak', 'sedang jilid', 'menunggu bahan', 'cetak', 'salin', 'finishing', 'proses'], true) || $statusPesananRaw === 'proses') {
                                $currentStep = 2;
                                $statusStepLabel = $order->antreanMesin?->status_alur ?: 'Sedang Diproses';
                                $statusBadgeBg = 'bg-amber-50 text-amber-800 border-amber-200';
                                $statusDotBg = 'bg-amber-500';
                                $statusDesc = 'Berkas Anda sedang dikerjakan di workshop (' . ($order->antreanMesin?->nama_mesin ?? 'Mesin Cetak Presisi') . '). Estimasi segera selesai.';
                            } else {
                                $currentStep = 1;
                                $statusStepLabel = 'Diterima di Antrean';
                                $statusBadgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                                $statusDotBg = 'bg-blue-500';
                                $statusDesc = 'Pesanan telah masuk antrean workshop gerai. Menunggu giliran mesin cetak & pengecekan berkas.';
                            }
                        @endphp

                        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md transition duration-200 space-y-4 flex flex-col justify-between">
                            
                            <!-- Card Header: Nota & Status Badge -->
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                        {{ $order->nomor_nota }}
                                    </span>
                                    @if($order->antreanMesin)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-mono font-extrabold flex items-center gap-1 shadow-2xs">
                                            <span>Tiket</span>
                                            <span class="text-amber-400">{{ $order->antreanMesin->kode_antrean }}</span>
                                        </span>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase font-mono border flex items-center gap-1.5 shrink-0 {{ $statusBadgeBg }}">
                                    <span class="w-2 h-2 rounded-full {{ $statusDotBg }} animate-pulse"></span>
                                    <span>{{ $statusStepLabel }}</span>
                                </span>
                            </div>

                            <!-- Document Details & Pricing -->
                            <div class="space-y-1.5">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="text-sm font-black text-slate-900 leading-snug tracking-tight">
                                        {{ $order->display_judul }}
                                    </h4>
                                    <span class="font-mono text-xs font-black text-blue-600 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg shrink-0">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $order->display_spesifikasi }}</span>
                                </p>
                                @if($order->antreanMesin && $order->antreanMesin->nama_mesin)
                                    <div class="flex items-center gap-2 pt-1 text-[11px] font-mono text-slate-500">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-bold text-slate-700">
                                            ⚙️ {{ $order->antreanMesin->nama_mesin }}
                                        </span>
                                        @if($order->antreanMesin->keterangan_tenggat)
                                            <span class="text-slate-400 font-medium">⏳ {{ $order->antreanMesin->keterangan_tenggat }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Visual Tracking Stepper Bar (Diterima -> Diproses -> Selesai) -->
                            <div class="py-3 px-3 sm:px-4 bg-slate-50/90 rounded-2xl border border-slate-200/80 space-y-3">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
                                    <span class="flex items-center gap-1.5 text-slate-800">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span>Progres Pengerjaan Berkas</span>
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400">Tahap {{ $currentStep }} dari 3</span>
                                </div>

                                <!-- Stepper Graphic -->
                                <div class="relative flex items-center justify-between px-3 sm:px-6">
                                    <!-- Connecting Line Background -->
                                    <div class="absolute left-7 right-7 top-4 h-1 bg-slate-200 rounded-full z-0"></div>
                                    <!-- Active Progress Line -->
                                    <div class="absolute left-7 top-4 h-1 rounded-full transition-all duration-500 z-0 {{ $currentStep === 1 ? 'w-0' : ($currentStep === 2 ? 'w-1/2 bg-gradient-to-r from-blue-500 to-amber-500' : 'right-7 bg-emerald-500') }}"></div>

                                    <!-- Step 1: Diterima -->
                                    <div class="relative z-10 flex flex-col items-center text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition {{ $currentStep >= 1 ? ($currentStep === 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md shadow-blue-500/30' : 'bg-emerald-500 text-white shadow-xs') : 'bg-white border-2 border-slate-300 text-slate-400' }}">
                                            @if($currentStep > 1)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span>1</span>
                                            @endif
                                        </div>
                                        <span class="mt-1.5 text-[11px] {{ $currentStep >= 1 ? 'font-black text-slate-900' : 'font-semibold text-slate-400' }}">Diterima</span>
                                        <span class="text-[9px] text-slate-400 hidden sm:block">Masuk Antrean</span>
                                    </div>

                                    <!-- Step 2: Diproses -->
                                    <div class="relative z-10 flex flex-col items-center text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition {{ $currentStep === 2 ? 'bg-amber-500 text-white ring-4 ring-amber-100 shadow-md shadow-amber-500/30 animate-pulse' : ($currentStep > 2 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white border-2 border-slate-300 text-slate-400') }}">
                                            @if($currentStep > 2)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span>2</span>
                                            @endif
                                        </div>
                                        <span class="mt-1.5 text-[11px] {{ $currentStep >= 2 ? 'font-black text-slate-900' : 'font-semibold text-slate-400' }}">Diproses</span>
                                        <span class="text-[9px] text-slate-400 hidden sm:block">Cetak / Jilid</span>
                                    </div>

                                    <!-- Step 3: Selesai -->
                                    <div class="relative z-10 flex flex-col items-center text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs transition {{ $currentStep === 3 ? 'bg-emerald-500 text-white ring-4 ring-emerald-100 shadow-md shadow-emerald-500/30' : 'bg-white border-2 border-slate-300 text-slate-400' }}">
                                            @if($currentStep === 3)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span>3</span>
                                            @endif
                                        </div>
                                        <span class="mt-1.5 text-[11px] {{ $currentStep === 3 ? 'font-black text-emerald-700' : 'font-semibold text-slate-400' }}">Selesai</span>
                                        <span class="text-[9px] text-slate-400 hidden sm:block">Siap Diambil</span>
                                    </div>
                                </div>

                                <!-- Status Explanation Alert -->
                                <div class="p-2.5 rounded-xl border text-[11px] leading-relaxed flex items-start gap-2.5 {{ $currentStep === 3 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($currentStep === 2 ? 'bg-amber-50 border-amber-200 text-amber-950' : 'bg-blue-50 border-blue-200 text-blue-950') }}">
                                    <span class="text-base shrink-0 leading-none mt-0.5">
                                        @if($currentStep === 3)
                                            🎉
                                        @elseif($currentStep === 2)
                                            🖨️
                                        @else
                                            📥
                                        @endif
                                    </span>
                                    <div>
                                        <strong class="font-extrabold block">
                                            @if($currentStep === 3)
                                                Pesanan Selesai &amp; Siap Diambil di Loket!
                                            @elseif($currentStep === 2)
                                                Sedang Dikerjakan di Workshop!
                                            @else
                                                Pesanan Telah Diterima di Antrean!
                                            @endif
                                        </strong>
                                        <p class="text-[10px] opacity-90 mt-0.5">{{ $statusDesc }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Time, Payment & WhatsApp Help Button -->
                            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs text-slate-500">
                                <div class="flex items-center gap-2 text-[11px] font-mono">
                                    <span>🕒 {{ $order->waktu_pesan ? $order->waktu_pesan->format('d M H:i') : '' }} WIB</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="capitalize">💳 {{ strtoupper($order->metode_pembayaran ?: 'QRIS') }}</span>
                                </div>
                                <a href="https://wa.me/6281277119030?text={{ urlencode('Halo Matahari Fotocopy, saya ingin tanya pesanan saya nomor: ' . $order->nomor_nota . ' (' . ($order->antreanMesin?->kode_antrean ?? '') . ')') }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-bold transition shadow-2xs active:scale-95 self-start sm:self-auto">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.761.889 2.796.889 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.766-5.768-5.766zm9.969 5.766c0 5.503-4.469 9.972-9.972 9.972-1.748 0-3.385-.45-4.819-1.236l-5.209 1.366 1.39-5.083c-.88-1.488-1.39-3.23-1.39-5.019 0-5.503 4.469-9.972 9.972-9.972 5.503 0 10.028 4.469 10.028 9.972z"/></svg>
                                    <span>Tanya Kasir WA</span>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- SECTION 8: BOTTOM FLOATING ASSISTANCE BANNER             -->
    <!-- ======================================================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14 w-full">
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl shadow-slate-900/10">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-white">Ada pertanyaan format file atau custom finishing?</h4>
                    <p class="text-[11px] sm:text-xs text-slate-300">Tim customer service kami siap membantu memeriksa kelayakan file cetak Anda.</p>
                </div>
            </div>

            <a href="https://wa.me/6281277119030?text=Halo%20Matahari%20Fotocopy,%20saya%20ingin%20konsultasi%20cetak%20dokumen" target="_blank" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm flex-shrink-0">
                <span>WhatsApp 0812-7711-9030</span>
            </a>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- FOOTER                                                   -->
    <!-- ======================================================== -->
    <footer class="bg-slate-50 border-t border-slate-200/80 py-8 text-center text-xs text-slate-400 mt-auto">
        <div class="max-w-7xl mx-auto px-4 space-y-2">
            <p class="text-[11px] text-slate-400">Layanan Cetak Cepat, Jilid Skripsi, Digital Offset, Spanduk, dan Distributor Alat Tulis Kantor (ATK).</p>
        </div>
    </footer>

    <!-- ======================================================== -->
    <!-- MODAL 1: ORDER CETAK & ATK CUSTOMER                      -->
    <!-- ======================================================== -->
    <div id="modalCustomerOrder" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4 transition-all">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-100 max-h-[92vh] flex flex-col overflow-hidden">
            
            <!-- Modal Header (Fixed) -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base tracking-tight" id="orderModalTitle">Kirim Berkas &amp; Buat Pesanan</h3>
                        <p class="text-xs text-slate-400 font-medium">Pesanan otomatis masuk ke antrean workshop kasir</p>
                    </div>
                </div>
                <button type="button" onclick="closeCustomerOrderModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <!-- Modal Body (Scrollable with Slim Scrollbar) -->
            <form id="custOrderForm" action="{{ route('customer.order.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="px-6 py-4 overflow-y-auto space-y-3.5 custom-scrollbar text-xs flex-1">
                    
                    <!-- Row 1: Judul Dokumen & Pilih Produk -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 text-xs">
                                Judul Dokumen / Nama Pesanan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="judul_dokumen" id="orderModalJudul" required placeholder="Contoh: Modul Pelatihan / Brosur" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium placeholder:text-slate-400 bg-slate-50/50 hover:bg-white focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 text-xs">
                                Pilih Produk Layanan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="produk_id" id="cust-prod-select" onchange="calculateCustPrice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-semibold text-slate-700 bg-slate-50/50 hover:bg-white focus:bg-white transition appearance-none pr-8">
                                    @foreach($availableProducts as $prod)
                                        <option value="{{ $prod->produk_id }}" data-price="{{ $prod->harga_dasar }}">{{ $prod->nama_item }} - Rp {{ number_format($prod->harga_dasar, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 top-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Modern Upload Dropzone Card -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-bold text-slate-700 text-xs">Upload Berkas PDF / Dokumen</label>
                            <span class="text-[11px] text-slate-400 font-medium">Opsional jika ATK</span>
                        </div>
                        
                        <div id="dropzone-cust-file" onclick="document.getElementById('cust-file-input').click()" class="border-2 border-dashed border-blue-200 hover:border-blue-400 bg-blue-50/30 hover:bg-blue-50/60 rounded-2xl p-3.5 text-center cursor-pointer transition group relative">
                            <input type="file" name="berkas" id="cust-file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="handleCustFileSelect(this)" class="hidden">
                            
                            <!-- Placeholder State -->
                            <div id="cust-file-empty-state" class="flex flex-col items-center justify-center space-y-1">
                                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-200/80 transition duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700">
                                    <span class="text-blue-600 underline underline-offset-2">Klik untuk pilih file</span> atau seret dokumen ke sini
                                </p>
                                <p class="text-[10px] text-slate-400">PDF, DOC, DOCX, JPG, PNG (Maksimal 20MB)</p>
                            </div>

                            <!-- Selected File State -->
                            <div id="cust-file-selected-state" class="hidden flex items-center justify-between bg-white p-2.5 rounded-xl border border-blue-200 shadow-2xs">
                                <div class="flex items-center gap-2.5 text-left truncate">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 font-black text-[10px] flex items-center justify-center shrink-0">
                                        PDF
                                    </div>
                                    <div class="truncate">
                                        <p id="cust-file-name" class="font-bold text-slate-800 text-xs truncate">nama_file.pdf</p>
                                        <p id="cust-file-info" class="text-[11px] text-emerald-600 font-semibold">✓ 63 Halaman terdeteksi otomatis</p>
                                    </div>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); removeCustSelectedFile();" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 px-2 py-1 rounded-lg hover:bg-rose-50 transition shrink-0">
                                    Ganti Berkas
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: 3 Kolom Sejajar Rapi: Halaman, Rangkap, Estimasi -->
                    <div class="grid grid-cols-3 gap-2.5 items-end">
                        <!-- Kolom 1: Halaman -->
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1.5 h-4 leading-4 truncate">
                                Jml Halaman
                            </label>
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500">
                                <button type="button" onclick="adjustCustPages(-1)" class="w-7 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50/50 transition font-black text-sm select-none">−</button>
                                <input type="number" name="jumlah_halaman" id="cust-pages" value="1" min="1" oninput="calculateCustPrice()" class="w-full h-10 text-center font-bold font-mono text-xs text-slate-800 focus:outline-none bg-transparent">
                                <button type="button" onclick="adjustCustPages(1)" class="w-7 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50/50 transition font-black text-sm select-none">+</button>
                            </div>
                        </div>

                        <!-- Kolom 2: Rangkap -->
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1.5 h-4 leading-4 truncate">
                                Jml Rangkap
                            </label>
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500">
                                <button type="button" onclick="adjustCustQty(-1)" class="w-7 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50/50 transition font-black text-sm select-none">−</button>
                                <input type="number" name="kuantitas" id="cust-qty" value="1" min="1" oninput="calculateCustPrice()" class="w-full h-10 text-center font-bold font-mono text-xs text-slate-800 focus:outline-none bg-transparent">
                                <button type="button" onclick="adjustCustQty(1)" class="w-7 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50/50 transition font-black text-sm select-none">+</button>
                            </div>
                        </div>

                        <!-- Kolom 3: Estimasi Biaya Highlight Card -->
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1.5 h-4 leading-4 truncate">
                                Estimasi Biaya
                            </label>
                            <div class="w-full h-10 px-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold font-mono text-xs flex items-center justify-center shadow-md shadow-blue-500/20">
                                <span id="cust-price-display">Rp 300</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Rincian Hitungan Bersih, Terstruktur & Rapih -->
                    <div id="cust-price-formula-box" class="p-3 rounded-2xl bg-blue-50/70 border border-blue-200/80 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
                                <span>💡</span>
                                <span>Rincian Perhitungan Biaya</span>
                            </span>
                            <span id="cust-formula-total-lembar" class="text-[11px] font-bold text-blue-700 bg-white border border-blue-200 px-2.5 py-0.5 rounded-full shadow-2xs">
                                Total 1 Lembar Cetak
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-5 items-center p-2.5 rounded-xl bg-white border border-blue-100/90 text-center shadow-2xs">
                            <div class="col-span-1">
                                <span class="text-[10px] text-slate-400 block font-semibold leading-tight">Halaman</span>
                                <span id="cust-breakdown-pages" class="font-mono font-black text-slate-800 text-xs mt-0.5 block">1 Hal</span>
                            </div>
                            <div class="col-span-1 text-slate-300 font-bold text-sm">×</div>
                            <div class="col-span-1">
                                <span class="text-[10px] text-slate-400 block font-semibold leading-tight">Tarif</span>
                                <span id="cust-breakdown-price" class="font-mono font-black text-slate-800 text-xs mt-0.5 block">Rp 300</span>
                            </div>
                            <div class="col-span-1 text-slate-300 font-bold text-sm">×</div>
                            <div class="col-span-1">
                                <span class="text-[10px] text-slate-400 block font-semibold leading-tight">Rangkap</span>
                                <span id="cust-breakdown-qty" class="font-mono font-black text-slate-800 text-xs mt-0.5 block">1 Buku</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Spesifikasi & Pembayaran (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 text-xs">
                                Spesifikasi Kertas &amp; Finishing <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="spesifikasi" id="orderModalSpesifikasi" required placeholder="Contoh: A4 70g • Full Color • Jilid Lakban" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-medium placeholder:text-slate-400 bg-slate-50/50 hover:bg-white focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 text-xs">Metode Pembayaran Online</label>
                            <div class="relative">
                                <select name="metode_pembayaran" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-semibold text-slate-700 bg-slate-50/50 hover:bg-white focus:bg-white transition appearance-none pr-8">
                                    <option value="qris">🟡 QRIS Dinamis (Instan Semua E-Wallet &amp; Bank)</option>
                                    <option value="transfer">🟣 Transfer Bank BCA / Mandiri</option>
                                </select>
                                <div class="absolute right-3 top-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sticky Footer: Always visible, never cut off! -->
                <div class="px-6 py-3.5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0 rounded-b-3xl">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Total:</span>
                        <span id="cust-footer-price" class="text-base font-black font-mono text-blue-700">Rp 300</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeCustomerOrderModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-white hover:border-slate-300 transition text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold transition shadow-md shadow-blue-500/25 flex items-center gap-2 text-xs hover:scale-[1.02] active:scale-[0.98]">
                            <span>Kirim Pesanan Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: LOYALTY MEMBER INFO                             -->
    <!-- ======================================================== -->
    <div id="modalLoyalty" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 mx-auto flex items-center justify-center text-xl">
                👑
            </div>
            <div>
                <h3 class="font-black text-slate-900 text-base">Program Member &amp; Poin Loyalitas</h3>
                <p class="text-xs text-slate-500 mt-1">Status: Member Pelanggan Aktif</p>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
                <span class="text-[11px] font-bold text-amber-700 uppercase">Saldo Poin Reward Anda</span>
                <div class="text-3xl font-black text-amber-900 font-mono mt-0.5">
                    {{ number_format($saldoPoin) }}
                </div>
                <p class="text-[10px] text-amber-700 mt-1">Dapat ditukarkan dengan diskon cetak &amp; hadiah eksklusif di kasir gerai.</p>
            </div>

            <button type="button" onclick="closeLoyaltyModal()" class="w-full py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- JAVASCRIPT LOGIC                                         -->
    <!-- ======================================================== -->
    <script>
        function openCustomerOrderModal() {
            document.getElementById('modalCustomerOrder').classList.remove('hidden');
            calculateCustPrice();
        }
        function closeCustomerOrderModal() {
            document.getElementById('modalCustomerOrder').classList.add('hidden');
        }

        function openLoyaltyModal() {
            document.getElementById('modalLoyalty').classList.remove('hidden');
        }
        function closeLoyaltyModal() {
            document.getElementById('modalLoyalty').classList.add('hidden');
        }

        let detectedPdfPages = null;

        function adjustCustPages(delta) {
            const input = document.getElementById('cust-pages');
            if (!input) return;
            const current = parseInt(input.value || 1);
            const next = Math.max(1, current + delta);
            input.value = next;
            calculateCustPrice();
        }

        function adjustCustQty(delta) {
            const input = document.getElementById('cust-qty');
            if (!input) return;
            const current = parseInt(input.value || 1);
            const next = Math.max(1, current + delta);
            input.value = next;
            calculateCustPrice();
        }

        function removeCustSelectedFile() {
            const input = document.getElementById('cust-file-input');
            if (input) input.value = '';
            handleCustFileSelect(input);
        }

        function handleCustFileSelect(input) {
            const file = input && input.files && input.files[0];
            const pagesInput = document.getElementById('cust-pages');
            const emptyState = document.getElementById('cust-file-empty-state');
            const selectedState = document.getElementById('cust-file-selected-state');
            const fileNameEl = document.getElementById('cust-file-name');
            const fileInfoEl = document.getElementById('cust-file-info');

            if (!file) {
                detectedPdfPages = null;
                if (emptyState) emptyState.classList.remove('hidden');
                if (selectedState) selectedState.classList.add('hidden');
                if (pagesInput) pagesInput.value = 1;
                calculateCustPrice();
                return;
            }

            if (emptyState) emptyState.classList.add('hidden');
            if (selectedState) selectedState.classList.remove('hidden');
            if (fileNameEl) fileNameEl.textContent = file.name;

            const sizeFormatted = (file.size / 1024 / 1024 >= 1) 
                ? (file.size / 1024 / 1024).toFixed(1) + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';

            if (file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const text = e.target.result;
                    const matches = text.match(/\/Type\s*\/Page\b(?!s)/g);
                    const pages = matches && matches.length > 0 ? matches.length : 1;
                    detectedPdfPages = pages;

                    if (pagesInput) {
                        pagesInput.value = pages;
                    }
                    if (fileInfoEl) {
                        fileInfoEl.textContent = `✓ ${pages} Halaman PDF terdeteksi otomatis (${sizeFormatted})`;
                    }
                    calculateCustPrice();
                };
                reader.readAsBinaryString(file);
            } else {
                detectedPdfPages = 1;
                if (pagesInput && !pagesInput.value) {
                    pagesInput.value = 1;
                }
                if (fileInfoEl) {
                    fileInfoEl.textContent = `✓ Berkas terlampir (${sizeFormatted})`;
                }
                calculateCustPrice();
            }
        }

        function calculateCustPrice() {
            const select = document.getElementById('cust-prod-select');
            if (!select) return;
            const price = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-price') || 300);
            const pagesInput = document.getElementById('cust-pages');
            const qtyInput = document.getElementById('cust-qty');

            const pages = Math.max(1, parseInt(pagesInput?.value || detectedPdfPages || 1));
            const qty = Math.max(1, parseInt(qtyInput?.value || 1));
            const total = pages * price * qty;

            const formattedPrice = new Intl.NumberFormat('id-ID').format(price);
            const formattedTotal = new Intl.NumberFormat('id-ID').format(total);

            const displayEl = document.getElementById('cust-price-display');
            if (displayEl) {
                displayEl.textContent = 'Rp ' + formattedTotal;
            }

            const footerPriceEl = document.getElementById('cust-footer-price');
            if (footerPriceEl) {
                footerPriceEl.textContent = 'Rp ' + formattedTotal;
            }

            const breakdownPages = document.getElementById('cust-breakdown-pages');
            const breakdownPrice = document.getElementById('cust-breakdown-price');
            const breakdownQty = document.getElementById('cust-breakdown-qty');
            const totalLembarBadge = document.getElementById('cust-formula-total-lembar');

            if (breakdownPages) breakdownPages.textContent = `${pages} Hal`;
            if (breakdownPrice) breakdownPrice.textContent = `Rp ${formattedPrice}`;
            if (breakdownQty) breakdownQty.textContent = `${qty} ${qty > 1 ? 'Rangkap' : 'Buku'}`;
            if (totalLembarBadge) totalLembarBadge.textContent = `Total ${pages * qty} Lembar Cetak`;
        }

        function resetCustOrderModalState() {
            removeCustSelectedFile();
            const pagesInput = document.getElementById('cust-pages');
            if (pagesInput) pagesInput.value = 1;
            const qtyInput = document.getElementById('cust-qty');
            if (qtyInput) qtyInput.value = 1;
            detectedPdfPages = null;
        }

        function openOrderForProduct(namaItem, hargaDasar) {
            resetCustOrderModalState();
            document.getElementById('orderModalTitle').textContent = 'Pesan Layanan: ' + namaItem;
            document.getElementById('orderModalJudul').value = 'Pesanan ' + namaItem;
            document.getElementById('orderModalSpesifikasi').value = namaItem + ' • Kualitas High-Res Cetak Presisi';
            const select = document.getElementById('cust-prod-select');
            if (select) {
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].text.toLowerCase().includes(namaItem.toLowerCase())) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
            openCustomerOrderModal();
        }

        function orderPromoDirect(namaPromo, hargaPromo) {
            resetCustOrderModalState();
            document.getElementById('orderModalTitle').textContent = 'Ambil Promo: ' + namaPromo;
            document.getElementById('orderModalJudul').value = namaPromo + ' (Flash Sale)';
            document.getElementById('orderModalSpesifikasi').value = 'Paket Promo Hemat Spesial Bulan Ini';
            openCustomerOrderModal();
        }

        function orderAtkItem(namaItem, harga, satuan) {
            resetCustOrderModalState();
            document.getElementById('orderModalTitle').textContent = 'Beli ATK: ' + namaItem;
            document.getElementById('orderModalJudul').value = 'Beli ATK: ' + namaItem;
            document.getElementById('orderModalSpesifikasi').value = 'Produk ATK Retail • Satuan: ' + satuan;
            openCustomerOrderModal();
        }

        function filterAtkByCategory(category) {
            // Update button styles
            const buttons = document.querySelectorAll('.atk-filter-btn');
            buttons.forEach(btn => {
                if (btn.getAttribute('data-cat') === category) {
                    btn.classList.add('bg-blue-600', 'text-white', 'shadow-2xs');
                    btn.classList.remove('bg-slate-100', 'text-slate-700');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white', 'shadow-2xs');
                    btn.classList.add('bg-slate-100', 'text-slate-700');
                }
            });

            // Filter cards
            const cards = document.querySelectorAll('.atk-item-card');
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'Semua' || cardCat === category) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        let isAllAtkExpanded = false;
        function toggleAllAtkItems() {
            const extraItems = document.querySelectorAll('.extra-atk-item');
            const txt = document.getElementById('txtToggleAtk');
            const icon = document.getElementById('iconToggleAtk');

            isAllAtkExpanded = !isAllAtkExpanded;
            if (isAllAtkExpanded) {
                extraItems.forEach(el => el.classList.remove('hidden'));
                txt.textContent = 'Tampilkan Lebih Sedikit';
                icon.textContent = '↑';
            } else {
                extraItems.forEach(el => el.classList.add('hidden'));
                txt.textContent = 'Lihat Semua Katalog ATK ({{ $stokItems->count() }} Produk Siap Kirim)';
                icon.textContent = '→';
            }
        }

        // Live Countdown Timer for Flash Sale
        let totalSeconds = 12 * 3600 + 45 * 60 + 32;
        setInterval(() => {
            if (totalSeconds > 0) totalSeconds--;
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            document.getElementById('cd-hours').textContent = String(h).padStart(2, '0');
            document.getElementById('cd-minutes').textContent = String(m).padStart(2, '0');
            document.getElementById('cd-seconds').textContent = String(s).padStart(2, '0');
        }, 1000);

        // ========================================================
        // DYNAMIC ACTIVE NAVBAR HIGHLIGHT & SCROLLSPY
        // ========================================================
        const trackedNavItems = document.querySelectorAll('#customerNav .nav-item');
        const trackedSectionIds = ['home', 'promo-section', 'katalog-print', 'katalog-atk', 'pesanan-saya'];

        function setActiveCustomerNav(targetId) {
            if (!targetId) return;
            trackedNavItems.forEach(item => {
                const itemTarget = item.getAttribute('data-target');
                if (itemTarget === targetId) {
                    item.classList.add('text-blue-600', 'bg-blue-50', 'font-extrabold');
                    item.classList.remove('text-slate-600', 'font-bold');
                } else {
                    item.classList.remove('text-blue-600', 'bg-blue-50', 'font-extrabold');
                    item.classList.add('text-slate-600', 'font-bold');
                }
            });
        }

        // On Click Navbar Item
        trackedNavItems.forEach(link => {
            link.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                if (targetId) {
                    setActiveCustomerNav(targetId);
                }
            });
        });

        // Scrollspy to automatically switch active highlight while scrolling
        function syncNavOnScroll() {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;

            // When scrolled near bottom, activate last section
            if (scrollY + windowHeight >= docHeight - 80) {
                setActiveCustomerNav('pesanan-saya');
                return;
            }

            // Check sections from bottom to top
            for (let i = trackedSectionIds.length - 1; i >= 0; i--) {
                const secId = trackedSectionIds[i];
                const sec = document.getElementById(secId);
                if (sec) {
                    const rect = sec.getBoundingClientRect();
                    // When section top is scrolled past header threshold (150px)
                    if (rect.top <= 150) {
                        setActiveCustomerNav(secId);
                        return;
                    }
                }
            }

            setActiveCustomerNav('home');
        }

        let navScrollThrottle;
        window.addEventListener('scroll', function() {
            if (!navScrollThrottle) {
                navScrollThrottle = setTimeout(function() {
                    syncNavOnScroll();
                    navScrollThrottle = null;
                }, 40);
            }
        }, { passive: true });

        // Set active on load or initial hash
        window.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.replace('#', '');
            if (hash && trackedSectionIds.includes(hash)) {
                setActiveCustomerNav(hash);
            } else {
                syncNavOnScroll();
            }

            // Setup modern drag and drop for modal file upload
            const dropzone = document.getElementById('dropzone-cust-file');
            const fileInput = document.getElementById('cust-file-input');
            if (dropzone && fileInput) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-blue-500', 'bg-blue-100/60');
                    }, false);
                });
                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-blue-500', 'bg-blue-100/60');
                    }, false);
                });
                dropzone.addEventListener('drop', function(e) {
                    const dt = e.dataTransfer;
                    const files = dt && dt.files;
                    if (files && files.length > 0) {
                        fileInput.files = files;
                        handleCustFileSelect(fileInput);
                    }
                }, false);
            }
        });

        window.addEventListener('hashchange', function() {
            const hash = window.location.hash.replace('#', '');
            if (hash && trackedSectionIds.includes(hash)) {
                setActiveCustomerNav(hash);
            }
        });

        // ========================================================
        // LIVE TRACKING REFRESH & AUTO-POLLING
        // ========================================================
        function refreshCustomerOrders(btn) {
            const icon = document.getElementById('refreshIcon');
            if (icon) icon.classList.add('animate-spin');

            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('pesanan-saya-container');
                const currentContent = document.getElementById('pesanan-saya-container');
                if (newContent && currentContent) {
                    currentContent.innerHTML = newContent.innerHTML;
                }
            })
            .catch(err => {
                console.warn('Gagal sinkronisasi antrean:', err);
            })
            .finally(() => {
                if (icon) {
                    setTimeout(() => icon.classList.remove('animate-spin'), 400);
                }
            });
        }

        // Auto polling every 15 seconds so customer sees status updates in real time
        setInterval(() => {
            refreshCustomerOrders();
        }, 15000);
    </script>
</body>
</html>
