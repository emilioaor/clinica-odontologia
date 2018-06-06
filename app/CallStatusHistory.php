<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallStatusHistory extends Model
{
    /** Estatus de la lista de llamada */
    const STATUS_PENDING = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_NOT_INTERESTED= 3;
    const STATUS_NOT_ANSWER_CALL = 4;
    const STATUS_CALL_AGAIN = 5;

    protected $table = 'call_status_history';

    protected $fillable = [
        'call_log_id',
        'status',
        'note'
    ];

    /**
     * Llamada a la que pertenece este estatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function callLog()
    {
        return $this->belongsTo(CallLog::class, 'call_log_id')->withTrashed();
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
    public function isNotInterested()
    {
        return $this->status === self::STATUS_NOT_INTERESTED;
    }

    /**
     * Indica si no contesto la llamada
     *
     * @return bool
     */
    public function isNotAnswerCall()
    {
        return $this->status === self::STATUS_NOT_ANSWER_CALL;
    }

    /**
     * Indica si se debe volver a llamar
     *
     * @return bool
     */
    public function isCallAgain()
    {
        return $this->status === self::STATUS_CALL_AGAIN;
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
        } elseif ($this->isNotInterested()) {
            return trans('message.callLog.status.no');
        } elseif ($this->isNotAnswerCall()) {
            return trans('message.callLog.status.notAnswerCall');
        } elseif ($this->isCallAgain()) {
            return trans('message.callLog.status.callAgain');
        }

        return '-';
    }
}
