<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** Requerimientos */
    const REQUIRE_NOTHING = 0;
    const REQUIRE_LAB = 1;
    const REQUIRE_EXPENSE = 2;

    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'public_id',
        'name',
        'price',
        'required_lab',
        'email_text'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['what_require'];

    /**
     * Precio formateado
     *
     * @return string
     */
    public function priceFormat()
    {
        return number_format($this->price, 2);
    }

    /**
     * Precio formateado con simbolo de moneda
     *
     * @return string
     */
    public function priceFormatWithSymbol()
    {
        return $this->getCurrencySymbol() . $this->priceFormat() . ' USD';
    }

    /**
     * Formato de moneda
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return '$';
    }

    /**
     * Indica si se puede editar un producto
     *
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * Todos los detalles que apuntan a este producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class, 'product_id');
    }

    /**
     * Elementos de historial de pacientes asociados a este producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'product_id');
    }

    /**
     * Todos los usuarios que tienen configurado comision con este
     * producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function commissionUsers()
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id')
            ->withPivot(['commission']);
    }

    /**
     * Todos los documentos adjuntos para el correo enviado en respuesta
     * a prestar este servicio
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailAttaches()
    {
        return $this->hasMany(EmailAttach::class, 'product_id');
    }

    /**
     * Todos los detalles de cita que apuntan a este producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointmentDetails()
    {
        return $this->hasMany(AppointmentDetail::class, 'product_id');
    }

    /**
     * Indica que requiere
     *
     * @return int
     */
    public function getWhatRequireAttribute()
    {
        if ($this->required_lab) {
            return self::REQUIRE_LAB;
        }

        if ($this->required_expense) {
            return self::REQUIRE_EXPENSE;
        }

        return self::REQUIRE_NOTHING;
    }
}
