<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentDetail extends Model
{
    protected $table = 'appointment_details';

    protected $fillable = [
        'appointment_id',
        'product_id'
    ];

    /**
     * Cita al que pertenece este detalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id')->withTrashed();
    }

    /**
     * Producto en este detalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}
