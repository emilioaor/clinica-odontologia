<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $table = 'patients';

    protected $fillable = [
        'public_id',
        'name',
        'phone',
        'email'

    ];

    protected $dates = ['deleted_at'];

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

    /**
     * Historial del paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'patient_id');
    }

    /**
     * Notas del paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'patient_id');
    }

    /**
     * Todos los reigstros en la lista de llamadas para este paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function callLogs()
    {
        return $this->hasMany(CallLog::class, 'patient_id');
    }

    /**
     * Todos los ray x asociados al paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rayX()
    {
        return $this->hasMany(RayX::class, 'patient_id');
    }

    /**
     * Todas las citas asociadas al paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
