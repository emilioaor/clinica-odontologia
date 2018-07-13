<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    /** Tipos de proveedores */
    const TYPE_OFFICE = 1;
    const TYPE_GENERAL_SERVICES = 2;
    const TYPE_HUMAN_RESOURCES = 3;
    const TYPE_MAINTENANCE = 4;
    const TYPE_PHARMACY = 5;
    const TYPE_IMPLANTS = 6;
    const TYPE_DENTAL_DEPOSIT = 7;
    const TYPE_LAB = 8;
    const TYPE_IMAGES = 9;
    const TYPE_DOCTOR_COMMISSION = 10;

    protected $table = 'suppliers';

    protected $fillable = [
        'public_id',
        'name',
        'phone',
        'email',
        'type'
    ];

    /**
     * Genera el public id
     */
    public function nextPublicId()
    {
        $this->public_id = 'SUP' . time();
    }

    /**
     * Todos los gastos asociados a este proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'supplier_id');
    }

    /**
     * Historial de paciente que indica a este proveedor como
     * laboratorio utilizado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'supplier_id');
    }
}
