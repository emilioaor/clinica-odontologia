<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'public_id',
        'name',
        'phone',
        'email'
    ];

    /**
     * Genera el public id
     */
    public function nextPublicId()
    {
        $this->public_id = 'SUP' . time();
    }

    /**
     * Todos los gastos asociados a este proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'supplier_id');
    }
}
