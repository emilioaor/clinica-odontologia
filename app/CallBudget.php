<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBudget extends Model
{
    /** Estatus */
    const STATUS_APPOINTMENT = 1;
    const STATUS_INTERESTED = 2;
    const STATUS_NOT_INTERESTED = 3;

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
        'notes'
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
}
