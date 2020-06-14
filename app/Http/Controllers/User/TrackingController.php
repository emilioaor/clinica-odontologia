<?php

namespace App\Http\Controllers\User;

use App\Tracking;
use App\TrackingNote;
use App\User;
use App\CallLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('secretary');
        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trackingList = Tracking::query()
            ->with([
                'secretary',
                'trackingNotes'
            ])
            ->where('status', Tracking::STATUS_PENDING);

        if (!Auth::user()->isAdmin()) {
            $trackingList->where('secretary_id', Auth::user()->id);
        }

        $title = false;
        $trackingList = $trackingList->get();
        $secretaries = User::query()->hasRole(['secretary', 'admin'], 'or')->get();

        return view('user.tracking.index', compact('trackingList', 'secretaries', 'title'));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resolved()
    {
        
        $title = true;
        $trackingList =[];
        $secretaries =[];
        return view('user.tracking.resolved', compact('trackingList', 'secretaries', 'title'));
    }

    public function search($dateStart, $dateEnd)
    {   
        $trackingList = Tracking::query()
        ->with([
            'secretary',
            'trackingNotes'
        ])
        ->where('status', Tracking::STATUS_RESOLVED)
        ->whereBetween('created_at', $this->getDateRange($dateStart, $dateEnd));
        
        if (!Auth::user()->isAdmin()) {
            $trackingList->where('secretary_id', Auth::user()->id);
        }

        $trackingList = $trackingList->get();
        $secretaries = User::query()->hasRole(['secretary', 'admin'], 'or')->get();

        $status[CallLog::STATUS_PENDING] = ['statusText' => trans('message.callLog.status.pending')];
        $status[CallLog::STATUS_SCHEDULED] = ['statusText' => trans('message.callLog.status.scheduled')];
        $status[CallLog::STATUS_NOT_INTERESTED] = ['statusText' => trans('message.callLog.status.no')];
        $status[CallLog::STATUS_NOT_ANSWER_CALL] = ['statusText' => trans('message.callLog.status.notAnswerCall')];
        $status[CallLog::STATUS_CALL_AGAIN] = ['statusText' => trans('message.callLog.status.callAgain')];

        return new JsonResponse([
            'success' => 200,
            'trackingList' => $trackingList,
            'secretaries' => $secretaries,
            'callLogs' => CallLog::with('statusHistory')->get(),
            'callStatus' => $status
        ]);
    }

    private function getDateRange($dateSatrt, $dateEnd)
    {
        $start = new \DateTime();
        $start->setTime(00, 00, 00);
        $end = new \DateTime();
        $end->setTime(23, 59, 59);

        if ($dateSatrt) {
            $start = new \DateTime($dateSatrt);
            $start->setTime(00, 00, 00);
        }

        if ($dateEnd) {
            $end = new \DateTime($dateEnd);
            $end->setTime(23, 59, 59);
        }

        return [$start, $end];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $secretaries = User::query()->hasRole(['secretary', 'admin'], 'or')->get();

        return view('user.tracking.create', compact('secretaries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $tracking = new Tracking($request->all());
        $tracking->status = Tracking::STATUS_PENDING;
        $tracking->secretary_id = $request->secretary_id > 0 ? $request->secretary_id : null;

        if (!Auth::user()->isAdmin()) {
            $tracking->secretary_id = Auth::user()->id;
        }

        $tracking->save();

        $this->sessionMessage('message.tracking.create');

        return new JsonResponse(['success' => true, 'redirect' => route('tracking.index')]);
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
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $tracking = Tracking::query()->find($id);
        $tracking->delete();

        $this->sessionMessage('message.tracking.delete');

        return new JsonResponse(['success' => true, 'redirect' => route('tracking.index')]);
    }

    /**
     * Add note to tracking
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function note(Request $request)
    {
        DB::beginTransaction();

        $tracking = Tracking::query()->find($request->tracking_id);
        $tracking->status = $request->status;
        $tracking->save();

        if (!empty($request->note)) {
            $trackingNote = new TrackingNote($request->all());
            $trackingNote->user_id = Auth::user()->id;
            $trackingNote->save();
        }
        DB::commit();

        $this->sessionMessage('message.tracking.addedNote');

        return new JsonResponse(['success' => true, 'redirect' => route('tracking.index')]);
    }
}
