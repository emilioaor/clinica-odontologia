<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallBudgetSource extends Model
{
    use SoftDeletes;

    protected $table = 'call_budget_sources';

    protected $filalble = ['name'];

    /**
     * Todos los presupuestos asociados a esta fuente
     */
    public function callBudgets()
    {
        return $this->hasMany(CallBudget::class, 'call_budget_source_id');
    }
}
