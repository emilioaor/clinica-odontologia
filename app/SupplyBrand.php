<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyBrand extends Model
{
    use SoftDeletes;

    protected $table = 'supply_brands';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    /**
     * Genera un public_id
     */
    public function generatePublicId()
    {
        $this->public_id = 'S-B00' . (self::withTrashed()->count() + 1);
    }

    /**
     * Todos los insumos con esta marca
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplies()
    {
        return $this->hasMany(Supply::class, 'supply_brand_id');
    }
}
