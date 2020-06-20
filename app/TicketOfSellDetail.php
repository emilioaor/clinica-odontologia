<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketOfSellDetail extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_of_sell_details';

    protected $fillable = [
        'ticket_of_sell_id',
        'patient_history_id'
    ];

    /**
     * TicketOfSell
     */
    public function ticketOfSell()
    {
        return $this->belongsTo(TicketOfSell::class, 'ticket_of_sell_id')->withTrashed();
    }

    /**
     * PatientHistory
     */
    public function patientHistory()
    {
        return $this->belongsTo(PatientHistory::class, 'patient_history_id')->withTrashed();
    }
}
