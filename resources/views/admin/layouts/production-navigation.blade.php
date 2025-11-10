<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="flex flex-col h-full text-gray-800 bg-white">
    <div class="h-[64px] border-b flex items-center flex-shrink-0" :class="sidebarOpen ? 'px-4' : 'justify-center'">
        <a href="{{ route('admin.production.dashboard') }}" class="flex items-center space-x-3">
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

        <a href="{{ route('admin.production.dashboard') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.production.dashboard') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>
        <div x-data="{ open: {{ request()->routeIs('admin.production.material-request.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full {{ $linkClasses }} {{ request()->routeIs('admin.production.material-request.*') ? 'bg-gray-100 text-gray-600' : $inactiveClasses }}"
                :class="!sidebarOpen && 'justify-center'">
                <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z" />
                </svg>

                <div class="ml-3 flex-1 text-left whitespace-nowrap" x-show="sidebarOpen" x-transition><span>Material
                        Request</span></div>
                <svg x-show="sidebarOpen" class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.production.material-request.index') }}"
                        class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.material-request.index') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">Create
                        Material Request</a>
                </div>
                <div class="py-2 pl-12 pr-3 space-y-1">
                    <a href="{{ route('admin.production.material-request.mr') }}"
                        class="block p-2 rounded-md text-sm {{ request()->routeIs('admin.production.material-request.mr') ? 'font-semibold text-blue-600 bg-blue-50' : 'text-gray-500 hover:bg-gray-100' }}">List
                        Material Request</a>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.production.picking-list.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.production.picking-list.index') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Picking List</span>
        </a>
        <a href="{{ route('admin.production.wip.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.production.wip.index') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Work In Progress</span>
        </a>
        <a href="{{ route('admin.production.finished-goods.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.production.finished-goods.index') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Finished Goods</span>
        </a>
        <a href="{{ route('admin.production.rejects-production.index') }}"
            class="{{ $linkClasses }} {{ request()->routeIs('admin.production.rejects-production.index') ? $activeClasses : $inactiveClasses }}"
            :class="!sidebarOpen && 'justify-center'">
            <svg class="w-7 h-7 {{ $iconClasses }}" xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
            </svg>

            <span class="ml-3 whitespace-nowrap" x-show="sidebarOpen" x-transition>Rejects Production</span>
        </a>
    </nav>
</div>
