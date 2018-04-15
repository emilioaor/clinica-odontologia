<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $table = 'supplies';

    protected $fillable = [
        'public_id',
        'name'
    ];

    /**
     * Todos los detalles de solicitud de insumo que piden este insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyRequestDetails()
    {
        return $this->hasMany(SupplyRequestDetail::class, 'supply_id');
    }
}
