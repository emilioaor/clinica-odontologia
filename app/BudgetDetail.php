<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetDetail extends Model
{
    protected $table = 'budget_details';

    protected $fillable = [
        'budget_id',
        'tooth',
        'product_id',
        'price',
        'quantity',

    ];

    /**
     * Cotizacion a la que pertenece este detalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    /**
     * Producto de este detalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
