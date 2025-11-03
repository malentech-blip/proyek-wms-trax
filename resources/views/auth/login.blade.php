<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - {{ config('app.name', 'Laravel') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        {{-- Vite Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        {{-- DIUBAH: Latar belakang diubah menjadi abu-abu terang untuk kontras yang lebih baik --}}
        {{-- DITAMBAHKAN: Alpine.js untuk mengontrol animasi saat halaman dimuat --}}
        <div 
            class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 bg-gray-100"
            x-data="{ loaded: false }"
            x-init="setTimeout(() => loaded = true, 200)"
        >

            {{-- DITAMBAHKAN: Transisi untuk animasi fade-in, slide-up, dan scale-up --}}
            <div 
                class="w-full max-w-sm px-8 py-12 bg-white shadow-xl overflow-hidden rounded-2xl transform transition-all duration-500 ease-out"
                :class="{ 'opacity-100 translate-y-0 scale-100': loaded, 'opacity-0 -translate-y-4 scale-95': !loaded }"
            >
                
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <h1 class="text-2xl font-bold text-gray-800">TRAX WMS</h1>
                    </a>
                </div>

                <h2 class="text-center text-xl font-bold text-gray-800 mb-1">Welcome back!</h2>
                <p class="text-center text-gray-500 mb-8 text-sm">Login to your account</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email Input --}}
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 012-2h10a2 2 0 012 2v1.158a.75.75 0 01-.22.53l-6 6a.75.75 0 01-1.06 0l-6-6A.75.75 0 013 5.158V4z" /><path d="M3 6.885v8.115a2 2 0 002 2h10a2 2 0 002-2V6.885l-6.22 6.22a.75.75 0 01-1.06 0L3 6.885z" /></svg>
                            </span>
                            <x-text-input
                                id="email"
                                {{-- DIUBAH: Disesuaikan stylenya dan ditambahkan class 'transition' --}}
                                class="block w-full pl-10 pr-3 py-2.5 bg-gray-100 border-transparent text-gray-900 placeholder-gray-500 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm transition"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Email Address" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password Input --}}
                    <div class="mt-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                            </span>
                            <x-text-input
                                id="password"
                                {{-- DITAMBAHKAN: class 'transition' untuk efek focus yang halus --}}
                                class="block w-full pl-10 pr-10 py-2.5 bg-gray-100 border-transparent text-gray-900 placeholder-gray-500 focus:border-red-500 focus:ring-red-500 rounded-lg shadow-sm transition"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Password" />
                            
                            <div id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500 hover:text-gray-700 transition-colors duration-200">
                               <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                               <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="text-right mt-2">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-red-600 hover:text-red-700 transition-colors font-medium" href="{{ route('password.request') }}">
                                Ask for User
                            </a>
                        @endif
                    </div>
                    
                    <div class="mt-8">
                        {{-- DIUBAH: Tombol submit diberi efek hover dan active yang lebih interaktif --}}
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-700 border border-transparent rounded-lg font-semibold text-base text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:-translate-y-1 active:scale-95">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword = document.getElementById('togglePassword');
                if (togglePassword) {
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eye-icon');
                    const eyeOffIcon = document.getElementById('eye-off-icon');

                    togglePassword.addEventListener('click', function () {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        eyeIcon.classList.toggle('hidden');
                        eyeOffIcon.classList.toggle('hidden');
                    });
                }
            });
        </script>
    </body>
</html>