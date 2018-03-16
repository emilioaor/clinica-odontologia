<?php

namespace App\Http\Controllers\User;

use App\Note;
use App\Patient;
use App\PatientHistory;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.patientHistory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $patient = Patient::where('public_id', $id)
            ->first()
        ;

        if (! $patient) {
            abort(404);
        }

        $date = new \DateTime(empty($request->date) ? 'now' : $request->date);
        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $patient->patient_history = $patient->patientHistory()->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->get()
        ;
        $patient->note = $patient->notes()->where('date', $date->format('Y-m-d'))->first();

        $products = Product::all();

        return view('user.patientHistory.edit', compact('patient', 'products', 'date'));
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
        $patient = Patient::where('public_id', $id)->firstOrFail();

        DB::beginTransaction();

        $date = new \DateTime(empty($request->date) ? 'now' : $request->date);
        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $patient->patientHistory()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->delete();

        foreach ($request->services as $service) {
            $service = new PatientHistory($service);
            $service->patient_id = $patient->id;
            $service->created_at = $date;
            $service->save();
        }

        $note = $patient->notes()->where('date', $date->format('Y-m-d'))->first();
        if (! $note) {
            $note = new Note();
            $note->patient_id = $patient->id;
        }
        $note->content = $request->note;
        $note->date = $date;
        $note->save();

        DB::commit();

        $this->sessionMessage('message.service.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('service.edit', [
                'service' => $id,
                'date' => $date->format('Y-m-d')
            ])
        ]);
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

    public function search()
    {
        return view('user.patientHistory.search');
    }
}
