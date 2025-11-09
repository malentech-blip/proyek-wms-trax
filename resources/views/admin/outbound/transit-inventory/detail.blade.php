<x-app-layout>
    <x-slot name="header">
        Timeline Outbound (Transtit Inventory)
    </x-slot>

    <x-outbound.tabs-outbound :packingId="$packingList->id" :status="$packingList->status"/>
    <div class="mt-8 bg-white rounded-xl shadow-sm">
       <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Track Packing List</h3>
            <div class="flex gap-3 items-start">
                <form id="qrScanForm" class="flex-1 w-full">
                    <input type="text" id="qrCodeInput" name="qr_code"
                        placeholder="Tulis QR Code/Scan QR code di sini"
                        class="border rounded-lg px-3 py-2 w-full flex-1" required />
                </form>
                <button type="button" id="openCameraScanBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1">
                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Camera
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-700">Sales Order</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Customer Id</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Total Items</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($packingList->status != 'Packed' && $packingList)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors">
                            <td class="p-4 text-gray-500">{{ $packingList->sales_order->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $packingList->sales_order->customer_id ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $packingList->items->count() }} Items</td>
                            <td class="p-4">
                                @php
                                    $color = match ($packingList->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $packingList->status }}
                                </span>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                Tidak ada data Packing List ditemukan.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
