<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Appointment extends Model
{
    use SoftDeletes;

    /** Estatus de las citas */
    const STATUS_PENDING = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_CANCEL = 3;

    protected $table = 'appointments';

    protected $fillable = [
        'date',
        'patient_id',
        'user_id',
        'doctor_id'
    ];

    protected $dates = ['date'];

    /**
     * Usuario que creo la cita
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Paciente citado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Detalles de la cita
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointmentDetails()
    {
        return $this->hasMany(AppointmentDetail::class, 'appointment_id');
    }

    /**
     * Doctor con el que esta programada la cita
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Indica si la cita esta pendiente
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING  && $this->date >= (new \DateTime());
    }

    /**
     * Indica si la cita esta completa
     *
     * @return bool
     */
    public function isComplete()
    {
        return $this->status === self::STATUS_COMPLETE;
    }

    /**
     * Indica si la cita esta cancelada
     *
     * @return bool
     */
    public function isCancel()
    {
        return $this->status === self::STATUS_CANCEL;
    }

    /**
     * Indica si el paciente no asistio a la cita
     *
     * @return bool
     */
    public function isNoAssisted()
    {
        return $this->status === self::STATUS_PENDING && $this->date < (new \DateTime());
    }

    /**
     * Retorna la clase para cada status
     *
     * @return string
     */
    public function statusClass()
    {
        if ($this->isPending()) {
            return 'warning';
        } elseif ($this->isNoAssisted()) {
            return 'info';
        } elseif ($this->isComplete()) {
            return 'success';
        } elseif ($this->isCancel()) {
            return 'danger';
        }

        return '';
    }

    /**
     * Retorna el texto para cada status
     *
     * @return string
     */
    public function statusText()
    {
        if ($this->isPending()) {
            return trans('message.appointment.status.pending');
        } elseif ($this->isNoAssisted()) {
            return trans('message.appointment.status.noAssisted');
        } elseif ($this->isComplete()) {
            return trans('message.appointment.status.complete');
        } elseif ($this->isCancel()) {
            return trans('message.appointment.status.cancel');
        }

        return '';
    }

    public function scopePerUser($query)
    {
        if (Auth::check() && ! Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::user()->id);
        }
    }
}
