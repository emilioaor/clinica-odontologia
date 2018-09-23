<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{
    protected $table = 'weekdays';

    protected $fillable = [
        'weekday'
    ];

    /**
     * Todos los calendarios de login configurados para este dia
     * de la semana
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'login_schedules', 'weekday_id', 'user_id')
            ->withPivot(['time_start', 'time_end'])
            ;
    }
}
