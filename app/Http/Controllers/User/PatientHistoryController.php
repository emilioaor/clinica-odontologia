<?php

namespace App\Http\Controllers\User;

use App\Patient;
use App\PatientHistory;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        abort(404);
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

        $patient->patient_history = $patient->patientHistory()->where('created_at', '>=', $date->setTime(00, 00, 00))
            ->where('created_at', '<=', $date->setTime(23, 59, 59))
            ->get()
        ;

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

        $patient->patientHistory()
            ->where('created_at', '>=', $date->setTime(00, 00, 00))
            ->where('created_at', '<=', $date->setTime(23, 59, 59))
            ->delete();

        foreach ($request->services as $service) {
            $service = new PatientHistory($service);
            $service->patient_id = $patient->id;
            $service->created_at = $date;
            $service->save();
        }

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
}
