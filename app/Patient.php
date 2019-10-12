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
        'email',
        'patient_reference_id',
        'cancel_appointment'

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
     * Seguimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracking()
    {
        return $this->hasMany(Tracking::class, 'patient_id');
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

    /**
     * Medio por donde llego el paciente al negocio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientReference()
    {
        return $this->belongsTo(PatientReference::class, 'patient_reference_id')->withTrashed();
    }

    /**
     * Agente de venta que registro a este paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sellManager()
    {
        return $this->belongsTo(User::class, 'sell_manager_id')->withTrashed();
    }

    /**
     * Filtra por Agente de venta
     *
     * @param $query
     * @param $sellManagerId
     */
    public function scopePerSellManager($query, $sellManagerId)
    {
        if ((int) $sellManagerId) {
            // Consulta a los pacientes para ese Agente de ventas
            $query->where('sell_manager_id', $sellManagerId);
        } else {
            // Consulta todos los pacientes que pertenezcan a un Agente de ventas
            // indistintamente quien sea
            $query->whereNotNull('sell_manager_id');
        }
    }
}
