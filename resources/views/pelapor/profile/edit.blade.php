@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Profil Saya</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 relative">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <form action="{{ route('pelapor.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email (Google)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight">
                    <p class="text-xs text-gray-500 mt-1">Email Anda tertaut dengan akun Google dan tidak dapat diubah.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">Nama Lengkap</label>
                    <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-600 @error('full_name') border-red-500 @enderror">
                    @error('full_name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">Nomor HP</label>
                    <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number == '-' ? '' : $user->phone_number) }}" placeholder="08..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-600 @error('phone_number') border-red-500 @enderror">
                    @error('phone_number')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
