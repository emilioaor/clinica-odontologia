<?php

namespace App\Http\Controllers\User;

use App\PatientReference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PatientReferenceController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
       $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patientReferences = PatientReference::all();

        return view('user.patientReference.index', compact('patientReferences'));
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
        DB::beginTransaction();

        $patientReferenceIds = [];

        foreach ($request->references as $reference) {

            if (! isset($reference['id'])) {
                $patientReference = new PatientReference();
            } else {
                $patientReference = PatientReference::findOrFail($reference['id']);
            }

            $patientReference->description = $reference['description'];
            $patientReference->save();

            $patientReferenceIds[] = $patientReference->id;
        }

        PatientReference::query()->whereNotIn('id', $patientReferenceIds)->delete();

        DB::commit();

        $this->sessionMessage('message.patientReference.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('patientReference.index')
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }
}
