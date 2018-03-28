<?php

namespace App\Http\Controllers\Secretary;

use App\CallLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallLogController extends Controller
{

    /**
     * Carga la lista de llamadas pendientes
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $callLogs = CallLog::orderByDesc('created_at')
            ->where('status', CallLog::STATUS_PENDING)
            ->paginate()
        ;

        return view('secretary.callLog.index', compact('callLogs'));
    }

    /**
     * Actualiza el registro de llamada pendiente
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $callLog = CallLog::where('public_id', $id)->firstOrFail();
        $callLog->status = $request->status;
        $callLog->save();

        $this->sessionMessage('message.callLog.update');

        return redirect()->route('callLog.index');
    }
}
