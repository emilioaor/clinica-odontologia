<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'patient_id',
        'date',
        'content'
    ];

    /**
     * Paciente al que le se registro esta nota
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
