<?php

namespace Dev3bdulrahman\Kds\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Kds\Services\KdsService;
use Dev3bdulrahman\Kds\Models\KdsDisplay;
use Dev3bdulrahman\Kds\Models\KdsOrder;
use Dev3bdulrahman\Kds\Models\KdsOrderItem;
use Illuminate\Http\JsonResponse;

class KdsApiController extends Controller
{
    use HasApiResponse;

    public function getActiveOrders(KdsDisplay $display, KdsService $service): JsonResponse
    {
        $this->authorize('view', $display);

        $orders = $service->getActiveOrders($display->id);

        return $this->success(
            $orders,
            __('kds::kds.active_orders_retrieved')
        );
    }

    public function updateItemStatus(KdsOrderItem $item, KdsService $service): JsonResponse
    {
        $status = request()->validate(['status' => 'required|in:pending,preparing,ready,completed'])['status'];

        $item = $service->updateItemStatus($item->id, $status);

        return $this->success(
            $item,
            __('kds::kds.item_status_updated')
        );
    }

    public function updateOrderStatus(KdsOrder $order, KdsService $service): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $status = request()->validate(['status' => 'required|in:pending,preparing,ready,completed,cancelled'])['status'];

        $order = $service->updateOrderStatus($order->id, $status);

        return $this->success(
            $order,
            __('kds::kds.order_status_updated')
        );
    }
}
