<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionAttach extends Model
{
    protected $table = 'question_attaches';

    protected $fillable = [
        'question_id',
        'url',
        'filename'
    ];

    /**
     * Pregunta en la cual se adjunto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id')->withTrashed();
    }
}
