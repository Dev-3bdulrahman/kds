<div class="bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-700 hover:border-gray-600 transition-colors {{ $order->priority === 'urgent' ? 'border-l-4 border-l-red-500' : '' }}">
    <div class="flex items-start justify-between mb-3">
        <div>
            <h3 class="text-2xl font-bold">{{ $order->order_number }}</h3>
            @if($order->table_number)
                <p class="text-gray-400 text-lg">{{ __('kds::kds.table_number') }}: {{ $order->table_number }}</p>
            @endif
        </div>
        <div class="text-right">
            @if($order->guest_count)
                <p class="text-gray-400 text-sm">{{ $order->guest_count }} {{ __('kds::kds.guest_count') }}</p>
            @endif
            @if($order->started_at)
                <p class="text-gray-400 text-sm">
                    {{ $this->getOrderWaitTime($order) }}
                </p>
            @endif
        </div>
    </div>

    @if($order->priority === 'urgent')
        <div class="mb-2">
            <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-sm font-bold">{{ __('kds::kds.urgent') }}</span>
        </div>
    @endif

    <div class="space-y-2 mb-4">
        @foreach($order->items as $item)
            <div class="flex items-center justify-between bg-gray-750 rounded-lg p-3 {{ $item->status === 'completed' ? 'opacity-50 line-through' : '' }}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-semibold">{{ $item->quantity }}</span>
                        <span class="text-xl">x</span>
                        <span class="text-xl font-medium">{{ $item->product_name }}</span>
                    </div>
                    @if($item->modifiers && count($item->modifiers) > 0)
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($item->modifiers as $modifier)
                                <span class="text-sm text-gray-400 bg-gray-700 px-2 py-0.5 rounded">{{ $modifier }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($item->notes)
                        <p class="text-sm text-gray-400 mt-1">{{ $item->notes }}</p>
                    @endif
                </div>
                <div class="text-right text-sm text-gray-400 ml-2">
                    {{ $this->getWaitTimeProperty($item) }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex gap-2">
        @if($column === 'pending')
            @foreach($order->items as $item)
                @if($item->status === 'pending')
                    <button wire:click="updateItemStatus({{ $item->id }}, 'preparing')" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-lg font-bold transition-colors active:scale-95">
                        {{ __('kds::kds.mark_preparing') }}
                    </button>
                @endif
            @endforeach
            <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-lg font-bold transition-colors active:scale-95">
                {{ __('kds::kds.mark_preparing') }} ({{ __('kds::kds.all') }})
            </button>
        @endif

        @if($column === 'preparing')
            @foreach($order->items as $item)
                @if($item->status !== 'completed')
                    <button wire:click="updateItemStatus({{ $item->id }}, 'ready')" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-lg font-bold transition-colors active:scale-95">
                        {{ $item->product_name }} - {{ __('kds::kds.mark_ready') }}
                    </button>
                @endif
            @endforeach
            <button wire:click="updateOrderStatus({{ $order->id }}, 'ready')" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-lg font-bold transition-colors active:scale-95">
                {{ __('kds::kds.mark_ready') }} ({{ __('kds::kds.all') }})
            </button>
        @endif

        @if($column === 'ready')
            <button wire:click="updateOrderStatus({{ $order->id }}, 'completed')" class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl text-lg font-bold transition-colors active:scale-95">
                {{ __('kds::kds.mark_completed') }}
            </button>
        @endif
    </div>
</div>
