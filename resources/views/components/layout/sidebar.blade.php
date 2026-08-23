@php
    $roleName = auth()->check() ? auth()->user()->role->role_name : 'Guest';
@endphp

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

<!-- Sidebar -->
<aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary text-white transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0 shadow-2xl flex flex-col border-r border-primary-dark">
    <!-- Branding -->
    <div class="pt-8 pb-6 px-4 flex flex-col items-center text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-transparent"></div>
        <!-- Logo -->
        <img src="{{ asset('images/logo-kpu.png') }}" alt="Logo Komisi Pemilihan Umum" class="mx-auto h-24 w-24 object-contain mb-4 relative z-10 drop-shadow-md hover:scale-110 hover:rotate-3 transition-transform duration-300 cursor-pointer">
        
        <h1 class="text-[11px] font-bold text-accent tracking-[0.2em] uppercase mb-1.5 relative z-10">
            KPU PROVINSI SUMATERA SELATAN
        </h1>
        <p class="text-[10px] leading-relaxed text-gray-200 font-medium tracking-wide uppercase mb-3 relative z-10 opacity-90">
            Sistem Informasi Pelaporan<br>Kematian Pemilih
        </p>
        <div class="px-4 py-1.5 bg-black/20 rounded-full border border-white/10 backdrop-blur-md relative z-10 shadow-inner">
            <span class="text-lg font-black text-white tracking-widest drop-shadow-sm">SIPKP</span>
        </div>
    </div>

    <!-- User Info Mini -->
    <div class="px-5 py-4 border-y border-white/10 bg-black/10 backdrop-blur-sm">
        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-0.5">Akses Sistem</p>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-400 shadow-[0_0_8px_rgba(74,222,128,0.5)]"></span>
            <p class="font-bold text-white text-sm tracking-wide">{{ $roleName }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 custom-scrollbar">
        <ul class="space-y-1.5 px-3">
            
            @if(strtolower($roleName) === 'operator provinsi' || strtolower($roleName) === 'operator')
                <li>
                    <a href="{{ route('operator.dashboard') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('operator.dashboard') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('operator.dashboard') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium">Dashboard Global</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('operator.monitoring') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('operator.monitoring') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('operator.monitoring') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm font-medium">Monitoring Laporan</span>
                    </a>
                </li>
                @can('manage-master-data')
                <li>
                    <a href="{{ route('operator.master-data.index') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('operator.master-data.*') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('operator.master-data.*') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        <span class="text-sm font-medium">Master Data</span>
                    </a>
                </li>
                @endcan
            @elseif(strtolower($roleName) === 'sub operator')
                <li>
                    <a href="{{ route('sub_operator.dashboard') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('sub_operator.dashboard') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('sub_operator.dashboard') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium">Dashboard Wilayah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('sub_operator.antrean') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('sub_operator.antrean') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('sub_operator.antrean') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="text-sm font-medium">Antrean Verifikasi</span>
                    </a>
                </li>
            @elseif(strtolower($roleName) === 'pelapor')
                <li>
                    <a href="{{ route('pelapor.dashboard') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('pelapor.dashboard') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('pelapor.dashboard') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium">Dashboard Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.laporan.create') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('pelapor.laporan.create') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('pelapor.laporan.create') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm font-medium">Buat Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.profile.edit') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('pelapor.profile.*') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('pelapor.profile.*') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="text-sm font-medium">Profil Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.laporan.index') }}" class="relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('pelapor.laporan.index') ? 'bg-white/10 text-white font-bold shadow-sm before:absolute before:left-0 before:top-1/4 before:bottom-1/4 before:w-1.5 before:bg-yellow-400 before:rounded-r-md' : 'text-gray-200 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('pelapor.laporan.index') ? 'text-accent' : 'opacity-75' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="text-sm font-medium">Laporan Saya</span>
                    </a>
                </li>
            @else
                <!-- Guest fallback -->
                <li>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-gray-200 hover:bg-white/5 hover:text-white">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium">Home</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    
    <!-- Footer / Logout -->
    <div class="p-4 border-t border-white/10 bg-black/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-red-300 hover:bg-red-500/20 hover:text-white transition-colors border border-transparent hover:border-red-500/30">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="text-sm font-medium">Logout Sistem</span>
            </button>
        </form>
    </div>
</aside>

