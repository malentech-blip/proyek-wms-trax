<x-app-layout>
    <x-slot name="header">
        List Material Request (Production)
    </x-slot>

    @php
        $statuses = [
          "Requested",
          "Picked",
          "Delivered to WIP"
        ];
    @endphp

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Material Request</h3>
            
            <form method="GET">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="request_date" class="text-sm font-medium text-gray-700">Tanggal Request</label>
                        <input type="date" name="request_date" id="request_date" onblur="this.form.submit()" value="{{ request('request_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" value="{{ request('status') }}" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                          <option value="">Pilih Status</option>
                          @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request("status") == $status ? "selected" : "" }}>{{ $status }}</option>  
                          @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Cari No. Material Request</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="flex-1 block w-full border-gray-300 rounded-none rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Cari nomor material request...">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-md hover:bg-gray-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[100px]">MR. No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[150px]">Item Request</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[150px]">Requested By</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[150px]">Request Date</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[200px]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($materialRequests as $mr)
                    <tr class="cursor-pointer" onclick="window.location='{{ route('admin.production.material-request.detail', $mr->id) }}'">
                        <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                        <td class="p-4 text-gray-700 font-medium">{{ $mr->mr_no }}</td>
                        <td class="p-4 text-gray-500">
                          @foreach ($mr->pickingList as $item)
                              <p class="text-gray-700 text-sm mb-1">{{ $item->item->item_name }}, {{ $item->quantity }} qty</p>
                          @endforeach
                        </td>
                        <td class="p-4 text-gray-500">{{ $mr->requested_by }}</td>
                        <td class="p-4 text-gray-500">
                          {{ \Carbon\Carbon::createFromFormat('Y-m-d', $mr["request_date"])->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-gray-500">
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">{{ $mr->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-12 text-gray-500">
                            Tidak ada data Material Request ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>