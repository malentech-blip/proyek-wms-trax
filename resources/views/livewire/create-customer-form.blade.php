<div>
    @if ($showModal)
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6" @click.away="show = false">
            
            <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-4">Buat Customer Baru</h3>

            <form wire:submit.prevent="saveCustomer">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama Customer <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.lazy="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model.lazy="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" wire:model.lazy="phone" id="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    @error('general')
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3">
                            <p>{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <button type="button" x-on:click="show = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                        <span wire:loading.remove>Simpan Customer</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>