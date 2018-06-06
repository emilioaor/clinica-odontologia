<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyRequest extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCEL = 3;

    protected $table = 'supply_requests';

    protected $fillable = [
        'public_id',
        'user_id',
        'status'
    ];

    /**
     * Usuario que genero la solicitud de insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Detalles de la solicitud de insumo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyRequestDetails()
    {
        return $this->hasMany(SupplyRequestDetail::class, 'supply_request_id');
    }

    /**
     * Indica si la solicitud esta pendiente
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Indica si la solicitud fue aprobada
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Indica si la solicitud fue cancelada
     *
     * @return bool
     */
    public function isCancel()
    {
        return $this->status === self::STATUS_CANCEL;
    }

    /**
     * Obtiene el texto correspondiente al estatus
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function statusText()
    {
        if ($this->isPending()) {
            return trans('message.supplyRequest.status.pending');
        } elseif ($this->isApproved()) {
            return trans('message.supplyRequest.status.approved');
        } elseif ($this->isCancel()) {
            return trans('message.supplyRequest.status.cancel');
        }

        return '-';
    }
}
