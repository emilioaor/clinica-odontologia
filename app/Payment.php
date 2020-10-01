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
        'date',
        'ticket_of_sell_id',
        'checked_in_ticket'
    ];

    protected $dates = ['deleted_at', 'date'];

    protected $appends = ['paymentMethod'];

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
     * Ticket de venta generado para este pago
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticketOfSell()
    {
        return $this->belongsTo(TicketOfSell::class, 'ticket_of_sell_id')->withTrashed();
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

    /**
     * Retorna el metodo de pago
     *
     * @string
     */
    public function paymentMethod()
    {
        return trans('message.paymentMethod.' . $this->type);
    }

    /**
     * Retorna el metodo de pago
     *
     * @string
     */
    public function getPaymentMethodAttribute()
    {
        return $this->paymentMethod();
    }

    /**
     * Pagos sin revisar
     *
     * @return array
     */
    public static function paymentsWithoutCheck()
    {
        $paymentsWithoutCheck = [
            'payments' => 0,
            'start' => '',
            'end' => ''
        ];

        $payments = Payment::query()
            ->where('checked_in_ticket', false)
            ->orderBy('date')
            ->get(['date'])
        ;

        if ($paymentsWithoutCheck['payments'] = count($payments)) {
            $paymentsWithoutCheck['start'] = $payments[0]->date;
            $paymentsWithoutCheck['end'] = $payments[ count($payments) -1 ]->date;
        }

        return $paymentsWithoutCheck;
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
