<x-app-layout>
    <x-slot name="header">
        {{-- DIUBAH: Dibuat responsif, item akan bertumpuk di layar kecil --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center w-full gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Logs') }}
            </h2>
            <div class="flex items-center space-x-2 sm:space-x-4 justify-end">
                {{-- DIUBAHKAN: Dibuat lebih kecil dan disembunyikan pada layar sangat kecil untuk menghemat ruang --}}
                <div class="relative hidden sm:block">
                    <button class="flex items-center space-x-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors duration-200">
                        <span>{{ Auth::user()->name }}</span>
                    </button>
                </div>
                <button class="relative text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.405L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">1</span>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center">
                                <span class="text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                        <x-dropdown-link href="{{ route('profile.edit') }}">{{ __('Profile') }}</x-dropdown-link>
                        <div class="border-t border-gray-200"></div>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </x-slot>
    
    <div 
        class="py-8" 
        x-data="{ 
            selectedIds: [], 
            checkAll: false,
            showFilterModal: false,
            pageLoaded: false, // DITAMBAHKAN: State untuk animasi saat halaman dimuat
            toggleAllCheckboxes() {
                this.checkAll = !this.checkAll;
                this.selectedIds = [];
                if (this.checkAll) {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = true;
                        this.selectedIds.push(parseInt(checkbox.value));
                    });
                } else {
                     document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            },
            updateSelected(id) {
                id = parseInt(id);
                if (this.selectedIds.includes(id)) {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                } else {
                    this.selectedIds.push(id);
                }
            }
        }"
        x-init="setTimeout(() => pageLoaded = true, 200)" {{-- DITAMBAHKAN: Memicu animasi setelah halaman siap --}}
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><p>{{ session('success') }}</p></div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><p>{{ session('error') }}</p></div>
            @endif

            {{-- DITAMBAHKAN: Transisi untuk animasi fade-in --}}
            <div 
                class="bg-white shadow-sm rounded-lg p-6 transition-all duration-500 ease-out"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-lg">
                             <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                    {{-- Dropdown Filter Waktu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-1 text-sm text-gray-500 hover:text-gray-800 transition-colors duration-200">
                            <span>
                                @if(request('time_filter') == 'this_week') This Week
                                @elseif(request('time_filter') == 'this_month') This Month
                                @elseif(request('time_filter') == 'this_year') This Year
                                @else This Week
                                @endif
                            </span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        {{-- DITAMBAHKAN: Transisi untuk dropdown --}}
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg z-10" style="display:none;">
                            <a href="{{ route('logs.index', array_merge(request()->query(), ['time_filter' => 'this_week', 'page' => 1])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">This Week</a>
                            <a href="{{ route('logs.index', array_merge(request()->query(), ['time_filter' => 'this_month', 'page' => 1])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">This Month</a>
                            <a href="{{ route('logs.index', array_merge(request()->query(), ['time_filter' => 'this_year', 'page' => 1])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">This Year</a>
                        </div>
                    </div>
                </div>
                {{-- DIUBAH: Grid dibuat responsif --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Penawaran</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalQuotations ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Penawaran Diproses</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $processedQuotations ?? 0 }}</p>
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Penawaran Terkirim</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $submittedQuotations ?? 0 }}</p>
                        </div>
                        @if(isset($percentageChange))
                            <span class="text-sm font-semibold
                                @if($percentageChange > 0) text-green-500
                                @elseif($percentageChange < 0) text-red-500
                                @else text-gray-500 @endif
                            ">
                                @if($percentageChange > 0)+@endif
                                {{ number_format($percentageChange, 2) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- DITAMBAHKAN: Transisi untuk animasi fade-in dengan sedikit delay --}}
            <div 
                class="bg-white shadow-sm rounded-lg p-6 transition-all duration-500 ease-out"
                style="transition-delay: 150ms"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <div class="flex justify-between items-center pb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Log Aktivitas Terbaru</h3>
                </div>
                <form action="{{ route('logs.index') }}" method="GET">
                    {{-- DIUBAH: Kontainer filter dibuat responsif, bertumpuk di mobile --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="w-full md:w-1/3 max-w-sm">
                            <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 w-full">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="flex-grow border-none focus:ring-0 p-0 text-sm text-gray-700" />
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" class="flex items-center space-x-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors duration-200">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6"></path></svg>
                                    <span>Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-10" style="display:none;">
                                    <a href="{{ route('logs.index', array_merge(request()->query(), ['status' => ''])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All</a>
                                    <a href="{{ route('logs.index', array_merge(request()->query(), ['status' => 'draft'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Draft</a>
                                    <a href="{{ route('logs.index', array_merge(request()->query(), ['status' => 'processed'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Processed</a>
                                    <a href="{{ route('logs.index', array_merge(request()->query(), ['status' => 'submitted'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Submitted</a>
                                </div>
                            </div>
                           <button type="button" @click="showFilterModal = true" class="flex items-center space-x-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors duration-200">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                <span>Filter</span>
                            </button>
                            <div class="relative" x-data="{ open: false }">
                                <div x-data="{ tooltip: 'Pilih item terlebih dahulu' }" x-tooltip.top="selectedIds.length === 0 ? tooltip : ''">
                                    <button 
                                        type="button" 
                                        @click="open = !open" 
                                        :disabled="selectedIds.length === 0" 
                                        {{-- DITAMBAHKAN: Transisi untuk opacity --}}
                                        class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm hover:bg-red-700 focus:outline-none flex items-center disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                    >
                                        Bulk Action
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-10" style="display:none;">
                                    <a href="#" @click.prevent="document.getElementById('bulk-process-form').submit()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proses ke Produksi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <form id="bulk-process-form" action="{{ route('logs.bulk.process') }}" method="POST" class="hidden">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="selected_ids[]" :value="id">
                    </template>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm align-middle">
                        <thead>
                             <tr class="text-gray-500">
                                <th class="p-3 text-left w-4">
                                    <input type="checkbox" @click="toggleAllCheckboxes()" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                </th>
                                <th class="p-3 text-left text-xs font-medium uppercase tracking-wider">ID Penawaran</th>
                                <th class="p-3 text-left text-xs font-medium uppercase tracking-wider">Customer</th>
                                <th class="p-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                <th class="p-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Dibuat</th>
                                <th class="p-3 text-right text-xs font-medium uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($activities as $activity)
                                {{-- DITAMBAHKAN: Efek hover pada baris tabel --}}
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="p-3">
                                        <input type="checkbox" class="item-checkbox rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" :value="{{ $activity->id }}" @change="updateSelected({{ $activity->id }})">
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-gray-800 font-medium">{{ $activity->id }}</td>
                                    <td class="p-3 whitespace-nowrap text-gray-600">{{ $activity->accurate_customer_id }}</td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($activity->status == 'draft') bg-yellow-100 text-yellow-800 
                                            @elseif($activity->status == 'in-progress') bg-blue-100 text-blue-800
                                            @elseif($activity->status == 'pending') bg-orange-100 text-orange-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-gray-600">{{ $activity->created_at->format('d M Y') }}</td>
                                    <td class="p-3 whitespace-nowrap text-right font-medium">
                                       <a href="{{ route('quotations.pdf', $activity) }}" target="_blank" class="inline-flex items-center bg-red-600 text-white rounded-lg px-3 py-1 text-xs font-semibold hover:bg-red-700 transition duration-200">
                                            Download PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-16 text-gray-500">Tidak ada data untuk ditampilkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(isset($activities) && $activities->hasPages())
                {{-- DIUBAH: Kontainer paginasi dibuat responsif --}}
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600 gap-4">
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('logs.index') }}" method="GET">
                            @foreach(request()->except('per_page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                <option value="10" @if(request('per_page', 10) == 10) selected @endif>10</option>
                                <option value="25" @if(request('per_page') == 25) selected @endif>25</option>
                                <option value="50" @if(request('per_page') == 50) selected @endif>50</option>
                                <option value="100" @if(request('per_page') == 100) selected @endif>100</option>
                            </select>
                        </form>
                        <span>items per page</span>
                        <span class="ml-4 hidden md:inline">
                            {{ $activities->firstItem() }}-{{ $activities->lastItem() }} of {{ $activities->total() }} items
                        </span>
                    </div>
                    <div>
                        {{ $activities->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- DIUBAH: Modal sekarang memiliki transisi yang smooth --}}
        <div 
            x-show="showFilterModal" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" 
            style="display: none;"
            x-cloak
        >
            <div @click="showFilterModal = false" class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div 
                x-show="showFilterModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-lg"
            >
                <h3 class="text-lg font-semibold text-gray-800">Filter Lanjutan</h3>
                <form action="{{ route('logs.index') }}" method="GET" class="mt-4 space-y-4">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="showFilterModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-50 transition-colors duration-200">Batal</button>
                        <a href="{{ route('logs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border rounded-lg hover:bg-gray-200 transition-colors duration-200">Reset Filter</a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-200">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>