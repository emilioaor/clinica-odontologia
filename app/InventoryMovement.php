<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    /** Tipos de movimientos */
    const TYPE_IN = 1;
    const TYPE_OUT = 2;

    protected $table = 'inventory_movements';

    /**
     * Todos los moviemientos de inventario de insumos
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyInventoryMovements()
    {
        return $this->hasMany(SupplyInventoryMovement::class, 'inventory_movement_id');
    }

    /**
     * Usuario que recibio el inventario, actualmente este campo
     * se penso unicamente para las salidas de inventario
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
