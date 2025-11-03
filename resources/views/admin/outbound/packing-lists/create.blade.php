<x-app-layout>
    <x-slot name="header">
        Create Packing List
    </x-slot>

    <livewire:admin.outbound.packing-form :so-id="$salesOrder['id'] ?? (int) request()->get('so_id')" />
</x-app-layout>


