<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientReference extends Model
{
    use SoftDeletes;

    protected $table = 'patient_references';

    protected $fillable = [
        'description'
    ];

    /**
     * Todos los pacientes referenciados por este medio
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'patient_reference_id');
    }
}
