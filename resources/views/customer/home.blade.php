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
                <nav class="hidden lg:flex items-center gap-1 text-xs font-bold text-slate-600">
                    <a href="{{ route('customer.home') }}" class="px-3.5 py-2 rounded-xl text-blue-600 bg-blue-50 font-extrabold flex items-center gap-1.5 transition">
                        <span>Home</span>
                    </a>
                    <a href="#katalog-print" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        Digital Printing
                    </a>
                    <a href="#katalog-atk" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        Katalog ATK
                    </a>
                    <a href="#promo-section" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        Promo &amp; Paket
                    </a>
                    <a href="#pesanan-saya" class="px-3.5 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition flex items-center gap-1.5">
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
    <section class="hero-glow pt-14 pb-20 px-4 sm:px-6 lg:px-8 text-center border-b border-slate-100">
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
    <section id="pesanan-saya" class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="bg-slate-50 rounded-3xl p-6 sm:p-8 border border-slate-200/90 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Pesanan &amp; Antrean Cetak Saya</h3>
                    <p class="text-xs text-slate-500">Pantau proses cetak berkas Anda langsung dari workshop gerai</p>
                </div>
                <button type="button" onclick="openCustomerOrderModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-2xs transition self-start sm:self-auto">
                    + Buat Pesanan Baru
                </button>
            </div>

            @if($customerOrders->isEmpty())
                <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-lg">
                        📄
                    </div>
                    <p class="text-xs font-bold text-slate-700">Belum ada riwayat pesanan aktif.</p>
                    <p class="text-[11px] text-slate-400 max-w-sm mx-auto">
                        Klik tombol <strong>"Mulai Pesan Cetak"</strong> untuk mengirim dokumen skripsi, modul, spanduk, atau membeli ATK tanpa antre.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($customerOrders->take(4) as $order)
                        <div class="bg-white rounded-2xl p-4.5 border border-slate-200 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-extrabold text-slate-900">{{ $order->nomor_nota }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase font-mono {{ $order->status_pesanan === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    ● {{ $order->status_pesanan }}
                                </span>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $order->display_judul }}</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $order->display_spesifikasi }}</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="font-mono font-bold text-blue-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $order->waktu_pesan ? $order->waktu_pesan->format('d M H:i') : '' }} WIB</span>
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
            <p class="font-semibold text-slate-600">&copy; 2026 Matahari Fotocopy &amp; Percetakan Modern Bintaro. Hak Cipta Dilindungi.</p>
            <p class="text-[11px] text-slate-400">Layanan Cetak Cepat, Jilid Skripsi, Digital Offset, Spanduk, dan Distributor Alat Tulis Kantor (ATK).</p>
        </div>
    </footer>

    <!-- ======================================================== -->
    <!-- MODAL 1: ORDER CETAK & ATK CUSTOMER                      -->
    <!-- ======================================================== -->
    <div id="modalCustomerOrder" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-base" id="orderModalTitle">Kirim Berkas &amp; Buat Pesanan</h3>
                        <p class="text-xs text-slate-400">Pesanan otomatis masuk ke antrean workshop kasir</p>
                    </div>
                </div>
                <button type="button" onclick="closeCustomerOrderModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <form action="{{ route('customer.order.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Dokumen / Nama Pesanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul_dokumen" id="orderModalJudul" required placeholder="Contoh: Modul Pelatihan atau Paket Cetak Brosur" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Produk Layanan <span class="text-rose-500">*</span></label>
                    <select name="produk_id" id="cust-prod-select" onchange="calculateCustPrice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium">
                        @foreach($availableProducts as $prod)
                            <option value="{{ $prod->produk_id }}" data-price="{{ $prod->harga_dasar }}">{{ $prod->nama_item }} - Rp {{ number_format($prod->harga_dasar, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Spesifikasi Kertas &amp; Finishing <span class="text-rose-500">*</span></label>
                    <input type="text" name="spesifikasi" id="orderModalSpesifikasi" required placeholder="Contoh: A4 70g • Full Color Cover • Jilid Lakban" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kuantitas / Jumlah Halaman</label>
                        <input type="number" name="kuantitas" id="cust-qty" value="50" min="1" oninput="calculateCustPrice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Estimasi Biaya</label>
                        <div class="w-full px-3.5 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 font-extrabold font-mono text-xs flex items-center">
                            <span id="cust-price-display">Rp 15.000</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Upload Berkas PDF / Dokumen (Opsional jika ATK)</label>
                    <input type="file" name="berkas" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Metode Pembayaran Online</label>
                    <select name="metode_pembayaran" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-medium">
                        <option value="qris">🟡 QRIS Dinamis (Instan Semua Bank &amp; E-Wallet)</option>
                        <option value="transfer">🟣 Transfer Bank BCA / Mandiri</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeCustomerOrderModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-xs">
                        Kirim Pesanan Sekarang
                    </button>
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

        function calculateCustPrice() {
            const select = document.getElementById('cust-prod-select');
            if (!select) return;
            const price = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-price') || 300);
            const qty = parseInt(document.getElementById('cust-qty')?.value || 1);
            const total = price * qty;
            document.getElementById('cust-price-display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        function openOrderForProduct(namaItem, hargaDasar) {
            document.getElementById('orderModalTitle').textContent = 'Pesan Layanan: ' + namaItem;
            document.getElementById('orderModalJudul').value = 'Pesanan ' + namaItem;
            document.getElementById('orderModalSpesifikasi').value = namaItem + ' • Kualitas High-Res Cetak Presisi';
            document.getElementById('cust-qty').value = 1;
            openCustomerOrderModal();
        }

        function orderPromoDirect(namaPromo, hargaPromo) {
            document.getElementById('orderModalTitle').textContent = 'Ambil Promo: ' + namaPromo;
            document.getElementById('orderModalJudul').value = namaPromo + ' (Flash Sale)';
            document.getElementById('orderModalSpesifikasi').value = 'Paket Promo Hemat Spesial Bulan Ini';
            document.getElementById('cust-qty').value = 1;
            openCustomerOrderModal();
        }

        function orderAtkItem(namaItem, harga, satuan) {
            document.getElementById('orderModalTitle').textContent = 'Beli ATK: ' + namaItem;
            document.getElementById('orderModalJudul').value = 'Beli ATK: ' + namaItem;
            document.getElementById('orderModalSpesifikasi').value = 'Produk ATK Retail • Satuan: ' + satuan;
            document.getElementById('cust-qty').value = 1;
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
    </script>
</body>
</html>
