<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSpooler extends Model
{
    /** Estatus de los correos */
    const STATUS_PENDING = 1;
    const STATUS_COMPLETE = 2;

    protected $table = 'email_spooler';

    protected $fillable = [
        'class',
        'status',
        'recipients',
        'params'
    ];

    /**
     * Setea los destinatarios
     *
     * @param array $recipients
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = json_encode($recipients);
    }

    /**
     * Get los destinatarios
     *
     * @return array
     */
    public function getRecipients()
    {
        return json_decode($this->recipients, true);
    }

    /**
     * Setea los parametros
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = json_encode($params);
    }

    /**
     * Get los parametros
     *
     * @return array
     */
    public function getParams()
    {
        return json_decode($this->params, true);
    }
}
