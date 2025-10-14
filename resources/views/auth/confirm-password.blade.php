<x-guest-layout>
    <div class="w-full max-w-md px-8 py-10 bg-white/80 backdrop-blur-sm border border-white/20 shadow-2xl overflow-hidden rounded-2xl fade-in-up">
        
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-800">Akses Aman</h2>
        </div>

        <div class="my-6 text-sm text-center text-gray-600">
            {{ __('Ini adalah area aman dari aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.') }}
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="fade-in-up delay-100">
                <x-input-label for="password" :value="__('Password')" class="sr-only"/>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                    </span>
                    <x-text-input id="password" class="block w-full pl-10 pr-3 py-3 bg-white/50 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" 
                                    placeholder="Masukkan Password Anda"/>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-8 fade-in-up delay-200">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#5C6B94] border border-transparent rounded-lg font-semibold text-base text-white tracking-widest hover:bg-[#4a5675] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                    {{ __('Konfirmasi') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>