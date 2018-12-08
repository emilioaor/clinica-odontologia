<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    /** Policitas de prestamo */
    const LOAN_POLICY_FLEX = 0;
    const LOAN_POLICY_TODAY = 1;

    use SoftDeletes;

    protected $table = 'supplies';

    protected $fillable = [
        'public_id',
        'name'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Marca del insumo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyBrand()
    {
        return $this->belongsTo(SupplyBrand::class, 'supply_brand_id')->withTrashed();
    }

    /**
     * Tipo del insumo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyType()
    {
        return $this->belongsTo(SupplyType::class, 'supply_type_id')->withTrashed();
    }

    /**
     * Todos los detalles de solicitud de insumo que piden este insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyRequestDetails()
    {
        return $this->hasMany(SupplyRequestDetail::class, 'supply_id');
    }

    /**
     * Todos los movimientos de inventario asociados al insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyInventoryMovements()
    {
        return $this->hasMany(SupplyInventoryMovement::class, 'supply_id');
    }
}
