<x-app-layout>
    <x-slot name="header">
        Delivery Orders
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Delivery Orders</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Filter Delivery Order</h3>
                
                <form method="GET" action="{{ route('admin.outbound.delivery-orders.index') }}">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="search" class="text-sm font-medium text-gray-700">Cari DO Number / Driver</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" 
                                placeholder="Cari nomor DO atau driver...">
                        </div>
                        <div>
                            <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="In Delivery" {{ request('status') === 'In Delivery' ? 'selected' : '' }}>In Delivery</option>
                                <option value="Delivered" {{ request('status') === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                                Cari
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.outbound.delivery-orders.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">DO Number</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Sales Order</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Driver Name</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Delivery Date</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($deliveryOrders as $do)
                        <tr>
                            <td class="p-4 text-gray-700 font-medium">{{ $do->delivered_no }}</td>
                            <td class="p-4 text-gray-500">{{ $do->packingList?->salesOrder?->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $do->driver_name }}</td>
                            <td class="p-4 text-gray-500">
                                {{ $do->delivery_date ? \Carbon\Carbon::parse($do->delivery_date)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="p-4">
                                @if($do->status === 'Delivered')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $do->status }}
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $do->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if($do->status !== 'Delivered')
                                        <form method="POST" action="{{ route('admin.outbound.delivery-orders.mark-delivered', $do->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                onclick="return confirm('Are you sure you want to mark this delivery order as Delivered?')"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                                Mark Delivered
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.outbound.delivery-orders.print-pdf', $do->id) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                        Download PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-12 text-gray-500">
                                Tidak ada data Delivery Order ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deliveryOrders->hasPages())
                <div class="p-4 border-t">
                    {{ $deliveryOrders->links() }}
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>

