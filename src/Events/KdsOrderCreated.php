<?php

namespace Dev3bdulrahman\Kds\Events;

use Dev3bdulrahman\Kds\Models\KdsOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KdsOrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public KdsOrder $kdsOrder,
        public int $userId,
        public int $companyId,
    ) {}
}
