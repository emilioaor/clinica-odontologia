<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientsImage extends Model
{
    protected $table = 'patients_image';

    protected $fillable = [
        'patient_id',
        'url'
    ];

    /**
     * Paciente al que pertenece este ray x
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }
}
