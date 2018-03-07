<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'public_id',
        'name',
        'phone',
        'email'

    ];

    /**
     * Todas las cotizaciones asociadas a este paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'patient_id');
    }

    /**
     * Genera el siguiente public_id
     */
    public function nextPublicId()
    {
        $this->public_id = 'PAT' . time();
    }
}
