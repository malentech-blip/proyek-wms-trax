<x-app-layout>
    <x-slot name="header">
        List Finished Goods (Production)
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Batch</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Status QC</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Pallet</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($finishedGoods as $finishedGood)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('admin.production.finished-goods.detail', $finishedGood->id) }}'">
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
