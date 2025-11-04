<div class="w-full">
    @php
        // Ganti nama route di bawah ini sesuai dengan nama route Anda yang sebenarnya
        $currentRoute = Route::currentRouteName();
        $isMRActive = Str::contains($currentRoute, 'material-request');
        $isPLActive = Str::contains($currentRoute, 'picking-list');
        $isWIPActive = Str::contains($currentRoute, 'wip');
        $isFinishedGoods = Str::contains($currentRoute, 'finished-goods');
        $isRejectsProduction = Str::contains($currentRoute, 'rejects-production');

        $isHaveFinishedGoods = $wip && $wip->status === 'Completed' && $wip->produced_qty > 0;
        $isHaveRejectsProduction = $wip && $wip->status === 'Completed' && $wip->rejected_qty > 0;

        if (!$isMRActive && !$isPLActive && !$isWIPActive && !$isFinishedGoods && !$isRejectsProduction) {
            $isMRActive = true; 
        }
    @endphp

    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">

            <a href="{{ route('admin.production.material-request.detail', $mrId) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isMRActive ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $isMRActive ? 'page' : 'false' }}">
                Material Request
            </a>

            <a href="{{ route('admin.production.picking-list.detail', $mrId) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isPLActive
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $isPLActive ? 'page' : 'false' }}">
                Picking List
            </a>

            <a href="{{ route('admin.production.wip.detail', $mrId) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isWIPActive
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $isWIPActive ? 'page' : 'false' }}">
                Work In Progress
            </a>
            @if ($isHaveFinishedGoods)
                <a href="{{ route('admin.production.finished-goods.detail', $mrId) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isFinishedGoods
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    aria-current="{{ $isFinishedGoods ? 'page' : 'false' }}">
                    Finished Goods
                </a>
            @endif
            @if ($isHaveRejectsProduction)
                <a href="{{ route('admin.production.rejects-production.detail', $mrId) }}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out 
                {{ $isRejectsProduction
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    aria-current="{{ $isRejectsProduction ? 'page' : 'false' }}">
                    Rejects Production
                </a>
            @endif
        </nav>
    </div>
</div>
