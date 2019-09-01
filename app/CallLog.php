<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLog extends Model
{
    use SoftDeletes;

    /** Estatus de la lista de llamada */
    const STATUS_PENDING = 1;
    const STATUS_SCHEDULED = 2;
    const STATUS_NOT_INTERESTED= 3;
    const STATUS_NOT_ANSWER_CALL = 4;
    const STATUS_CALL_AGAIN = 5;

    protected $table = 'call_logs';

    protected $fillable = [
        'public_id',
        'description',
        'patient_id',
        'call_date',
        'status',
        'appointment_date',
        'user_id',
        'call_budget_id'

    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['statusText'];

    /**
     * Paciente al que se debe llamar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Historial de estatus para esta llamada
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistory()
    {
        return $this->hasMany(CallStatusHistory::class, 'call_log_id');
    }

    /**
     * Indica la secretaria a la que se registra la llamada
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Envío de presupuesto que genero esta llamada
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function callBudget()
    {
        return $this->belongsTo(CallBudget::class, 'call_budget_id')->withTrashed();
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

    /**
     * Status text accessor
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->statusText();
    }

    /**
     * Retorna la fecha de llamada en \Datetime
     *
     * @return \DateTime
     */
    public function callDateTime()
    {
        return new \DateTime($this->call_date);
    }
}
