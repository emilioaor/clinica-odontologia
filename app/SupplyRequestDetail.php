<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyRequestDetail extends Model
{
    protected $table = 'supply_request_details';

    protected $fillable = [
        'supply_request_id',
        'supply_id',
        'qty',
        'approved'
    ];

    protected $with = ['supply'];

    /**
     * Solicitud de insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplyRequest()
    {
        return $this->belongsTo(SupplyRequest::class, 'supply_request_id')->withTrashed();
    }

    /**
     * Insumo solicitado en este detalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id')->withTrashed();
    }
}
