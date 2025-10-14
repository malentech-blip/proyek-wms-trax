<x-app-layout>
    <x-slot name="header">
        Putaway & Cetak Label: {{ $goodsReceipt->receipt_number }}
    </x-slot>

    <div>
        @livewire('admin.inbound.putaway-form', ['goodsReceipt' => $goodsReceipt])
    </div>
</x-app-layout>