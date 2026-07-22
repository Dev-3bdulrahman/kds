<?php

namespace Dev3bdulrahman\Kds\Policies;

use App\Models\User;
use Dev3bdulrahman\Kds\Models\KdsOrder;

class KdsOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('kds.orders.view');
    }

    public function view(User $user, KdsOrder $order): bool
    {
        return $user->can('kds.orders.view') && $order->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('kds.orders.create');
    }

    public function update(User $user, KdsOrder $order): bool
    {
        return $user->can('kds.orders.update') && $order->company_id === $user->company_id;
    }

    public function delete(User $user, KdsOrder $order): bool
    {
        return $user->can('kds.orders.delete') && $order->company_id === $user->company_id;
    }

    public function updateStatus(User $user, KdsOrder $order): bool
    {
        return $user->can('kds.orders.update-status') && $order->company_id === $user->company_id;
    }
}
