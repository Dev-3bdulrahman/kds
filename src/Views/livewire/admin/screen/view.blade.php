<div class="min-h-screen bg-gray-900 text-white p-4" wire:poll.10s>
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-700">
        <div>
            <h1 class="text-3xl font-bold">{{ $display->name }}</h1>
            <p class="text-gray-400 text-lg">{{ $display->location ?: ucfirst($display->display_type) }}</p>
        </div>
        <div class="text-right">
            <p class="text-gray-400 text-sm">{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6 h-[calc(100vh-140px)]">
        {{-- Pending Column --}}
        <div class="bg-gray-800/50 rounded-2xl p-4 flex flex-col overflow-hidden border-t-4 border-yellow-500">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-700">
                <h2 class="text-2xl font-bold text-yellow-400">{{ __('kds::kds.pending') }}</h2>
                <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-lg font-semibold">{{ $ordersByStatus['pending']->count() }}</span>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4">
                @forelse($ordersByStatus['pending'] as $order)
                    @include('kds::livewire.admin.screen._order-card', ['order' => $order, 'column' => 'pending'])
                @empty
                    <div class="text-center text-gray-500 mt-12">
                        <p class="text-xl">--</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Preparing Column --}}
        <div class="bg-gray-800/50 rounded-2xl p-4 flex flex-col overflow-hidden border-t-4 border-blue-500">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-700">
                <h2 class="text-2xl font-bold text-blue-400">{{ __('kds::kds.preparing') }}</h2>
                <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-lg font-semibold">{{ $ordersByStatus['preparing']->count() }}</span>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4">
                @forelse($ordersByStatus['preparing'] as $order)
                    @include('kds::livewire.admin.screen._order-card', ['order' => $order, 'column' => 'preparing'])
                @empty
                    <div class="text-center text-gray-500 mt-12">
                        <p class="text-xl">--</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Ready Column --}}
        <div class="bg-gray-800/50 rounded-2xl p-4 flex flex-col overflow-hidden border-t-4 border-green-500">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-700">
                <h2 class="text-2xl font-bold text-green-400">{{ __('kds::kds.ready') }}</h2>
                <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-lg font-semibold">{{ $ordersByStatus['ready']->count() }}</span>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4">
                @forelse($ordersByStatus['ready'] as $order)
                    @include('kds::livewire.admin.screen._order-card', ['order' => $order, 'column' => 'ready'])
                @empty
                    <div class="text-center text-gray-500 mt-12">
                        <p class="text-xl">--</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
