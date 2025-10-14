<section class="bg-white p-6 rounded-xl shadow-sm border border-red-200">
    <header>
        <h2 class="text-lg font-semibold text-red-700">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <div class="mt-6">
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-colors"
        >{{ __('Delete Account') }}</button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
            </p>

            <div class="mt-6">
                <label for="password_delete" class="block mb-2 text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                <input
                    id="password_delete"
                    name="password"
                    type="password"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')" class="bg-white hover:bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-lg border border-gray-300">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>