<?php

namespace App\Http\Controllers\Secretary;

use App\CallLog;
use App\CallStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CallLogController extends Controller
{

    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('secretary');

        $this->middleware('doctor')->except([
            'index',
            'update'
        ]);

        $this->middleware('admin')->only([
            'search',
            'searchCall'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $callLogs = CallLog::orderByDesc('call_date')
            ->whereDate('call_date', '<=', new \DateTime())
            ->where('status', '<>', CallLog::STATUS_NOT_INTERESTED)
            ->where('status', '<>', CallLog::STATUS_SCHEDULED)
            ->paginate()
        ;

        return view('secretary.callLog.index', compact('callLogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('secretary.callLog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $callLog = new CallLog();
        $callLog->public_id = 'CALL' . time();
        $callLog->description = $request->description;
        $callLog->patient_id = $request->patient_id;
        $callLog->status = CallLog::STATUS_PENDING;
        $callLog->call_date = new \DateTime();
        $callLog->save();

        $callStatusHistory = new CallStatusHistory();
        $callStatusHistory->call_log_id = $callLog->id;
        $callStatusHistory->status = CallLog::STATUS_PENDING;
        $callStatusHistory->note = $request->description;
        $callStatusHistory->save();

        DB::commit();

        $this->sessionMessage('message.callLog.create');

        return new JsonResponse(['success' => true, 'redirect' => route('callLog.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $callLog = CallLog::where('public_id', $id)->firstOrFail();
        $callLog->status = $request->status;

        if ($request->status == CallLog::STATUS_NOT_ANSWER_CALL) {
            // Si no contesto la llamada, debe aparecer de nuevo al dia siguiente
            $callLog->call_date = $callLog->callDateTime()->modify('+1 day');
        } elseif ($request->status == CallLog::STATUS_CALL_AGAIN) {
            // Si es volver a llamar se cambia la fecha por la indicada por el paciente
            $callLog->call_date = $request->date_again;
        } elseif ($callLog->status == CallLog::STATUS_SCHEDULED) {
            // Si tiene cita guardo la fecha
            $callLog->appointment_date = $request->date_again;
        }

        $callLog->save();

        $callStatusHistory = new CallStatusHistory();
        $callStatusHistory->call_log_id = $callLog->id;
        $callStatusHistory->status = $request->status;
        $callStatusHistory->note = $request->note;
        $callStatusHistory->save();

        DB::commit();

        $this->sessionMessage('message.callLog.update');

        return new JsonResponse(['success' => true, 'redirect' => route('callLog.index')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }

    /**
     * Carla la vista para busqueda de llamadas
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        return view('secretary.callLog.search');
    }

    /**
     * Busca las llamadas hechas en un rango de fechas
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCall(Request $request)
    {
        $calls = CallLog::orderBY('call_date')
            ->whereBetween('call_date', [
                $request->start,
                $request->end
            ])
            ->with('patient')
            ->get()
        ;

        $status[CallLog::STATUS_PENDING] = ['statusText' => trans('message.callLog.status.pending')];
        $status[CallLog::STATUS_SCHEDULED] = ['statusText' => trans('message.callLog.status.scheduled')];
        $status[CallLog::STATUS_NOT_INTERESTED] = ['statusText' => trans('message.callLog.status.no')];
        $status[CallLog::STATUS_NOT_ANSWER_CALL] = ['statusText' => trans('message.callLog.status.notAnswerCall')];
        $status[CallLog::STATUS_CALL_AGAIN] = ['statusText' => trans('message.callLog.status.callAgain')];

        return new JsonResponse([
            'success' => true,
            'calls' => $calls,
            'status' => $status
        ]);
    }
}
