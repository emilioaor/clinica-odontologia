<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'supplier_id',
        'description',
        'date',
        'amount',
        'patient_history_id',
        'commission_id'
    ];

    protected $dates = ['date'];

    /**
     * Proveedor al que se le asocia el gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }

    /**
     * Servicio al que se asocia este gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientHistory()
    {
        return $this->belongsTo(PatientHistory::class, 'patient_history_id')->withTrashed();
    }

    /**
     * Servicio cuya comision registro este gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientHistoryCommission()
    {
        return $this->belongsTo(PatientHistory::class, 'commission_id')->withTrashed();
    }

    /**
     * Doctor cuya comision se pago con este gasto. Al obtener un reporte de comision
     * de doctores se da la opcion de registra un gasto por el monto de la comission
     * este campo es para guardar que doctor gano la comision
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctorCommission()
    {
        return $this->belongsTo(User::class, 'doctor_commission_id');
    }

    public function scopeGetTotalAmounExpense($query, $start, $end)
    {   
        return $query->selectRaw('sum(amount) as amount')
            ->where([
                    ['date', '>=', $start],
                    ['date', '<=', $end]
                ])
            ->first()->amount;
    }

    public function scopeExpenseForType($query, $start, $end)
    {   
        return $query->selectRaw('supplier_id, sum(amount) as amount')
            ->with('supplier')
            ->where([
                    ['date', '>=', $start],
                    ['date', '<=', $end]
                ])
            ->groupBy('supplier_id')
            ->orderBy('amount', 'desc')
            ->get();
    }

}
