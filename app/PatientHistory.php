<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientHistory extends Model
{
    protected $table = 'patient_history';

    protected $fillable = [
        'patient_id',
        'product_id',
        'tooth',
        'doctor_id',
        'price'

    ];

    /**
     * Paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Doctor que brindo el servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}
