<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingNote extends Model
{
    protected $table = 'tracking_notes';

    protected $fillable = [
        'tracking_id',
        'note',
        'user_id'
    ];

    /**
     * Tracking
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tracking()
    {
        return $this->belongsTo(Tracking::class, 'tracking_id');
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
