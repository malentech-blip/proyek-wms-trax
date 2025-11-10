<div class="flex flex-col h-full text-gray-800 bg-white">
    <div class="h-[64px] border-b flex items-center flex-shrink-0" :class="sidebarOpen ? 'px-4' : 'justify-center'">
        <a href="{{ route('admin.outbound.dashboard') }}" class="flex items-center space-x-3">
            <div class="w-8 h-8 flex items-center justify-center bg-blue-600 rounded-md text-white font-bold text-lg">W
            </div>
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
        <a href="{{ route('admin.outbound.dashboard') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.dashboard') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>
        <a href="{{ route('admin.outbound.sales-orders.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.sales-orders.*') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Sales Orders</span>
        </a>

        <div x-data="{ open: {{ request()->routeIs('admin.outbound.packing-lists.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.outbound.packing-lists.*') ? 'bg-gray-100 text-gray-600' : $inactiveClasses }}"
                :class="!sidebarOpen && 'justify-center'">
                <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
                </svg>


                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Packing
                        Lists</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.outbound.packing-lists.create') }}"
                        class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.outbound.packing-lists.create') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Create
                        Packing List</a>
                </div>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.outbound.packing-lists.index') }}"
                        class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.outbound.packing-lists.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">List
                        Packing Lists</a>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.outbound.transit-inventory.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.transit-inventory.*') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.14294 20v-9l-4 1.125V20h4Zm0 0V6.66667m0 13.33333h2.99996m5-9V6.66667m0 4.33333 4 1.125V13m-4-2v3m2-6-6-4-5.99996 4m4.99996 1h2m-2 3h2m1 6 2 2 4-4" />
            </svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Transit Inventory</span>
        </a>
        <a href="{{ route('admin.outbound.delivery-orders.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.outbound.delivery-orders.*') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
            </svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Delivery Order</span>
        </a>
    </nav>
</div>
