<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Edit Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Memuat komponen ItemBuilder dengan data item yang akan diedit --}}
            @livewire('item-builder', ['item' => $item])
        </div>
    </div>
</x-app-layout>