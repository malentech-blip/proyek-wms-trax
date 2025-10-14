<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.set('showModal', false)">
            <form wire:submit.prevent="save" class="w-full">
                <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tambah Pengguna Baru</h3></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Nama Pengguna</label>
                        <input type="text" wire:model="name" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Username (Email)</label>
                        <input type="email" wire:model="email" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <input type="password" wire:model="password" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Role</label>
                        <select wire:model="role" class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">Pilih Role</option>
                            @if($roles)
                                @foreach($roles as $roleName)
                                    <option value="{{ $roleName }}">{{ $roleName }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-2">
                    <button type="button" @click="$wire.set('showModal', false)" class="bg-white border rounded-lg px-4 py-2">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>