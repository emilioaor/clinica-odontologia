<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /** Niveles de los usuarios */
    const LEVEL_ADMIN = 1;
    const LEVEL_DOCTOR = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'logo',
        'business_name',
        'email',
        'address',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Indica si un usuario es administrador
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    /**
     * Indica si el usuario es doctor
     *
     * @return bool
     */
    public function isDoctor()
    {
        return $this->level === self::LEVEL_DOCTOR;
    }
}
