<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="SIPKP">
    <meta name="description" content="Sistem Informasi Pelaporan Kematian Pemilih KPU Provinsi Sumatera Selatan">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'SIPKP') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/kpu-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/kpu-logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Scripts/Styles -->
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback if Vite is not running -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#8B0000',
                            'primary-dark': '#660000',
                            'primary-light': '#B22222',
                            accent: '#D4AF37',
                            background: '#F8FAFC',
                            surface: '#FFFFFF',
                            text: '#1E293B',
                            muted: '#64748B',
                            success: '#10B981',
                            warning: '#F59E0B',
                            danger: '#EF4444',
                            info: '#3B82F6',
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif
</head>
<body class="bg-background text-text font-sans antialiased flex h-screen overflow-x-hidden overflow-y-hidden">

    <!-- Sidebar Component -->
    <x-layout.sidebar />

    <div class="flex flex-col flex-1 w-full lg:ml-64">
        <!-- Topbar Component -->
        <x-layout.topbar />

        @auth
            @if(auth()->user()->role->role_name === 'Pelapor')
                <div class="bg-blue-50 border-b border-blue-200 px-4 py-2.5 text-center shadow-sm shrink-0 flex flex-col justify-center items-center">
                    <div class="font-bold text-blue-800">
                        Portal Layanan Masyarakat
                    </div>
                    <div class="text-[13px] text-blue-600 font-medium mt-0.5">
                        KPU Provinsi Sumatera Selatan
                    </div>
                </div>
            @elseif(auth()->user()->role->role_name === 'Sub Operator')
                <div class="bg-orange-50 border-b border-orange-200 px-4 py-2.5 text-center shadow-sm shrink-0 flex flex-col justify-center items-center">
                    <div class="font-bold text-orange-800">
                        Panel Verifikasi Wilayah
                    </div>
                    <div class="text-[13px] text-orange-600 font-medium mt-0.5">
                        {{ auth()->user()->district->name ?? 'Sub-Operator' }}
                    </div>
                </div>
            @elseif(auth()->user()->role->role_name === 'Operator Provinsi')
                <div class="bg-red-50 border-b border-red-200 px-4 py-2.5 text-center shadow-sm shrink-0 flex flex-col justify-center items-center">
                    <div class="font-bold text-red-800">
                        Dasbor Monitoring Pusat
                    </div>
                    <div class="text-[13px] text-red-600 font-medium mt-0.5">
                        Operator Provinsi
                    </div>
                </div>
            @endif
        @endauth

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-background w-full">
            <div class="mx-auto max-w-[1440px] w-full px-4 md:px-6 py-6 lg:py-8">
                <!-- Toast / Alert placeholder -->
                <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Script for interactive mobile menu (minimal JS without Alpine for now to keep dependencies low) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('app-sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('hidden');
                    }
                });
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
