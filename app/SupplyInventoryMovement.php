<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyInventoryMovement extends Model
{
    protected $table = 'supply_inventory_movements';

    protected $fillable = ['description'];

    /**
     * Insumo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supply()
    {
        return $this->belongTo(Supply::class, 'supply_id');
    }
}