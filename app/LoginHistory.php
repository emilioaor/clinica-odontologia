<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $fillable = [
        'username',
        'date',
        'ip',
        'user_id',
        'success'
    ];

    protected $dates = ['date'];

    /**
     * User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
