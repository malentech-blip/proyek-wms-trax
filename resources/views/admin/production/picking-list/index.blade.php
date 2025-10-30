<x-app-layout>
    <x-slot name="header">
        List Picking List (Production)
    </x-slot>

    <div x-data="{
        open: false,
        qr_code: '',
        scanning: false,
        scanResult: null,
        submittingConfirm: false,
    
        openModal(id) {
            this.qr_code = '';
            this.scanResult = null;
            this.open = true;
            this.$nextTick(() => this.$refs.qrInput.focus());
        },
    
        async submitScan() {
            this.scanning = true;
            try {
                const res = await fetch('{{ route('admin.production.picking-list.scan-item') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_code: this.qr_code,
                    }),
                });
                const data = await res.json();
                this.scanning = false;
    
                console.log(this.qr_code)
    
                if (data.success) {
                    this.scanResult = data.data;
                } else {
                    alert('❌ ' + data.message);
                    this.scanResult = null;
                }
            } catch (err) {
                this.scanning = false;
                console.error(err);
                alert('Terjadi error: ' + err.message);
            }
        },
    
        async confirmPick() {
            if (!this.scanResult) return;
    
            this.submittingConfirm = true;
            try {
                const res = await fetch('{{ route('admin.production.picking-list.confirm-pick') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        picking_id: this.scanResult.id,
                    }),
                });
                const data = await res.json();
                this.submittingConfirm = false;
    
                if (data.success) {
                    alert('✅ ' + data.message);
                    this.open = false;
                    window.location.reload(); // Refresh table
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (err) {
                this.submittingConfirm = false;
                alert('Terjadi error: ' + err.message);
            }
        },
    }" class="bg-white rounded-xl shadow-sm">

        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Picking List</h3>

            {{-- FILTER FORM --}}
            <form method="GET">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="date_picked" class="text-sm font-medium text-gray-700">Tanggal Ambil</label>
                        <input type="date" name="date_picked" id="date_picked" value="{{ request('date_picked') }}" onblur="this.form.submit()"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Cari Nama Item/Lokasi</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="flex-1 block w-full border-gray-300 rounded-none rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari nama item atau lokasi item yang mau diambil...">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-md hover:bg-gray-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="w-full overflow-x-auto">
            <table class="min-w-max text-sm border-collapse">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[60px]">No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[60px]">PL. No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[200px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[120px]">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Qty Request</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Qty Ready</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Picked By</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Date Picked</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[100px]">Scan</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($pickingList as $pl)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('admin.production.picking-list.detail', $pl->mr_id) }}'">
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $pl->pl_no }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->location_name ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->rack_code ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->quantity ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->item_quantity ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->picked_by ?? '-' }}</td>
                            <td class="p-4 text-gray-500">
                                @if ($pl->date_picked)
                                    {{ \Carbon\Carbon::parse($pl->date_picked)->format('d/m/Y') }}
                                @else
                                    -Not yet picked-
                                @endif
                            </td>
                            <td class="p-4 text-gray-500">
                                @if (!$pl->date_picked)
                                    <svg class="w-6 h-6 cursor-pointer text-blue-600 hover:text-blue-800"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                        @click="openModal({{ $pl->id }})">
                                        <path
                                            d="M257.1 96C238.4 96 220.9 105.4 210.5 120.9L184.5 160L128 160C92.7 160 64 188.7 64 224L64 480C64 515.3 92.7 544 128 544L512 544C547.3 544 576 515.3 576 480L576 224C576 188.7 547.3 160 512 160L455.5 160L429.5 120.9C419.1 105.4 401.6 96 382.9 96L257.1 96zM250.4 147.6C251.9 145.4 254.4 144 257.1 144L382.8 144C385.5 144 388 145.3 389.5 147.6L422.7 197.4C427.2 204.1 434.6 208.1 442.7 208.1L512 208.1C520.8 208.1 528 215.3 528 224.1L528 480.1C528 488.9 520.8 496.1 512 496.1L128 496C119.2 496 112 488.8 112 480L112 224C112 215.2 119.2 208 128 208L197.3 208C205.3 208 212.8 204 217.3 197.3L250.5 147.5zM320 448C381.9 448 432 397.9 432 336C432 274.1 381.9 224 320 224C258.1 224 208 274.1 208 336C208 397.9 258.1 448 320 448zM256 336C256 300.7 284.7 272 320 272C355.3 272 384 300.7 384 336C384 371.3 355.3 400 320 400C284.7 400 256 371.3 256 336z" />
                                    </svg>
                                @else
                                    -Scanned-
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center p-12 text-gray-500">
                                Tidak ada data Picking List ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Modal Scan -->
        <div x-show="open" x-cloak
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" x-transition>
            <div @click.away="open = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-lg font-semibold mb-4">Scan Item</h2>

                <!-- Input Scan -->
                <form @submit.prevent="submitScan" x-show="!scanResult">
                    <input type="text" x-model="qr_code" x-ref="qrInput" placeholder="Scan QR Code di sini..."
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md"
                            x-text="scanning ? 'Memproses...' : 'Verifikasi'"></button>
                    </div>
                </form>

                <!-- Hasil Scan -->
                <div x-show="scanResult" class="space-y-3">
                    <div class="border-t pt-4 mt-4">
                        <p><strong>Item:</strong> <span x-text="scanResult.item_name"></span></p>
                        <p><strong>QR Code:</strong> <span x-text="scanResult.qr_code"></span></p>
                        <p><strong>Qty Request:</strong> <span x-text="scanResult.quantity"></span></p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button @click="open = false"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Tutup</button>
                        <button @click="confirmPick" class="px-4 py-2 bg-green-600 text-white rounded-md"
                            x-text="submittingConfirm ? 'Menyimpan...' : 'Confirm Pick'"></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
