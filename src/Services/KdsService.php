<?php

namespace Dev3bdulrahman\Kds\Services;

use Dev3bdulrahman\Kds\Models\KdsOrder;
use Dev3bdulrahman\Kds\Models\KdsOrderItem;
use Dev3bdulrahman\Kds\Models\KdsDisplay;
use Dev3bdulrahman\Kds\Events\KdsOrderCreated;
use Dev3bdulrahman\Kds\Events\KdsOrderReady;
use Dev3bdulrahman\Kds\Events\KdsOrderCompleted;
use Illuminate\Support\Facades\DB;

class KdsService
{
    public function createOrderFromPosSale($posSale): KdsOrder
    {
        return DB::transaction(function () use ($posSale) {
            $display = $this->resolveDisplayForPosSale($posSale);

            $order = KdsOrder::create([
                'company_id' => $posSale->company_id,
                'pos_sale_id' => $posSale->id,
                'display_id' => $display?->id,
                'order_number' => 'KDS-' . $posSale->id . '-' . time(),
                'table_number' => $posSale->table_number ?? null,
                'guest_count' => $posSale->guest_count ?? null,
                'status' => 'pending',
                'priority' => $posSale->priority ?? 'normal',
                'notes' => $posSale->notes ?? null,
            ]);

            $sortOrder = 0;
            foreach ($posSale->items as $item) {
                KdsOrderItem::create([
                    'kds_order_id' => $order->id,
                    'pos_sale_item_id' => $item->id,
                    'product_name' => $item->product_name,
                    'product_name_ar' => $item->product_name_ar ?? null,
                    'quantity' => $item->quantity,
                    'modifiers' => $item->modifiers ?? null,
                    'status' => 'pending',
                    'notes' => $item->notes ?? null,
                    'sort_order' => $sortOrder++,
                ]);
            }

            event(new KdsOrderCreated($order, auth()->id(), $posSale->company_id));

            return $order->load('items');
        });
    }

    public function updateItemStatus($itemId, $status): KdsOrderItem
    {
        $item = KdsOrderItem::findOrFail($itemId);
        $item->update(['status' => $status]);

        if ($status === 'preparing' && $item->preparation_time === 0) {
            $item->update(['preparation_time' => time()]);
        }

        $this->syncOrderStatusFromItems($item->kds_order_id);

        return $item->fresh();
    }

    public function updateOrderStatus($orderId, $status): KdsOrder
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

        if ($status === 'ready') {
            event(new KdsOrderReady($order, auth()->id(), $order->company_id));
        }

        if ($status === 'completed') {
            event(new KdsOrderCompleted($order, auth()->id(), $order->company_id));
        }

        return $order->load('items');
    }

    public function getActiveOrders($displayId)
    {
        return KdsOrder::with('items')
            ->where('display_id', $displayId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderByRaw("FIELD(priority, 'urgent', 'normal')")
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getOrdersByStatus($companyId, $status)
    {
        return KdsOrder::with('items', 'display')
            ->where('company_id', $companyId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsReady($orderId): KdsOrder
    {
        return $this->updateOrderStatus($orderId, 'ready');
    }

    public function markAsCompleted($orderId): KdsOrder
    {
        return $this->updateOrderStatus($orderId, 'completed');
    }

    private function resolveDisplayForPosSale($posSale): ?KdsDisplay
    {
        return KdsDisplay::where('company_id', $posSale->company_id)
            ->where('status', 'online')
            ->first();
    }

    private function syncOrderStatusFromItems($orderId): void
    {
        $order = KdsOrder::with('items')->find($orderId);
        if (!$order) return;

        $statuses = $order->items->pluck('status')->unique()->values()->toArray();

        if (count($statuses) === 1 && $statuses[0] === 'completed') {
            $order->update(['status' => 'completed', 'completed_at' => now()]);
            event(new KdsOrderCompleted($order, auth()->id(), $order->company_id));
        } elseif (in_array('ready', $statuses) && !in_array('pending', $statuses) && !in_array('preparing', $statuses)) {
            if ($order->status !== 'ready') {
                $order->update(['status' => 'ready']);
                event(new KdsOrderReady($order, auth()->id(), $order->company_id));
            }
        } elseif (in_array('preparing', $statuses)) {
            if ($order->status === 'pending') {
                $order->update(['status' => 'preparing', 'started_at' => $order->started_at ?? now()]);
            }
        }
    }
}
