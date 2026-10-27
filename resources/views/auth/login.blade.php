<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matahari Fotocopy - Masuk & Pendaftaran Akun</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN for instant styling fidelity -->
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
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#0056d2',
                            600: '#0047b3',
                            700: '#00398f',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        sun: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #eaf2fc 0%, #f1f6fd 50%, #e8f0fb 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .hero-title {
            letter-spacing: -0.03em;
            line-height: 1.15;
        }
        .pulse-indicator {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
        .tab-btn.active {
            background-color: #0056d2;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 86, 210, 0.25);
        }
        .tab-btn:not(.active) {
            background-color: #f1f5f9;
            color: #475569;
        }
        .tab-btn:not(.active):hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body class="text-slate-800 antialiased py-6 sm:py-8 lg:py-10 px-4 sm:px-6 lg:px-8 flex flex-col justify-start items-center min-h-screen">

    <div class="max-w-7xl w-full mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
        
        <!-- ========================================== -->
        <!-- LEFT COLUMN: BRANDING & FEATURE SHOWCASE   -->
        <!-- ========================================== -->
        <div class="md:col-span-7 flex flex-col justify-between h-full space-y-6 pr-0 md:pr-4">
            
            <!-- Top Bar: Logo & Portal Badge -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <!-- Brand Official Logo -->
                    <a href="{{ route('login') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('images/logo.png') }}" alt="Matahari Fotocopy" class="h-12 w-auto object-contain drop-shadow-xs transition transform group-hover:scale-105 duration-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-extrabold tracking-tight text-slate-900">Matahari<span class="text-brand-500">Fotocopy</span></span>
                                <span class="bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">HQ</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Digital Print & Fotocopy Solutions</p>
                        </div>
                    </a>
                </div>

                <!-- Live Tag Badge -->
                <div id="leftRolePill" class="inline-flex items-center gap-2 bg-white/90 backdrop-blur border border-slate-200 px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-700 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-ping"></span>
                    <span id="leftRoleText">Portal Pelanggan & Member</span>
                </div>
            </div>

            <!-- Main Hero Headlines -->
            <div class="space-y-3 pt-1">
                <h1 class="hero-title text-2xl sm:text-3xl lg:text-[34px] font-extrabold text-slate-900 leading-tight">
                    Solusi Cetak Cepat, Presisi & Pantau Status Pesanan Real-Time.
                </h1>
                <p class="text-sm sm:text-base text-slate-600 max-w-2xl font-normal leading-relaxed">
                    Platform integrasi digital print modern untuk monitoring pesanan fleksibel, proofing warna terkalibrasi, hingga pengiriman instan.
                </p>
            </div>

            <!-- 3 Feature Cards Side-by-Side (flex-1 to harmonize height) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
                <!-- Card 1 -->
                <div class="bg-white/80 hover:bg-white transition-all duration-200 rounded-2xl p-5 border border-slate-200/90 shadow-sm flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-500 mb-4">
                            <!-- Radar / Crosshair Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <circle cx="12" cy="12" r="5"/>
                                <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Status Real-Time</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Notifikasi instan saat file divalidasi, masuk rip spooling, hingga siap dipickup.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] font-bold text-brand-500 uppercase tracking-wider font-mono">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>LIVE DISPATCH</span>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white/80 hover:bg-white transition-all duration-200 rounded-2xl p-5 border border-slate-200/90 shadow-sm flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-500 mb-4">
                            <!-- Tag / Loyalty Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a2 2 0 011.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 015 9V5a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Loyalty & Diskon</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Cashback reward per meter cetak, tier membership korporat, & potongan invoice khusus.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] font-bold text-brand-500 uppercase tracking-wider font-mono">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>UP TO 15% REBATE</span>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white/80 hover:bg-white transition-all duration-200 rounded-2xl p-5 border border-slate-200/90 shadow-sm flex flex-col justify-between h-full">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-500 mb-4">
                            <!-- Cloud Reprints Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1.5">Cloud Reprints</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Arsip ICC profile & artwork siap cetak aman tersimpan untuk reorder 1-klik tanpa layout ulang.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] font-bold text-brand-500 uppercase tracking-wider font-mono">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                        </svg>
                        <span>RAW FILE VAULT</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Strip: Client Avatars & Plotter Status -->
            <div class="bg-white/90 backdrop-blur rounded-2xl p-4 border border-slate-200 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Overlapping Avatars -->
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[11px] font-bold ring-2 ring-white">PT</div>
                        <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-[11px] font-bold ring-2 ring-white">MK</div>
                        <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-bold ring-2 ring-white">5k+</div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 leading-tight">5.000+ Klien Aktif B2B & Kreator</p>
                        <p class="text-[11px] text-slate-500">Tingkat akurasi warna 99.4% & ketepatan SLA cetak</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 pulse-indicator"></span>
                    <span class="text-[11px] font-mono font-bold text-slate-700 tracking-wider">PLOTTER CLUSTER: RUNNING</span>
                </div>
            </div>

        </div>


        <!-- ========================================== -->
        <!-- RIGHT COLUMN: LOGIN & AUTHENTICATION CARD  -->
        <!-- ========================================== -->
        <div class="md:col-span-5 flex flex-col h-full">
            <div class="bg-white rounded-3xl p-6 sm:p-7 lg:p-8 shadow-xl shadow-slate-200/60 border border-slate-200/80 relative overflow-hidden flex flex-col justify-between h-full">
                <div>
                
                <!-- Role Separation Tabs -->
                <div class="mb-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 flex items-center justify-between">
                        <span>Pilih Jalur Masuk:</span>
                        <span id="roleIndicatorBadge" class="text-brand-600 font-bold bg-brand-50 px-2 py-0.5 rounded text-[11px]">Role: Pelanggan</span>
                    </div>

                    <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl gap-1">
                        <button type="button" id="tabUserBtn" onclick="switchLoginRole('user')" class="tab-btn active py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Login User / Pelanggan</span>
                        </button>

                        <button type="button" id="tabAdminBtn" onclick="switchLoginRole('admin')" class="tab-btn py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Login Admin / Kasir</span>
                        </button>
                    </div>
                </div>

                <!-- Card Header Title & Description -->
                <div class="space-y-1.5 mb-5">
                    <div id="accessBadgeContainer" class="flex items-center gap-1.5 text-brand-600 text-xs font-bold uppercase tracking-widest font-mono">
                        <svg id="accessBadgeIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span id="accessBadgeText">CUSTOMER ACCESS</span>
                    </div>

                    <h2 id="formTitle" class="text-2xl sm:text-[26px] font-extrabold text-slate-900 leading-tight">
                        Masuk ke Akun Member Matahari FotoCopy
                    </h2>
                    <p id="formSubtitle" class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Akses dashboard pesanan, kirim berkas cetak, dan kelola saldo reward point Anda.
                    </p>
                </div>

                <!-- Flash Notifications (Errors / Success) -->
                @if (session('success'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-1.5">
                                <span class="font-bold">•</span>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- MAIN LOGIN FORM -->
                <form id="mainLoginForm" action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Hidden Login Type -->
                    <input type="hidden" name="login_type" id="loginTypeInput" value="{{ old('login_type', $activeTab ?? 'user') }}">

                    <!-- Field 1: Identifier (Email) -->
                    <div>
                        <label for="identifierInput" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <!-- ID / Mail Card Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="identifier" 
                                id="identifierInput"
                                required
                                value="{{ old('identifier') }}"
                                placeholder="Masukkan email"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium placeholder:text-slate-400"
                            >
                        </div>
                    </div>

                    <!-- Field 2: Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="passwordInput" class="block text-xs font-bold text-slate-700">
                                Kata Sandi
                            </label>
                            <a href="javascript:void(0)" onclick="alert('Silakan hubungi WhatsApp CS untuk bantuan pemulihan kata sandi.')" class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline">
                                Lupa sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <!-- Lock Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="passwordInput"
                                required
                                value=""
                                placeholder="Minimal 8 karakter"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium placeholder:text-slate-400"
                            >
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & 2FA Status -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                        </label>
                        
                        <div class="flex items-center gap-1 text-slate-500 font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>2FA Aktif</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="submitLoginBtn"
                        class="w-full py-3 px-4 bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white font-bold rounded-xl shadow-md shadow-brand-500/25 transition-all duration-200 flex items-center justify-center gap-2 text-sm tracking-wide mt-2"
                    >
                        <span id="submitButtonText">Masuk ke Akun Pelanggan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                <!-- Alternative Social Divider -->
                <div class="relative my-4 sm:my-5 text-center">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <span class="relative px-3 bg-white text-[10px] font-bold text-slate-400 uppercase tracking-widest font-mono">
                        ATAU GUNAKAN
                    </span>
                </div>

                <!-- Social Quick Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="alert('Login Google terintegrasi via Google OAuth API')" class="py-2 px-3 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl flex items-center justify-center gap-2 transition shadow-2xs">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Google</span>
                    </button>

                    <button type="button" onclick="alert('Login OTP WhatsApp terintegrasi via WhatsApp Business Cloud API')" class="py-2 px-3 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl flex items-center justify-center gap-2 transition shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.546 1.761.782 2.796.783h.005c3.18 0 5.768-2.587 5.768-5.766-.001-3.182-2.589-5.77-5.773-5.77zm0 10.279h-.004c-.886 0-1.748-.238-2.503-.687l-.179-.107-1.86.488.496-1.813-.117-.187c-.495-.788-.755-1.704-.755-2.385 0-2.474 2.013-4.488 4.49-4.488 1.199 0 2.327.468 3.175 1.316.848.849 1.315 1.977 1.315 3.178 0 2.475-2.013 4.489-4.49 4.489z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </button>
                </div>

                <!-- Register Member Callout Banner (Hanya untuk Tab Pelanggan / User) -->
                <div id="customerRegisterBanner" class="mt-4 sm:mt-5 p-3.5 sm:p-4 rounded-2xl bg-blue-50/80 border border-blue-100 text-center space-y-1.5 transition-all" style="{{ ($activeTab ?? 'user') === 'admin' ? 'display: none;' : '' }}">
                    <p class="text-xs text-slate-600 font-medium">
                        Belum terdaftar sebagai member Matahari FotoCopy?
                    </p>
                    <button 
                        type="button" 
                        onclick="openRegisterModal()" 
                        class="text-xs font-extrabold text-brand-600 hover:text-brand-700 hover:underline inline-flex items-center gap-1"
                    >
                        <span>Daftar Akun Pelanggan Baru</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </button>
                </div>

                <!-- Admin & Cashier Notice Banner (Hanya untuk Tab Admin / Kasir) -->
                <div id="adminNoticeBanner" class="mt-4 sm:mt-5 p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center space-y-1.5 transition-all" style="{{ ($activeTab ?? 'user') === 'admin' ? '' : 'display: none;' }}">
                    <p class="text-xs font-bold text-slate-700 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Akses Khusus Karyawan & Kasir</span>
                    </p>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Pendaftaran akun staf dikelola langsung oleh Admin HQ / Supervisor.
                    </p>
                </div>
                </div>

                <!-- Footer Help & Working Hours -->
                <div class="mt-4 sm:mt-5 pt-3.5 sm:pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between text-[11px] text-slate-500 gap-2">
                    <div class="flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>

                    </div>

                    <div class="flex items-center gap-1 font-mono text-[10px] text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Senin - Minggu: 08:00 - 22:00 WIB</span>
                    </div>
                </div>

            </div>
        </div>

    </div>


    <!-- ============================================================== -->
    <!-- MODAL: REGISTRASI KHUSUS PELANGGAN BARU (CUSTOMER SIGNUP)     -->
    <!-- ============================================================== -->
    <div id="registerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 transform scale-95 transition-transform duration-200 relative max-h-[90vh] overflow-y-auto">
            
            <!-- Close Button -->
            <button type="button" onclick="closeRegisterModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="space-y-1 mb-5">
                <div class="inline-flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full text-xs font-bold font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>KHUSUS AKUN PELANGGAN (USER)</span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900">Daftar Akun Pelanggan Baru</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dapatkan kemudahan upload berkas online, monitoring proses cetak, dan kumpulkan poin reward!
                </p>
            </div>

            <!-- Role Notice Banner -->
            <div class="mb-5 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2.5">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="space-y-0.5">
                    <span class="font-bold block">Ketentuan Peran (Role Separation):</span>
                    <span class="text-amber-800 leading-normal">
                        Semua akun baru otomatis terdaftar sebagai <strong>Role Pelanggan (User)</strong> dan <strong>tidak memiliki hak akses</strong> ke dashboard Admin / Kasir.
                    </span>
                </div>
            </div>

            <!-- Registration Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="nama" 
                        required 
                        value="{{ old('nama') }}"
                        placeholder="Contoh: Budi Santoso"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                    >
                </div>

                <!-- No WhatsApp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Handphone / WhatsApp</label>
                    <input 
                        type="text" 
                        name="nomor_telepon" 
                        required 
                        value="{{ old('nomor_telepon') }}"
                        placeholder="081234567890"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                    >
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Min. 5 karakter"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Sandi</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="Ulangi sandi"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                        >
                    </div>
                </div>

                <!-- Loyalty Bonus Highlight -->
                <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 flex items-center gap-2.5 text-xs text-brand-700">
                    <span class="text-base">🎁</span>
                    <span>Bonus sambutan: <strong>50 Poin Loyalitas</strong> langsung aktif saat pendaftaran selesai!</span>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white font-bold rounded-xl shadow-md shadow-brand-500/25 transition flex items-center justify-center gap-2 text-sm tracking-wide"
                >
                    <span>Daftar Akun Pelanggan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-4 text-center">
                <button type="button" onclick="closeRegisterModal()" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Sudah punya akun? Kembali ke Halaman Masuk
                </button>
            </div>

        </div>
    </div>


    <!-- ============================================================== -->
    <!-- INTERACTIVE SCRIPT (ROLE SWITCHING, QUICK FILL, MODAL)         -->
    <!-- ============================================================== -->
    <script>
        let currentRole = '{{ old("login_type", $activeTab ?? "user") }}';

        function switchLoginRole(role) {
            currentRole = role;
            const loginTypeInput = document.getElementById('loginTypeInput');
            const tabUserBtn = document.getElementById('tabUserBtn');
            const tabAdminBtn = document.getElementById('tabAdminBtn');
            const roleIndicatorBadge = document.getElementById('roleIndicatorBadge');
            const accessBadgeText = document.getElementById('accessBadgeText');
            const formTitle = document.getElementById('formTitle');
            const formSubtitle = document.getElementById('formSubtitle');
            const submitButtonText = document.getElementById('submitButtonText');
            const leftRoleText = document.getElementById('leftRoleText');
            const identifierInput = document.getElementById('identifierInput');
            const passwordInput = document.getElementById('passwordInput');

            const customerRegisterBanner = document.getElementById('customerRegisterBanner');
            const adminNoticeBanner = document.getElementById('adminNoticeBanner');

            loginTypeInput.value = role;

            if (role === 'admin') {
                tabUserBtn.classList.remove('active');
                tabAdminBtn.classList.add('active');

                roleIndicatorBadge.textContent = 'Role: Admin / Kasir';
                roleIndicatorBadge.className = 'text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-[11px]';

                accessBadgeText.textContent = 'ADMIN & CASHIER ACCESS';
                formTitle.textContent = 'Masuk ke Dashboard Admin / Kasir';
                formSubtitle.textContent = 'Akses monitoring antrean mesin cetak, kasir pesanan, dan manajemen berkas pelanggan.';
                submitButtonText.textContent = 'Masuk ke Dashboard Admin';
                leftRoleText.textContent = 'Internal Staff & Cashier Panel';

                if (customerRegisterBanner) {
                    customerRegisterBanner.style.display = 'none';
                }
                if (adminNoticeBanner) {
                    adminNoticeBanner.style.display = 'block';
                }
            } else {
                tabAdminBtn.classList.remove('active');
                tabUserBtn.classList.add('active');

                roleIndicatorBadge.textContent = 'Role: Pelanggan';
                roleIndicatorBadge.className = 'text-brand-600 font-bold bg-brand-50 px-2 py-0.5 rounded text-[11px]';

                accessBadgeText.textContent = 'CUSTOMER ACCESS';
                formTitle.textContent = 'Masuk ke Akun Member Matahari FotoCopy';
                formSubtitle.textContent = 'Akses dashboard pesanan, kirim berkas cetak, dan kelola saldo reward point Anda.';
                submitButtonText.textContent = 'Masuk ke Akun Pelanggan';
                leftRoleText.textContent = 'Portal Pelanggan & Member';

                if (customerRegisterBanner) {
                    customerRegisterBanner.style.display = 'block';
                }
                if (adminNoticeBanner) {
                    adminNoticeBanner.style.display = 'none';
                }
            }

            // Sync URL query without reloading
            const url = new URL(window.location);
            url.searchParams.set('tab', role);
            window.history.replaceState({}, '', url);
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Modal Controls
        function openRegisterModal() {
            const modal = document.getElementById('registerModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRegisterModal();
            }
        });

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            switchLoginRole(currentRole);
        });
    </script>
</body>
</html>
