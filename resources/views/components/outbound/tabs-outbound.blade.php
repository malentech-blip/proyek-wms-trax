<div class="w-full">
    @php
        // Ganti nama route di bawah ini sesuai dengan nama route Anda yang sebenarnya
        $currentRoute = Route::currentRouteName();
        $isPLActive = Str::contains($currentRoute, 'packing-lists');
        $isTIActive = Str::contains($currentRoute, 'transit-inventory');
        $isDOActive = Str::contains($currentRoute, 'delivery-orders');

        if (!$isPLActive && !$isTIActive && !$isDOActive) {
            $isPLActive = true;
        }
    @endphp

    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.outbound.packing-lists.detail', $packingId) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isPLActive ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $isPLActive ? 'page' : 'false' }}">
                Packing Lists
            </a>
            @if ($status !== 'Packed')
                <a href="{{ route('admin.outbound.transit-inventory.detail', $packingId) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isTIActive
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    aria-current="{{ $isTIActive ? 'page' : 'false' }}">
                    Transit Inventory
                </a>
            @endif
            @if ($status !== 'Packed')
                <a href="{{ route('admin.outbound.delivery-orders.detail', $packingId) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isDOActive
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    aria-current="{{ $isDOActive ? 'page' : 'false' }}">
                    Delivery Orders
                </a>
            @endif
        </nav>
    </div>
</div>
