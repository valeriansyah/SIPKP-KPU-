<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Tidak Ditemukan - SIPKP</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kpu.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-kpu.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden text-center">
        <div class="bg-[#8B0000] p-6 flex justify-center">
            <img src="{{ asset('images/logo-kpu.png') }}" alt="Logo KPU" class="h-20 w-20 object-contain drop-shadow-md">
        </div>
        <div class="p-8">
            <h1 class="text-6xl font-black text-[#8B0000] mb-2 tracking-tighter">404</h1>
            <h2 class="text-xl font-bold text-gray-800 mb-4 uppercase tracking-wide">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Maaf, halaman yang Anda cari tidak tersedia, telah dipindahkan, atau Anda salah memasukkan URL.
            </p>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#8B0000] text-white font-medium rounded hover:bg-[#660000] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8B0000] shadow-sm">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>
