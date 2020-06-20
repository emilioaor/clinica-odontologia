<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketOfSell extends Model
{
    use SoftDeletes;

    protected $table = 'tickets_of_sell';

    protected $fillable = [
        'public_id',
        'patient_id',
        'user_id'
    ];

    /**
     * Genera un public_id
     */
    public function generatePublicId()
    {
        $this->public_id = 'TS-00' . (self::withTrashed()->count() + 1);
    }

    /**
     * Paciente
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Regisrado por
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Detalles
     */
    public function ticketOfSellDetails()
    {
        return $this->hasMany(TicketOfSellDetail::class, 'ticket_of_sell_id');
    }
}
