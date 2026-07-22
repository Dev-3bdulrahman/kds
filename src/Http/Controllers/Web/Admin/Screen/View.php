<?php

namespace Dev3bdulrahman\Kds\Http\Controllers\Web\Admin\Screen;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Dev3bdulrahman\Kds\Models\KdsDisplay;
use Dev3bdulrahman\Kds\Models\KdsOrder;
use Dev3bdulrahman\Kds\Models\KdsOrderItem;

class View extends Component
{
    public KdsDisplay $display;
    public array $ordersByStatus = [];

    protected $listeners = ['refreshScreen' => '$refresh'];

    public function mount(KdsDisplay $display)
    {
        $this->display = $display;
    }

    public function updateItemStatus($itemId, $status)
    {
        $item = KdsOrderItem::findOrFail($itemId);
        $item->update(['status' => $status]);

        if ($status === 'preparing' && $item->preparation_time === 0) {
            $item->update(['preparation_time' => time()]);
        }

        $this->syncOrderStatus($item->kds_order_id);
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = KdsOrder::findOrFail($orderId);
        $data = ['status' => $status];

        if ($status === 'preparing' && !$order->started_at) {
            $data['started_at'] = now();
        }

        if ($status === 'completed') {
            $data['completed_at'] = now();
        }

        $order->update($data);
        $order->items()->update(['status' => $status]);
    }

    public function getWaitTimeProperty($item): string
    {
        if (!$item->preparation_time || $item->preparation_time === 0) {
            return '--';
        }

        $elapsed = time() - $item->preparation_time;
        $mins = floor($elapsed / 60);
        $secs = $elapsed % 60;

        if ($mins > 0) {
            return $mins . ' ' . __('kds::kds.minutes') . ' ' . $secs . ' ' . __('kds::kds.seconds');
        }

        return $secs . ' ' . __('kds::kds.seconds');
    }

    public function getOrderWaitTime($order): string
    {
        if (!$order->started_at) {
            return '--';
        }

        $elapsed = $order->started_at->diffInSeconds(now());
        $mins = floor($elapsed / 60);
        $secs = $elapsed % 60;

        if ($mins > 0) {
            return $mins . ' ' . __('kds::kds.minutes') . ' ' . $secs . ' ' . __('kds::kds.seconds');
        }

        return $secs . ' ' . __('kds::kds.seconds');
    }

    private function syncOrderStatus($orderId): void
    {
        $order = KdsOrder::with('items')->find($orderId);
        if (!$order) return;

        $statuses = $order->items->pluck('status')->unique()->values()->toArray();

        if (count($statuses) === 1 && $statuses[0] === 'completed') {
            $order->update(['status' => 'completed', 'completed_at' => now()]);
        } elseif (in_array('ready', $statuses) && !in_array('pending', $statuses) && !in_array('preparing', $statuses)) {
            if ($order->status !== 'ready') {
                $order->update(['status' => 'ready']);
            }
        } elseif (in_array('preparing', $statuses)) {
            if ($order->status === 'pending') {
                $order->update(['status' => 'preparing', 'started_at' => $order->started_at ?? now()]);
            }
        }
    }

    #[Layout('layouts.kds')]
    public function render()
    {
        $orders = KdsOrder::with('items')
            ->where('display_id', $this->display->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderByRaw("FIELD(priority, 'urgent', 'normal')")
            ->orderBy('created_at', 'asc')
            ->get();

        $this->ordersByStatus = [
            'pending' => $orders->where('status', 'pending'),
            'preparing' => $orders->where('status', 'preparing'),
            'ready' => $orders->where('status', 'ready'),
        ];

        return view('kds::livewire.admin.screen.view', [
            'ordersByStatus' => $this->ordersByStatus,
        ])->title(__('kds::kds.kds_screen') . ' - ' . $this->display->name);
    }
}
