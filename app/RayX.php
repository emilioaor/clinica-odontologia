<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RayX extends Model
{
    use SoftDeletes;

    protected $table = 'ray_x';

    protected $fillable = [
        'patient_id',
        'url'
    ];

    /**
     * Paciente al que pertenece este ray x
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }
}
