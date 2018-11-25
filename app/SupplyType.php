<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyType extends Model
{
    use SoftDeletes;

    protected $table = 'supply_types';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    /**
     * Genera un public_id
     */
    public function generatePublicId()
    {
        $this->public_id = 'S-T00' . (self::withTrashed()->count() + 1);
    }
}
