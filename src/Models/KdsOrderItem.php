<?php

namespace Dev3bdulrahman\Kds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KdsOrderItem extends Model
{
    protected $table = 'kds_order_items';

    protected $fillable = [
        'kds_order_id',
        'pos_sale_item_id',
        'product_name',
        'product_name_ar',
        'quantity',
        'modifiers',
        'status',
        'preparation_time',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'modifiers' => 'array',
        'preparation_time' => 'integer',
        'sort_order' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(KdsOrder::class, 'kds_order_id');
    }
}
