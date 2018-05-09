<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    use SoftDeletes;

    protected $table = 'supplies';

    protected $fillable = [
        'public_id',
        'name'
    ];

    protected $dates = ['deleted_at'];

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
