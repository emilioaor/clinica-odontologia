<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    /** Tipos de pago */
    const TYPE_CREDIT_CARD = 1;
    const TYPE_CASH = 2;
    const TYPE_CHECK = 3;

    protected $table = 'payments';

    protected $fillable = [
        'patient_id',
        'user_created_id',
        'amount',
        'type'
    ];

    protected $dates = ['deleted_at'];

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
        return $this->belongsTo(User::class, 'user_created_id');
    }
}
