<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIPKP KPU Sumsel') }}</title>
    
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
<body class="bg-background text-text font-sans antialiased flex h-screen overflow-hidden">

    <!-- Sidebar Component -->
    <x-layout.sidebar />

    <div class="flex flex-col flex-1 w-full">
        <!-- Topbar Component -->
        <x-layout.topbar />

        @auth
            @if(auth()->user()->role->role_name === 'Pelapor')
                <div class="bg-blue-50 border-b border-blue-200 px-4 py-2 text-center text-sm text-blue-700 font-medium shadow-sm">
                    Portal Layanan Masyarakat — KPU Provinsi Sumatera Selatan
                </div>
            @elseif(auth()->user()->role->role_name === 'Sub Operator')
                <div class="bg-orange-50 border-b border-orange-200 px-4 py-2 text-center text-sm text-orange-700 font-medium shadow-sm">
                    Panel Verifikasi Wilayah — {{ auth()->user()->district->name ?? 'Sub-Operator' }}
                </div>
            @elseif(auth()->user()->role->role_name === 'Operator Provinsi')
                <div class="bg-red-50 border-b border-red-200 px-4 py-2 text-center text-sm text-red-700 font-medium shadow-sm">
                    Dasbor Monitoring Pusat — Operator Provinsi
                </div>
            @endif
        @endauth

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-background">
            <div class="mx-auto max-w-[1440px] w-full px-4 md:px-4 lg:px-6 py-6 lg:py-8">
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
