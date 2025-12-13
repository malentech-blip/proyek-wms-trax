<div class="flex flex-col h-full text-gray-800 bg-white">

    <div class="h-[64px] border-b flex items-center flex-shrink-0" :class="sidebarOpen ? 'px-4' : 'justify-center'">
        <a href="{{ route('super-admin.dashboard') }}" class="flex items-center space-x-3">
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

        <a href="{{ route('super-admin.dashboard') }}" class="{{ $linkClasses }} {{ request()->routeIs('super-admin.dashboard') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
            <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        {{-- ============================================= --}}
        {{-- ||     BLOK MENU ADMIN OPERASIONAL (BARU)    || --}}
        {{-- ============================================= --}}

        @can('manage_inbound')
        <div x-data="{ open: {{ request()->routeIs('admin.inbound.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.inbound.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}"
                    :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Inbound</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.inbound.dashboard') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.dashboard') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Dashboard</a>
                    <a href="{{ route('admin.inbound.purchase-orders.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.purchase-orders.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Purchase Orders</a>
                    <a href="{{ route('admin.inbound.quality-check.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.quality-check.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Quality Check</a>
                    <a href="{{ route('admin.inbound.putaway.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.putaway.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Putaway</a>
                    <a href="{{ route('admin.inbound.reject-warehouse.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.reject-warehouse.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Reject Warehouse</a>
                    <a href="{{ route('admin.inbound.history.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inbound.history.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">History</a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage_inventory')
        <div x-data="{ open: {{ request()->routeIs('admin.inventory.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.inventory.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}"
                    :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Inventory</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.inventory.dashboard') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.dashboard') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Dashboard</a>
                    <a href="{{ route('admin.inventory.raw-materials') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.raw-materials') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Raw Materials</a>
                    <a href="{{ route('admin.inventory.move-stock') }}"
                        class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.move-stock') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Move
                        Stock</a>
                    <a href="{{ route('admin.inventory.reject-warehouses') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.reject-warehouses') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Reject Warehouses</a>
                    <a href="{{ route('admin.inventory.stock-reports') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.stock-reports') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Stock Reports</a>
                    <a href="{{ route('admin.inventory.stock-adjustment') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.inventory.stock-adjustment') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Stock Adjustment</a>

                </div>
            </div>
        </div>
        @endcan

        @can('manage_production')
        <div x-data="{ open: {{ request()->routeIs('admin.production.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.production.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}"
                    :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H21"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Production</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.production.dashboard') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.dashboard') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Dashboard</a>
                    <a href="{{ route('admin.production.material-request.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.material-request.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Material Request</a>
                    <a href="{{ route('admin.production.picking-list.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.picking-list.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Picking List</a>
                    <a href="{{ route('admin.production.wip.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.wip.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Work in Progress</a>
                    <a href="{{ route('admin.production.finished-goods.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.finished-goods.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Finished Goods</a>
                    <a href="{{ route('admin.production.rejects-production.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.rejects-production.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Rejects Production</a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage_outbound')
        <div x-data="{ open: {{ request()->routeIs('admin.outbound.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.outbound.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}"
                    :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Outbound</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.outbound.dashboard') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.outbound.dashboard') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Dashboard</a>
                    <a href="{{ route('admin.outbound.sales-orders.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.outbound.sales-orders.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Sales Orders</a>
                    <a href="{{ route('admin.outbound.packing-lists.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.outbound.packing-lists.*') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Packing Lists</a>
                </div>
            </div>
        </div>
        @endcan

        {{-- Pemisah --}}
        <hr class="my-4 mx-3">

        {{-- ============================================= --}}
        {{-- ||         BLOK MENU SUPER ADMIN           || --}}
        {{-- ============================================= --}}

        @can('manage_master_data')
        <div x-data="{ open: {{ request()->routeIs('super-admin.master-data.*') ? 'true' : 'false' }} }">
<button @click="open = !open"
                class="w-full {{ $linkClasses }} {{ request()->routeIs('super-admin.master-data.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}"
                    :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10m16-5H4m16 5v-2a3 3 0 00-3-3H7a3 3 0 00-3 3v2m16 0H4m16 0a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v7a3 3 0 003 3z"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Master Data</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('super-admin.master-data.items.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.master-data.items.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Item Master</a>
                    <a href="{{ route('super-admin.master-data.suppliers.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.master-data.suppliers.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Supplier</a>
                    <a href="{{ route('super-admin.master-data.customers.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.master-data.customers.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Customer</a>
                    <a href="{{ route('super-admin.master-data.locations.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.master-data.locations.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Lokasi/Rak/Pallet</a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage_users')
        <div x-data="{ open: {{ request()->routeIs('super-admin.user-management.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full {{ $linkClasses }} {{ request()->routeIs('super-admin.user-management.*') ? 'bg-gray-100 text-gray-900' : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21V5a2 2 0 00-2-2H9a2 2 0 00-2 2v16"></path></svg>
                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>User Management</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('super-admin.user-management.users.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.user-management.users.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Users</a>
                    <a href="{{ route('super-admin.user-management.roles.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.user-management.roles.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Roles</a>
                    <a href="{{ route('super-admin.user-management.permissions.index') }}" class="block p-2 rounded-md text-sm {{ request()->routeIs('super-admin.user-management.permissions.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Permission</a>
                </div>
            </div>
        </div>
        @endcan

        @can('manage_integration')
            <a href="{{ route('settings.accurate') }}" class="{{ $linkClasses }} {{ request()->routeIs('settings.accurate') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Accurate Integration</span>
            </a>
        @endcan

        @can('manage_templates')
            <a href="{{ route('super-admin.label-templates.index') }}" class="{{ $linkClasses }} ... {{ request()->routeIs('label-templates.index') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Label Template</span>
            </a>
        @endcan

        @can('view_reports')
            <a href="{{ route('super-admin.reports-center.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('reports-center.index') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Reports Center</span>
            </a>
        @endcan

        @can('view_system_logs')
            <a href="{{ route('super-admin.system-logs.index') }}" class="{{ $linkClasses }} {{ request()->routeIs('system-logs.index') ? $activeClasses : $inactiveClasses }}" :class="!sidebarOpen && 'justify-center'">
                <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>System Logs</span>
            </a>
        @endcan
    </nav>
</div>
