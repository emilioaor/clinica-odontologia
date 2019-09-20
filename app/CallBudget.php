<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallBudget extends Model
{
    use SoftDeletes;

    /** Estatus */
    const STATUS_APPOINTMENT = 1;
    const STATUS_INTERESTED = 2;
    const STATUS_NOT_INTERESTED = 3;
    const STATUS_TRANSFERED = 4;

    /** Forma de contacto */
    const CONTACT_TYPE_PHONE = 1;
    const CONTACT_TYPE_EMAIL = 2;

    protected $table = 'call_budgets';

    protected $fillable = [
        'name',
        'service',
        'phone',
        'date',
        'email',
        'amount',
        'last_call',
        'next_call',
        'status',
        'call_budget_source_id',
        'notes',
        'contact_type',
        'sell_manager_id'
    ];

    protected $dates = [
        'date',
        'last_call',
        'next_call'
    ];

    /**
     * Fuente
     */
    public function callBudgetSource()
    {
        return $this->belongsTo(CallBudgetSource::class, 'call_budget_source_id')->withTrashed();
    }

    /**
     * Historial de estatus
     */
    public function histories()
    {
        return $this->hasMany(CallBudgetHistory::class, 'call_budget_id');
    }

    /**
     * Agente de venta
     */
    public function sellManager()
    {
        return $this->belongsTo(User::class, 'sell_manager_id');
    }

    /**
     * Llamada
     */
    public function callLog()
    {
        return $this->hasOne(CallLog::class, 'call_budget_id');
    }

    /**
     * Is contact type phone
     *
     * @return bool
     */
    public function isContactTypePhone()
    {
        return $this->contact_type === self::CONTACT_TYPE_PHONE;
    }

    /**
     * Is contact type phone
     *
     * @return bool
     */
    public function isContactTypeEmail()
    {
        return $this->contact_type === self::CONTACT_TYPE_EMAIL;
    }
}
