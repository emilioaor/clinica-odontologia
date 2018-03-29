<?php

namespace App\Http\Controllers\Secretary;

use App\CallLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        $callLog = new CallLog();
        $callLog->public_id = 'CALL' . time();
        $callLog->description = $request->description;
        $callLog->patient_id = $request->patient_id;
        $callLog->status = CallLog::STATUS_PENDING;
        $callLog->save();

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
        $callLog = CallLog::where('public_id', $id)->firstOrFail();
        $callLog->status = $request->status;
        $callLog->save();

        $this->sessionMessage('message.callLog.update');

        return redirect()->route('callLog.index');
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
}
