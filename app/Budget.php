<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';

    protected $fillable = [
        'public_id',
        'title',
        'client_label',
        'client_value',
        'creation_date_label',
        'creation_date_value',
        'total_head_label',
        'total_head_value',
        'discount_label',
        'discount_type',
        'discount_value',
        'shaping_label',
        'shaping_value',
        'subtotal_footer_label',
        'subtotal_footer_value',
        'total_footer_label',
        'total_footer_value',
        'notes_label',
        'notes_value',
        'terms_label',
        'terms_value',
        'table_description_label',
        'table_quantity_label',
        'table_price_label',
        'table_total_label'

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

    /**
     * Genera el siguiente Public_id
     *
     * @return int
     */
    public static function nextPublicId()
    {
        return 860 + (Budget::count() + 1);
    }
}
