<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAttach extends Model
{
    protected $table = 'email_attaches';

    protected $fillable = [
        'product_id',
        'name',
        'url'
    ];

    /**
     * Producto al que se le asocia estos adjuntos
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
