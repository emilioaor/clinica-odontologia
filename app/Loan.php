<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{

    /**
     * Movimiento de inventario para pagar el prestamo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyInventoryMovementIn()
    {
        return $this->belongsTo(SupplyInventoryMovement::class, 'in_id');
    }

    /**
     * Movimiento de inventario para generar el prestamo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyInventoryMovementOut()
    {
        return $this->belongsTo(SupplyInventoryMovement::class, 'out_id');
    }
}
