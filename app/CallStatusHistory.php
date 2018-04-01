<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallStatusHistory extends Model
{
    protected $table = 'call_status_history';

    protected $fillable = [
        'call_log_id',
        'status',
        'note'
    ];

    /**
     * Llamada a la que pertenece este estatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function callLog()
    {
        return $this->belongsTo(CallLog::class, 'call_log_id');
    }
}
