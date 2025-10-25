<x-app-layout>
    <x-slot name="header">
        Reject Warehouses
    </x-slot>

    <div class="space-y-6" @user-saved.window="location.reload()">
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Reject Warehouses</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel Produk Yang Ditolak.</p>
            </div>
        </div>

<form method="GET" class="flex justify-between items-center end-0">
            <div class="flex items-center space-x-4">
<select name="status" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
    <option value="all" {{ request('status')=='all' ? 'selected' : '' }}>Filter: Status</option>
    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
    <option value="rework" {{ request('status')=='rework' ? 'selected' : '' }}>Rework</option>
    <option value="scrap" {{ request('status')=='scrap' ? 'selected' : '' }}>Scrap</option>
                </select>
<input type="text" name="search" placeholder="Cari Nama Produk" value="{{ request('search') }}"
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
    Filter
</button>
            </div>
</form>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-600">
                        <tr>
                            <th class="p-4 text-left font-semibold text-white">Kode Item</th>
                            <th class="p-4 text-left font-semibold text-white">Nama Item</th>
                            <th class="p-4 text-left font-semibold text-white">Qty</th>
                            <th class="p-4 text-left font-semibold text-white">Alasan</th>
                            <th class="p-4 text-left font-semibold text-white">Asal</th>
                            <th class="p-4 text-left font-semibold text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
@forelse ($rejectProductions as $reject)
                        <tr>
<td class="p-4 text-gray-700 font-medium">{{ $reject->item_code ?? 'N/A' }}</td>
<td class="p-4 text-gray-500">{{ $reject->item_name ?? 'N/A' }}</td>
<td class="p-4 text-gray-500">{{ $reject->quantity ?? 'N/A' }}</td>
<td class="p-4 text-gray-500">{{ $reject->reason ?? 'N/A' }}</td>
<td class="p-4 text-gray-500">{{ $reject->source ?? 'N/A' }}</td>
                            <td class="p-4">
@if($reject->status === 'pending')
<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
@elseif($reject->status === 'rework')
<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Rework</span>
@elseif($reject->status === 'scrap')
<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Scrap</span>
@endif
</td>
                            <td class="p-4">
@if($reject->status === 'pending')
<div class="flex space-x-2">
    <form method="POST" action="{{ route('admin.inventory.reject-warehouses.rework', $reject) }}" class="inline">
        @csrf
        <button type="submit" class="text-blue-600 hover:underline text-sm"
            onclick="return confirm('Apakah Anda yakin ingin mengubah status menjadi rework?')">
            Rework
        </button>
    </form>
    <form method="POST" action="{{ route('admin.inventory.reject-warehouses.scrap', $reject) }}" class="inline">
        @csrf
        <button type="submit" class="text-red-600 hover:underline text-sm"
            onclick="return confirm('Apakah Anda yakin ingin mengubah status menjadi scrap?')">
            Scrap
        </button>
    </form>
</div>
@else
<span class="text-gray-400 text-sm">Tidak ada aksi</span>
@endif
                            </td>
                        </tr>
                        @empty
                        <tr>
<td colspan="7" class="text-center p-12 text-gray-500">
    Tidak ada data reject production ditemukan.
                            </td>
                        </tr>
@endforelse
                    </tbody>
                </table>
            </div>
@if($rejectProductions->hasPages())
            <div class="p-4 border-t">
{{ $rejectProductions->links() }}
            </div>
@endif
        </div>
    </div>
</x-app-layout>
