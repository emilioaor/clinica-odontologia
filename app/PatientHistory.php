<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
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
        'price',
        'qty',
        'unit_price',
        'diagnostic_id'

    ];

    protected $dates = ['deleted_at'];

    /**
     * Paciente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Doctor que brindo el servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->withTrashed();
    }

    /**
     * Asistente del doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id')->withTrashed();
    }

    /**
     * Producto ofrecido
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
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
     * Proveedor asociado, en este momento el proveedor representa el
     * laboratorio a que se envio examenenes para este servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }

    /**
     * Responsable de enviar los examenes de laboratorio. En este
     * momento es un usuario asistente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id')->withTrashed();
    }

    /**
     * Doctor que diagnostico el servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function diagnostic()
    {
        return $this->belongsTo(User::class, 'diagnostic_id')->withTrashed();
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

    /**
     * Indica el monto restante por pagar para este servicio en la fecha indicada
     *
     * @return mixed
     */
    public function pendingAmountToDate($topDate)
    {
        $pending = $this->price;
        $payments = $this->payments()->where('date', '<=', $topDate)->get();

        foreach ($payments as $payment) {
            $pending -= $payment->amount;
        }

        return $pending;
    }

    /**
     * Indica si un servicio ya esta completo. En este momento para que
     * un servicio de considere completo debe tener el balance en 0, y
     * ademas si el servicio requiere gastos de laboratorio estos ya deben
     * estar asociados o se considera incompleto
     *
     * @return bool
     */
    public function hasComplete()
    {
        if ($this->pendingAmount() > 0) {
            // Si no tiene el balance en 0 no esta completo
            return false;
        }

        if ($this->product->required_lab && ! $this->hasLabExpense()) {
            // Si el servicio requiere gastos de laboratorio y no los tiene esta incompleto
            return false;
        }

        return true;
    }

    /**
     * Indica si un servicio ya esta completo. En este momento para que
     * un servicio de considere completo debe tener el balance en 0, y
     * ademas si el servicio requiere gastos de laboratorio estos ya deben
     * estar asociados o se considera incompleto
     *
     * @param $topDate
     * @return bool
     */
    public function hasCompleteToDate($topDate)
    {
        if ($this->pendingAmountToDate($topDate) > 0) {
            // Si no tiene el balance en 0 no esta completo
            return false;
        }

        if ($this->product->required_lab && ! $this->hasLabExpenseToDate($topDate)) {
            // Si el servicio requiere gastos de laboratorio y no los tiene esta incompleto
            return false;
        }

        if ($this->product->required_expense && ! $this->hasExpenseToDate($topDate)) {
            // Si el servicio requiere gastos y no los tiene esta incompleto
            return false;
        }

        return true;
    }

    /**
     * Indica si el servicio posee gastos de laboratorio
     * asociados
     *
     * @return bool
     */
    public function hasLabExpense()
    {
        foreach ($this->expenses as $expense) {
            if ($expense->supplier->type === Supplier::TYPE_LAB) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indica si el servicio posee gastos de laboratorio
     * asociados igual o anteriores a la fecha indicada
     *
     * @param $topDate
     * @return bool
     */
    public function hasLabExpenseToDate($topDate)
    {
        $expenses = $this->expenses()->where('date', '<=', $topDate)->get();

        foreach ($expenses as $expense) {
            if ($expense->supplier->type === Supplier::TYPE_LAB) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indica si el servicio posee gastos asociados
     * igual o anteriores a la fecha indicada
     *
     * @param $topDate
     * @return bool
     */
    public function hasExpenseToDate($topDate)
    {
        $expenses = $this->expenses()->where('date', '<=', $topDate)->get();

        return (bool) count($expenses);
    }

    /**
     * Calcula el monto gastado por un paciente
     *
     * @param Builder $query
     * @param $patientId
     */
    public function scopeServiceAmount($query, $patientId)
    {
        $query
            ->selectRaw('SUM(price) as amount')
            ->where('patient_id', $patientId)
        ;
    }
}
