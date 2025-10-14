<x-app-layout>
    <x-slot name="header">
        Quality Check untuk Penerimaan: {{ $goodsReceipt->receipt_number }}
    </x-slot>

    <div class="py-6">
        {{-- Baris ini akan memuat dan merender komponen Livewire interaktif Anda --}}
        @livewire('admin.inbound.quality-check-form', ['goodsReceipt' => $goodsReceipt])
    </div>
</x-app-layout>