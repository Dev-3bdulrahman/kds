<?php

namespace Dev3bdulrahman\Kds\Policies;

use App\Models\User;
use Dev3bdulrahman\Kds\Models\KdsDisplay;

class KdsDisplayPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('kds.displays.view');
    }

    public function view(User $user, KdsDisplay $display): bool
    {
        return $user->can('kds.displays.view') && $display->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('kds.displays.create');
    }

    public function update(User $user, KdsDisplay $display): bool
    {
        return $user->can('kds.displays.update') && $display->company_id === $user->company_id;
    }

    public function delete(User $user, KdsDisplay $display): bool
    {
        return $user->can('kds.displays.delete') && $display->company_id === $user->company_id;
    }
}
