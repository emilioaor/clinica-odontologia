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

    /**
     * Pagos procesados con este ticket de venta
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'ticket_of_sell_id');
    }

    public static function textWrap($text, $wrap = ' ')
    {
        $len = strlen($text);
        $response = [];
        $level = 0;

        for ($x = 0; $x < $len; $x++) {

            if (! isset($response[$level])) {
                $response[$level] = '';
            }

            $response[$level] .= $text[$x];

            if (strlen($response[$level]) >= 20 && ! preg_match('/[0-9]/', $text[$x])) {
                $level++;
            }
        }

        $response = implode($wrap, $response);

        return $response;
    }
}
