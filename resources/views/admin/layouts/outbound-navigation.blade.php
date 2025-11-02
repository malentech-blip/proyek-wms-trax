<div class="flex flex-col h-full text-gray-800 bg-white">
    <div class="h-[64px] border-b flex items-center flex-shrink-0" :class="sidebarOpen ? 'px-4' : 'justify-center'">
        <a href="{{ route('admin.outbound.dashboard') }}" class="flex items-center space-x-3">
            <div class="w-8 h-8 flex items-center justify-center bg-blue-600 rounded-md text-white font-bold text-lg">W</div>
            <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                <span class="font-bold text-lg text-gray-800">TRAX WMS</span>
            </div>
        </a>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-2">
        @php
            $linkClasses = 'flex items-center p-3 rounded-lg text-sm font-medium transition-colors duration-200 group';
            $activeClasses = 'bg-blue-600 text-white';
            $inactiveClasses = 'text-gray-600 hover:bg-gray-100';
            $iconClasses = 'w-6 h-6 flex-shrink-0 transition-colors duration-200';
        @endphp

        <a href="{{ route('admin.outbound.dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.dashboard') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        <a href="{{ route('admin.inbound.purchase-orders.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('admin.inbound.purchase-orders.index') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Sales Order</span>
        </a>

        <a href="#" class="{{ $linkClasses }} {{ $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Packing List</span>
        </a>
        <a href="#" class="{{ $linkClasses }} {{ $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Transit Inventory</span>
        </a>
        <a href="{{ route('admin.outbound.delivery-orders.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.delivery-orders.*') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Delivery Order</span>
        </a>
        <a href="#" class="{{ $linkClasses }} {{ $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Outbound Report</span>
        </a>

        <a href="#" class="{{ $linkClasses }} {{ $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>History</span>
        </a>
    </nav>
</div>