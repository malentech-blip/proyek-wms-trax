<x-guest-layout>
    {{-- Main Card with Animation --}}
    <div class="w-full max-w-md px-8 py-10 bg-white/80 backdrop-blur-sm border border-white/20 shadow-2xl overflow-hidden rounded-2xl fade-in-up">
    
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-800">Lupa Password?</h2>
        </div>

        <div class="my-6 text-sm text-center text-gray-600">
            {{ __('Tidak masalah. Cukup beritahu kami alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="fade-in-up delay-100">
                <x-input-label for="email" :value="__('Email')" class="sr-only"/>
                 <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 012-2h10a2 2 0 012 2v1.158a.75.75 0 01-.22.53l-6 6a.75.75 0 01-1.06 0l-6-6A.75.75 0 013 5.158V4z" /><path d="M3 6.885v8.115a2 2 0 002 2h10a2 2 0 002-2V6.885l-6.22 6.22a.75.75 0 01-1.06 0L3 6.885z" /></svg>
                    </span>
                    <x-text-input id="email" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan Email Anda" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-center mt-8 fade-in-up delay-200">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#5C6B94] border border-transparent rounded-lg font-semibold text-base text-white tracking-widest hover:bg-[#4a5675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                    {{ __('Kirim Link Reset Password') }}
                </button>
            </div>
            
            <div class="text-center mt-6 fade-in-up delay-300">
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors" href="{{ route('login') }}">
                    Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>