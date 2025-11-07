<x-app-layout>
    <x-slot name="header">
        Sales Orders
    </x-slot>

    <div class="space-y-6">
        <form method="GET" class="bg-white p-4 rounded-xl shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari nomor / customer" class="border rounded-lg px-3 py-2 w-full" />
            <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="border rounded-lg px-3 py-2 w-full" />
            <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="border rounded-lg px-3 py-2 w-full" />
            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('admin.outbound.sales-orders.index') }}" class="px-4 py-2 rounded-lg border">Reset</a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3">SO Number</th>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Customer</th>
                        <th class="text-left px-4 py-3">Amount</th>
                        <th class="text-left px-4 py-3">Local Status</th>
                        <th class="text-left px-4 py-3">Sync Status Status</th>
                        <th class="text-left px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($salesOrders as $so)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium">{{ $so['number'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                          {{ $so['transDate'] ?? "-" }}
                        </td>
                        <td class="px-4 py-3">{{ $so['customer']['name'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ number_format($so['totalAmount'] ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($so['localStatus']) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Packed' => 'bg-blue-100 text-blue-800',
                                    'Shipped' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs {{ $badge }}">{{ $so['localStatus'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($so['localStatus']) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Packed' => 'bg-blue-100 text-blue-800',
                                    'Shipped' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs {{ $badge }}">{{ $so['syncStatus'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.outbound.sales-orders.create-packing-list', ['so_id' => $so['id']]) }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm">Create Packing List</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No sales orders found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


