<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    /** Tipos de pago */
    const TYPE_CREDIT_CARD = 1;
    const TYPE_CASH = 2;
    const TYPE_CHECK = 3;
    const TYPE_DISCOUNT = 4;

    protected $table = 'payments';

    protected $fillable = [
        'user_created_id',
        'amount',
        'type',
        'patient_history_id',
        'date'
    ];

    protected $dates = ['deleted_at', 'date'];

    /**
     * Usuario que registro el pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_created_id')->withTrashed();
    }

    /**
     * Servicio al que esta asociado este pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patientHistory()
    {
        return $this->belongsTo(PatientHistory::class, 'patient_history_id')->withTrashed();
    }

    /**
     * Indica si el pago es por tarjeta de credito
     *
     * @return bool
     */
    public function isCreditCard()
    {
        return $this->type === self::TYPE_CREDIT_CARD;
    }

    /**
     * Indica si el pago es en efectivo
     *
     * @return bool
     */
    public function isCash()
    {
        return $this->type === self::TYPE_CASH;
    }

    /**
     * Indica si el pago es en cheque
     *
     * @return bool
     */
    public function isCheck()
    {
        return $this->type === self::TYPE_CHECK;
    }

    /**
     * Indica si es un descuento
     *
     * @return bool
     */
    public function isDiscount()
    {
        return $this->type === self::TYPE_DISCOUNT;
    }

    public function scopeGetTotalAmountForType($query, $dateRange, $type)
    {
        if ($type != 0) {
            $query->where('type', $type);
        }
        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }
        return $query->selectRaw('*, sum(amount) as amount')
            ->groupBy('type')
            ->orderBy('type')
            ->get();
    }

    public function scopeGetTotalAmounOfService($query, $dateRange)
    {
        if ($dateRange) {
            $query->whereBetween('payments.created_at', $dateRange);
        }
        return $query->selectRaw('sum(payments.amount) as amount')
            ->leftJoin('patient_history','patient_history.id','payments.patient_history_id')
            ->leftJoin('products','products.id','patient_history.product_id')
            ->get();
    }

    public function scopeGetTotalAmounPayment($query, $start, $end)
    {   
        return $query->selectRaw('sum(amount) as amount')
            ->where([
                    ['date', '>=', $start],
                    ['date', '<=', $end]
                ])
            ->first()->amount;
    }

    public function scopeGetTotalPayments($query, $dateRange, $type)
    {
        
        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }
        return $query->selectRaw('sum(amount) as total')->first();
    }
}
