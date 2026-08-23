<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="SIPKP">
    <meta name="description" content="Sistem Informasi Pelaporan Kematian Pemilih KPU Provinsi Sumatera Selatan">
    <title>{{ $title ?? config('app.name', 'SIPKP') }} - Login</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kpu.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-kpu.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-surface rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
        <!-- Header Section -->
        <div class="bg-primary px-8 py-10 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary-light rounded-full opacity-20 blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-primary-dark rounded-full opacity-40 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <!-- Logo -->
                <img src="{{ asset('images/logo-kpu.png') }}" alt="Logo Komisi Pemilihan Umum" class="h-28 w-28 object-contain mb-4 relative z-10 drop-shadow-md">
                <h1 class="text-white text-xs font-bold tracking-widest uppercase mb-2 opacity-90">KPU Provinsi Sumatera Selatan</h1>
                <h2 class="text-white text-2xl font-extrabold tracking-tight">SIPKP Login</h2>
                <p class="text-primary-100 text-sm mt-2 max-w-xs mx-auto">Sistem Informasi Pelaporan Kematian Pemilih</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm" onsubmit="document.getElementById('submitBtn').disabled=true; document.getElementById('submitText').classList.add('hidden'); document.getElementById('loadingText').classList.remove('hidden');">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    {{ $errors->first() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-text mb-2">Alamat Email / Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full pl-10 px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-colors" 
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-text">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="appearance-none block w-full pl-10 px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-colors"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-primary-light group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <span id="submitText">Masuk ke SIPKP</span>
                        <span id="loadingText" class="hidden flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-surface text-gray-500">Atau masuk dengan</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google.redirect') }}" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Daftar / Masuk sebagai Pelapor
                    </a>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-xs text-muted">
                    Pastikan Anda memasukkan data yang valid.<br>Aktivitas Anda direkam secara log.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
