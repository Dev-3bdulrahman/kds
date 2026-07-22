<?php

namespace Dev3bdulrahman\Kds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KdsOrder extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'kds_orders';

    protected $fillable = [
        'company_id',
        'pos_sale_id',
        'display_id',
        'order_number',
        'table_number',
        'guest_count',
        'status',
        'priority',
        'preparation_time',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'preparation_time' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function display(): BelongsTo
    {
        return $this->belongsTo(KdsDisplay::class, 'display_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KdsOrderItem::class, 'kds_order_id');
    }
}
