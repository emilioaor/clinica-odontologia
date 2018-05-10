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
}
