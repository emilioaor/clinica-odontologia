<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const ALERT_DANGER = 'alert-danger';
    const ALERT_SUCCESS = 'alert-success';

    /**
     * Crea una alerta del sistema
     *
     * @param $message
     * @param string $alertType
     */
    protected function sessionMessage($message, $alertType = 'alert-success')
    {
        Session::flash('alert-type', $alertType);
        Session::flash('alert-message', Lang::trans($message));
    }
}
