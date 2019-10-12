<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tracking extends Model
{
    use SoftDeletes;

    /** Status */
    const STATUS_PENDING = 0;
    const STATUS_RESOLVED = 1;

    protected $table = 'tracking';

    protected $fillable = [
        'patient_id',
        'name',
        'phone',
        'email',
        'note',
        'secretary_id'
    ];

    protected $appends = ['statusText'];

    /**
     * Patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id')->withTrashed();
    }

    /**
     * Secretary
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id')->withTrashed();
    }

    /**
     * Secretary
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trackingNotes()
    {
        return $this->hasMany(TrackingNote::class, 'tracking_id');
    }

    /**
     * Estatus
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if ($this->status === self::STATUS_PENDING) {
            return 'Pendiente';
        } elseif ($this->status === self::STATUS_RESOLVED) {
            return 'Resuelto';
        }

        return '';
    }
}
