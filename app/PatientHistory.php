<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientHistory extends Model
{
    use SoftDeletes;

    protected $table = 'patient_history';

    protected $fillable = [
        'patient_id',
        'product_id',
        'tooth',
        'doctor_id',
        'assistant_id',
        'price'

    ];

    protected $dates = ['deleted_at'];

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

    /**
     * Asistente del doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }

    /**
     * Producto ofrecido
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
