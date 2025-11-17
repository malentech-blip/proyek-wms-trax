<x-app-layout>
    <x-slot name="header">
        List Finished Goods (Production)
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Finished Goods</h3>
            <form method="GET">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="create_date" class="text-sm font-medium text-gray-700">
                            Tanggal Produksi
                        </label>
                        <input type="date" name="create_date" id="create_date" onblur="this.form.submit()"
                            value="{{ request('create_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Cari Finished Goods</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="flex-1 block w-full border-gray-300 rounded-none rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari finished goods berdasarkan keyword apapun...">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-md hover:bg-gray-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">Batch</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">Status QC</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Pallet</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($finishedGoods as $finishedGood)
                        <tr class="cursor-pointer"
                            onclick="window.location='{{ route('admin.production.finished-goods.detail', $finishedGood->id) }}'">
                            <td class="p-4 text-gray-500">{{ $finishedGood->item->item_code }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->item->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->quantity }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->batch_no }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->qc_status }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->location->name }}
                            </td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->rack->code }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->pallet->code }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                Belum ada finished goods.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
