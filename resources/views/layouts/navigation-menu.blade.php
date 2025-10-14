@auth
    @if (Auth::user()->hasRole('Super Admin'))
        {{-- Jika user adalah Super Admin, muat menu navigasi super admin --}}
        @include('layouts.partials.navigation-superadmin')

    @elseif (Auth::user()->hasRole('Admin Inbound'))
        {{-- Jika user adalah Admin Inbound, muat menu navigasi inbound --}}
        @include('layouts.partials.navigation-inbound')

    {{-- Tambahkan @elseif untuk role lain di sini nanti --}}
    {{--
    @elseif (Auth::user()->hasRole('Admin Production'))
        @include('layouts.partials.navigation-production')
    --}}

    @else
        {{-- Menu default jika tidak ada role yang cocok (opsional) --}}
        <p class="p-4 text-sm text-red-500">No navigation menu assigned for this role.</p>
    @endif
@endauth