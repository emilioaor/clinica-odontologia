<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'questions';

    protected $fillable = [
        'title',
        'question',
        'answer',
        'to_id',
        'from_id'
    ];

    /**
     * Usuario al que se le hace la pregunta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to()
    {
        return $this->belongsTo(User::class, 'to_id')->withTrashed();
    }

    /**
     * Usuario que formulo esta pregunta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from()
    {
        return $this->belongsTo(User::class, 'from_id')->withTrashed();
    }

    /**
     * Todos los adjuntos en esta pregunta
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionAttaches()
    {
        return $this->hasMany(QuestionAttach::class, 'question_id');
    }

    /**
     * Genera el public_id de la pregunta
     */
    public function generatePublicId()
    {
        $this->public_id = 'QUE' . random_int(100000000, 999999999);
    }

    /**
     * Indica si una pregunta ya fue contestada
     *
     * @return bool
     */
    public function isAnswered()
    {
        return ! is_null($this->answer);
    }
}
