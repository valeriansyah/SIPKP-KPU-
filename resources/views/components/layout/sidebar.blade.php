@php
    $roleName = auth()->check() ? auth()->user()->role->role_name : 'Guest';
@endphp

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-40 hidden lg:hidden"></div>

<!-- Sidebar -->
<aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary text-white transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0 lg:static lg:flex-shrink-0 shadow-xl flex flex-col">
    <!-- Branding -->
    <div class="pt-6 pb-4 px-4 border-b border-primary-light/50 flex flex-col items-center text-center">
        <!-- Logo Slot / Placeholder -->
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center p-2 mb-3 shadow-sm">
            <!-- [LOGO KPU] -->
            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        
        <h1 class="text-xs font-bold text-accent tracking-widest uppercase mb-1">
            KPU PROVINSI SUMATERA SELATAN
        </h1>
        <p class="text-[10px] leading-tight text-gray-300 font-medium tracking-wide uppercase mb-2">
            Sistem Informasi Pelaporan<br>Kematian Pemilih
        </p>
        <div class="px-3 py-1 bg-primary-dark rounded-full border border-primary-light/30">
            <span class="text-lg font-extrabold text-white tracking-wider">SIPKP</span>
        </div>
    </div>

    <!-- User Info Mini -->
    <div class="px-4 py-3 border-b border-primary-light/50 bg-black/10">
        <p class="text-[11px] text-gray-300 uppercase tracking-wider font-semibold">Akses Sistem</p>
        <p class="font-bold text-accent text-sm">{{ $roleName }}</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            
            @if(strtolower($roleName) === 'operator provinsi' || strtolower($roleName) === 'operator')
                <li>
                    <a href="{{ route('operator.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('operator.dashboard') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium text-white">Dashboard Global</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('operator.monitoring') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('operator.monitoring') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm font-medium text-white">Monitoring Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors opacity-70 cursor-not-allowed" title="Belum Diimplementasikan">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        <span class="text-sm font-medium text-white">Master Data</span>
                    </a>
                </li>
            @elseif(strtolower($roleName) === 'sub operator')
                <li>
                    <a href="{{ route('sub_operator.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('sub_operator.dashboard') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium text-white">Dashboard Wilayah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('sub_operator.antrean') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('sub_operator.antrean') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="text-sm font-medium text-white">Antrean Verifikasi</span>
                    </a>
                </li>
            @elseif(strtolower($roleName) === 'pelapor')
                <li>
                    <a href="{{ route('pelapor.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('pelapor.dashboard') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium text-white">Dashboard Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.laporan.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('pelapor.laporan.create') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm font-medium text-white">Buat Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('pelapor.profile.*') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="text-sm font-medium text-white">Profil Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pelapor.laporan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors {{ request()->routeIs('pelapor.laporan.index') ? 'bg-primary-light border-l-4 border-accent' : '' }}">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="text-sm font-medium text-white">Laporan Saya</span>
                    </a>
                </li>
            @else
                <!-- Guest fallback -->
                <li>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-md hover:bg-primary-light transition-colors">
                        <svg class="w-5 h-5 opacity-75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="text-sm font-medium text-white">Home</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    
    <!-- Footer / Logout -->
    <div class="p-4 border-t border-primary-light">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 rounded-md text-red-200 hover:bg-primary-dark hover:text-white transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
