<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>
    <div
        {{-- BARU: Wrapper untuk animasi fade-in seluruh halaman --}}
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 50)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="py-12"
    >
        <div 
            class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8"
            {{-- BARU: Alpine.js untuk animasi staggered pada kartu --}}
            x-data
            x-init="
                $el.querySelectorAll('.animated-card').forEach((card, index) => {
                    card.style.opacity = 0;
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                        card.style.opacity = 1;
                        card.style.transform = 'translateY(0)';
                    }, 100 * index);
                })
            "
        >
            {{-- DIUBAH: Header dipindahkan ke dalam halaman untuk konsistensi --}}
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Edit Profil</h2>
                <p class="mt-1 text-base text-gray-500">Perbarui detail akun dan keamanan Anda di sini.</p>
            </div>

            {{-- DIUBAH: Setiap bagian sekarang dibungkus dalam div terpisah untuk animasi --}}
            <div class="animated-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="animated-card">
                 @include('profile.partials.update-password-form')
            </div>

            <div class="animated-card">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>