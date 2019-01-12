<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyInventoryMovement extends Model
{
    protected $table = 'supply_inventory_movements';

    protected $fillable = ['description'];

    /**
     * Movimiento de inventario padre
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryMovement()
    {
        return $this->belongsTo(InventoryMovement::class, 'inventory_movement_id');
    }

    /**
     * Insumo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id')->withTrashed();
    }

    /**
     * Prestamo, se tiene este objeto en caso que se genere por la salida
     * en este movimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loanIn()
    {
        return $this->hasOne(Loan::class, 'in_id');
    }

    /**
     * Prestamo, se tiene este objeto en caso que se salde por la entrada
     * en este movimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loanOut()
    {
        return $this->hasOne(Loan::class, 'out_id');
    }
}