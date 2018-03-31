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
        'note',
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

    /**
     * Indica si esta pendiente
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Indica si esta citado
     *
     * @return bool
     */
    public function isScheduled()
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Indica si no esta interesado
     *
     * @return bool
     */
    public function isStatusNO()
    {
        return $this->status === self::STATUS_NO;
    }

    /**
     * @return string
     */
    public function statusText()
    {
        if ($this->isPending()) {
            return trans('message.callLog.status.pending');
        } elseif ($this->isScheduled()) {
            return trans('message.callLog.status.scheduled');
        } elseif ($this->isStatusNO()) {
            return trans('message.callLog.status.no');
        }

        return '-';
    }
}
