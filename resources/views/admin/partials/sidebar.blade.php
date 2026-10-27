@php
    $active = $active ?? 'dashboard';
    $activeCount = $activeAntreanCount ?? (\App\Models\AntreanMesin::where('status_antrean', '!=', 'selesai')->count());
@endphp

<aside class="w-full md:w-64 lg:w-72 sidebar-bg flex-shrink-0 flex flex-col justify-between p-4 md:p-5 border-r border-blue-100/60 sticky top-0 md:h-screen z-40 select-none">
    <div class="space-y-6">
        
        <!-- Brand Logo Matahari Fotocopy -->
        <div class="flex flex-col items-center justify-center pt-1 pb-1">
            <a href="{{ route('admin.dashboard') }}" 
               @if($active === 'dashboard') onclick="event.preventDefault(); return false;" @endif
               class="block group">
                <img src="{{ asset('images/logo.png') }}" alt="Matahari Fotocopy" class="w-32 h-auto max-h-24 object-contain drop-shadow-xs transition transform group-hover:scale-105 duration-200">
            </a>
        </div>

        <!-- Cashier / Admin Profile Badge Card -->
        <div class="bg-white rounded-3xl p-3 shadow-xs border border-white/80 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-[#dce8f5] text-slate-700 font-extrabold flex items-center justify-center text-xs tracking-wider shadow-2xs">
                    FA
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 leading-tight">
                        {{ $user->nama ?? 'Admin Matahari (Uji)' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 font-medium leading-normal mt-0.5">
                        {{ $user->adminKasir->shift_kerja ?? 'Pagi-Sore' }}
                    </p>
                </div>
            </div>
            <!-- Active Online Indicator -->
            <div class="relative flex items-center pr-1">
                <span class="w-3.5 h-3.5 rounded-full bg-amber-400 ring-4 ring-amber-100"></span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1.5 pt-1" id="sidebar-nav">
            <!-- 1. Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               data-menu="dashboard"
               data-active="{{ $active === 'dashboard' ? 'true' : 'false' }}"
               @if($active === 'dashboard') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'dashboard' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'dashboard' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Antrean Mesin -->
            <a href="{{ route('admin.antrean.index') }}" 
               data-menu="antrean"
               data-active="{{ $active === 'antrean' ? 'true' : 'false' }}"
               @if($active === 'antrean') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'antrean' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'antrean' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Antrean Mesin</span>
                </div>
                @if($activeCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold font-mono shadow-2xs {{ $active === 'antrean' ? 'bg-white text-amber-700' : 'bg-white text-slate-700' }}">
                    {{ $activeCount }}
                </span>
                @endif
            </a>

            <!-- 3. Kasir Walk-in -->
            <a href="{{ route('admin.walkin.index') }}" 
               data-menu="walkin"
               data-active="{{ $active === 'walkin' ? 'true' : 'false' }}"
               @if($active === 'walkin') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'walkin' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'walkin' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Kasir Walk-in</span>
            </a>

            <!-- 4. Manajemen Stok -->
            <a href="{{ route('admin.stok.index') }}" 
               data-menu="stok"
               data-active="{{ $active === 'stok' ? 'true' : 'false' }}"
               @if($active === 'stok') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'stok' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'stok' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Manajemen Stok</span>
            </a>

            <!-- 5. Data Member -->
            <a href="{{ route('admin.member.index') }}" 
               data-menu="member"
               data-active="{{ $active === 'member' ? 'true' : 'false' }}"
               @if($active === 'member') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'member' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'member' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Data Member</span>
            </a>

            <!-- 6. Laporan Omzet -->
            <a href="{{ route('admin.laporan.index') }}" 
               data-menu="laporan"
               data-active="{{ $active === 'laporan' ? 'true' : 'false' }}"
               @if($active === 'laporan') onclick="event.preventDefault(); return false;" @endif
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition select-none {{ $active === 'laporan' ? 'bg-[#f59e0b] text-white shadow-sm cursor-default' : 'text-slate-800 hover:bg-white/40 hover:text-slate-900 active:bg-[#f59e0b] active:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active === 'laporan' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                <span>Laporan Omzet</span>
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer Status & Logout -->
    <div class="pt-4 border-t border-blue-200/50 space-y-3">
        <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 font-medium">
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs text-blue-900/60 font-semibold font-mono">
                    {{ $active === 'laporan' ? 'Analitik Terkini' : ($active === 'stok' ? 'Logistik Siap' : ($active === 'antrean' ? 'Workshop Siap' : 'Sistem Aktif')) }}
                </span>
            </span>
            <span class="text-xs text-blue-900/50 font-mono font-medium">
                {{ $user->adminKasir->shift_kerja ?? 'Pagi-Sore' }}
            </span>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-2xl text-xs font-bold text-slate-700 hover:text-red-600 hover:bg-white border border-white/80 bg-white/90 shadow-2xs transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar Sistem</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // Seamless active click state & prevent jump to top on already active menu
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#sidebar-nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('data-active') === 'true') {
                    e.preventDefault();
                    return false;
                }
                // Instantly show active amber styling before page loads
                navLinks.forEach(el => {
                    el.classList.remove('bg-[#f59e0b]', 'text-white', 'shadow-sm', 'cursor-default');
                    el.classList.add('text-slate-800', 'hover:bg-white/40');
                    const svg = el.querySelector('svg');
                    if (svg) {
                        svg.classList.remove('text-white');
                        svg.classList.add('text-slate-600');
                    }
                });
                this.classList.remove('text-slate-800', 'hover:bg-white/40');
                this.classList.add('bg-[#f59e0b]', 'text-white', 'shadow-sm');
                const activeSvg = this.querySelector('svg');
                if (activeSvg) {
                    activeSvg.classList.remove('text-slate-600');
                    activeSvg.classList.add('text-white');
                }
            });
        });
    });
</script>

