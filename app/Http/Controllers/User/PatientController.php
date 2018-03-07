<?php

namespace App\Http\Controllers\User;

use App\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = Patient::orderBy('id', 'DESC')->paginate();

        return view('user.patient.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.patient.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $patient = new Patient($request->all());
        $patient->nextPublicId();
        $patient->save();

        $this->sessionMessage('message.patient.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('patient.index')
        ]);
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
        $patient = Patient::where('public_id', $id)->first();

        if (! $patient) {
            abort(404);
        }

        return view('user.patient.edit', compact('patient'));
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
        $patient->phone = $request->phone;
        $patient->name = $request->name;
        $patient->email = $request->email;
        $patient->save();

        $this->sessionMessage('message.patient.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('patient.edit', ['patient' => $id])
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

    /**
     * Valida que un numero de telefono no este registrado
     *
     * @param $phone
     * @param $public_id
     * @return JsonResponse
     */
    public function verifyPhone($phone, $public_id = null)
    {
        $patient = Patient::where('phone', $phone);

        if (! is_null($public_id)) {
            $patient->where('public_id', '<>', $public_id);
        }

        return new JsonResponse([
            'success' => true,
            'valid' => $patient->first() ? false : true
        ]);
    }
}
