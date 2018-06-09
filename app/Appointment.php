<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

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
}
