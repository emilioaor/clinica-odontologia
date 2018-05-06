<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** Tipos de pago */
    const TYPE_CREDIT_CARD = 1;
    const TYPE_CASH = 2;
    const TYPE_CHECK = 3;

    protected $table = 'payments';

    protected $fillable = [
        'patient_id',
        'user_created',
        'amount',
        'type'
    ];

    /**
     * Paciente que pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Usuario que registro el pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_created');
    }
}
