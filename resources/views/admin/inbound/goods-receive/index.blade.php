<x-app-layout>
    <x-slot name="header">
        Penerimaan Barang (Goods Receive)
    </x-slot>

    <div>
        @livewire('admin.inbound.goods-receive-form', ['poId' => $poId])
    </div>
</x-app-layout>