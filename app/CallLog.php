<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{

    /** Estatus de la lista de llamada */
    const STATUS_PENDING = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_NO = 3;

    protected $table = 'call_logs';

    protected $fillable = [
        'public_id',
        'description',
        'patient_id',
        'status'

    ];

    /**
     * Paciente al que se debe llamar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
