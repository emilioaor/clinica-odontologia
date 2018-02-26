<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';

    protected $fillable = [
        'public_id',
        'business_name',
        'business_logo',
        'title',
        'client_label',
        'client_value',
        'creation_date_label',
        'creation_date_value',
        'expiration_date_label',
        'expiration_date_value',
        'total_head_label',
        'total_head_value',
        'discount_label',
        'discount_type',
        'discount_value',
        'tax_label',
        'tax_type',
        'tax_value',
        'shaping_label',
        'shaping_value',
        'total_footer_label',
        'total_footer_value',
        'amount_paid_label',
        'amount_paid_value',
        'notes_label',
        'notes_value',
        'terms_label',
        'terms_value',

    ];

    /**
     * Detalles de esta cotización
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class, 'budget_id');
    }
}
