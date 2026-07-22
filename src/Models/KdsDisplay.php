<?php

namespace Dev3bdulrahman\Kds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KdsDisplay extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'kds_displays';

    protected $fillable = [
        'company_id',
        'name',
        'name_ar',
        'display_type',
        'location',
        'status',
        'last_heartbeat',
        'display_categories',
    ];

    protected $casts = [
        'last_heartbeat' => 'datetime',
        'display_categories' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(KdsOrder::class, 'display_id');
    }
}
