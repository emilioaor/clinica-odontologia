<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;

    protected $table = 'budgets';

    protected $fillable = [
        'public_id',
        'title',
        'client_label',
        'client_phone_label',
        'client_email_label',
        'creation_date_label',
        'creation_date_value',
        'total_head_label',
        'total_head_value',
        'discount_label',
        'discount_type',
        'discount_value',
        'shaping_label',
        'shaping_value',
        'subtotal_footer_label',
        'subtotal_footer_value',
        'total_footer_label',
        'total_footer_value',
        'notes_label',
        'notes_value',
        'terms_label',
        'terms_value',
        'table_description_label',
        'table_quantity_label',
        'table_price_label',
        'table_total_label',
        'patient_id',
        'user_id'

    ];

    protected $dates = ['deleted_at'];

    /**
     * Detalles de esta cotización
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class, 'budget_id');
    }

    /**
     * Genera el siguiente Public_id
     *
     * @return int
     */
    public static function nextPublicId()
    {
        return 860 + (Budget::withTrashed()->count() + 1);
    }

    /**
     * Paciente al cual se le genero esta cotizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Usuario que registro la cotizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
