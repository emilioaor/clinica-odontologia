<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBudgetHistory extends Model
{
    protected $table = 'call_budget_histories';

    protected $fillable = ['call_budget_id', 'status'];

    /**
     * Presupuesto
     */
    public function callBudget()
    {
        return $this->belongsTo(CallBudget::class, 'call_budget_id');
    }
}
