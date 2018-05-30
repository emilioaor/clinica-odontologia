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

    /**
     * Todos los gastos asociados a este servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'patient_history_id');
    }

    /**
     * Pagos asociados al servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'patient_history_id');
    }

    /**
     * Genera el siguiente public_id
     */
    public function nextPublicId()
    {
        $this->public_id = 'SER' . (PatientHistory::withTrashed()->count() + 1);
    }

    /**
     * Indica el monto restante por pagar para este servicio
     *
     * @return mixed
     */
    public function pendingAmount()
    {
        $pending = $this->price;

        foreach ($this->payments as $payment) {
            $pending -= $payment->amount;
        }

        return $pending;
    }
}
