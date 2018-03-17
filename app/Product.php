<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'public_id',
        'name',
        'price',
    ];

    /**
     * Precio formateado
     *
     * @return string
     */
    public function priceFormat()
    {
        return number_format($this->price, 2);
    }

    /**
     * Precio formateado con simbolo de moneda
     *
     * @return string
     */
    public function priceFormatWithSymbol()
    {
        return $this->getCurrencySymbol() . $this->priceFormat() . ' USD';
    }

    /**
     * Formato de moneda
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return '$';
    }

    /**
     * Indica si se puede editar un producto
     *
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * Todos los detalles que apuntan a este producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class, 'product_id');
    }

    /**
     * Elementos de historial de pacientes asociados a este producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'product_id');
    }
}
