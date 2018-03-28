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
    const LEVEL_SECRETARY = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'logo',
        'business_name',
        'email',
        'address',
        'phone',
        'level'
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

    /**
     * Indica si el usuario es secretaria
     *
     * @return bool
     */
    public function isSecretary()
    {
        return $this->level === self::LEVEL_SECRETARY;
    }

    /**
     * Todos los servicios brindados por un Doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'doctor_id');
    }

    /**
     * Genera el id publico en base al nivel
     */
    public function generatePublicId()
    {
        if ($this->isDoctor()) {
            $this->public_id = 'DOC' . time();
        } elseif ($this->isSecretary()) {
            $this->public_id = 'SEC' . time();
        }
    }
}
