<x-guest-layout>
    {{-- 
        Catatan: Layout ini mengasumsikan `x-guest-layout` membungkus konten di dalam
        sebuah container flex yang berada di tengah halaman dengan latar belakang gradien.
        Jika tidak, Anda perlu menambahkan wrapper-nya di sini seperti pada file login.blade.php
        yang pernah saya buat.
    --}}

    {{-- Registration Card with Glassmorphism Effect and Animation --}}
    <div class="w-full max-w-md px-8 py-10 bg-white/80 backdrop-blur-sm border border-white/20 shadow-2xl overflow-hidden rounded-2xl fade-in-up">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Buat Akun Baru</h2>
            <p class="text-gray-600 mt-2">Isi form di bawah untuk mendaftar.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="fade-in-up delay-100">
                <x-input-label for="name" :value="__('Name')" class="sr-only" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="name" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Lengkap"/>
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4 fade-in-up delay-200">
                <x-input-label for="email" :value="__('Email')" class="sr-only"/>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 012-2h10a2 2 0 012 2v1.158a.75.75 0 01-.22.53l-6 6a.75.75 0 01-1.06 0l-6-6A.75.75 0 013 5.158V4z" /><path d="M3 6.885v8.115a2 2 0 002 2h10a2 2 0 002-2V6.885l-6.22 6.22a.75.75 0 01-1.06 0L3 6.885z" /></svg>
                    </span>
                    <x-text-input id="email" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Alamat Email"/>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4 fade-in-up delay-300">
                <x-input-label for="password" :value="__('Password')" class="sr-only"/>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="password" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password" required autocomplete="new-password" placeholder="Password"/>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4 fade-in-up delay-400">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="sr-only"/>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="password_confirmation" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password"/>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            
            <div class="mt-4 fade-in-up delay-500">
                <x-input-label for="role" :value="__('Daftar sebagai')" class="sr-only" />
                 <div class="relative">
                     <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" /></svg>
                    </span>
                    <select id="role" name="role" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 rounded-lg shadow-sm appearance-none">
                        <option value="sales">Sales</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 fade-in-up delay-[600ms]">
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-[#5C6B94] border border-transparent rounded-lg font-semibold text-base text-white hover:bg-[#4a5675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>