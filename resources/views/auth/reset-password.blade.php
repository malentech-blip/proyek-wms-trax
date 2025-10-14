<x-guest-layout>
    <div class="w-full max-w-md px-8 py-10 bg-white/80 backdrop-blur-sm border border-white/20 shadow-2xl overflow-hidden rounded-2xl fade-in-up">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Atur Password Baru</h2>
            <p class="text-gray-600 mt-2">Buat password baru yang kuat.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="fade-in-up delay-100">
                <x-input-label for="email" :value="__('Email')" class="sr-only"/>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 012-2h10a2 2 0 012 2v1.158a.75.75 0 01-.22.53l-6 6a.75.75 0 01-1.06 0l-6-6A.75.75 0 013 5.158V4z" /><path d="M3 6.885v8.115a2 2 0 002 2h10a2 2 0 002-2V6.885l-6.22 6.22a.75.75 0 01-1.06 0L3 6.885z" /></svg>
                    </span>
                    <x-text-input id="email" class="block w-full pl-10 pr-3 py-3 bg-gray-100/80 border-gray-300 rounded-lg shadow-sm" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" readonly />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4 fade-in-up delay-200">
                <x-input-label for="password" :value="__('Password')" class="sr-only"/>
                <div class="relative">
                     <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="password" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password" required autocomplete="new-password" placeholder="Password Baru"/>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4 fade-in-up delay-300">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="sr-only"/>
                <div class="relative">
                     <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="password_confirmation" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password Baru"/>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-8 fade-in-up delay-400">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#5C6B94] border border-transparent rounded-lg font-semibold text-base text-white tracking-widest hover:bg-[#4a5675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>