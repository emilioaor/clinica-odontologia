<?php

namespace App\Http\Controllers\User;

use App\Note;
use App\Patient;
use App\PatientHistory;
use App\Product;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientHistoryController extends Controller
{

    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('doctor')->except([
            'searchService',
            'search'
        ]);

        $this->middleware('noAssistant');
    }

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
        $patient = Patient::where('public_id', $id)->first();

        if (! $patient) {
            abort(404);
        }

        $date = new \DateTime(empty($request->date) ? 'now' : $request->date);
        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $patient->patient_history = [];
        $patient->notes = [];
        $products = Product::all();
        $assistants = User::where('level', User::LEVEL_ASSISTANT)->orderBy('name')->get();

        return view('user.patientHistory.edit', compact('patient', 'products', 'date', 'assistants'));
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

        foreach ($request->services as $service) {
            $service = new PatientHistory($service);
            $service->patient_id = $patient->id;
            $service->created_at = $date;
            $service->save();
        }

        foreach ($request->notes as $newNote) {

            if (! empty($newNote['content'])) {
                $note = new Note();
                $note->patient_id = $patient->id;
                $note->content = $newNote['content'];
                $note->date = $date;
                $note->save();
            }
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

    /**
     * Carga la vista de busqueda de servicios
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        return view('user.patientHistory.search');
    }

    /**
     * Busca el historial de un paciente para el rango de
     * fechas especificadas
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function searchService(Request $request, $id)
    {
        $patient = Patient::where('public_id', $id)->firstOrFail();

        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        if ($start > $end) {
            return new JsonResponse([
                'success' => true,
                'services' => [],
                'notes' => []
            ]);
        }

        $services = $patient->patientHistory()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->orderBy('created_at')
            ->with('doctor')
            ->with('product')
            ->with('assistant')
            ->get()
        ;

        $notes = $patient->notes()->orderBy('date')
            ->where('content', '<>', '')
            ->whereBetween('date', [$start, $end])
            ->get()
        ;

        $servicesResponse = [];
        $d = 0;
        $lastDate = '';
        foreach ($services as $service) {

            if ($service->created_at->format('Y-m-d') !== $lastDate) {
                $d++;

                $service->formatDate = $service->created_at->format('Y-m-d');
                $lastDate = $service->created_at->format('Y-m-d');
                $servicesResponse[$d] = [];
            }

            $servicesResponse[$d][] = $service;
        }


        $notesResponse = [];
        $d = 0;
        $lastDate = '';
        foreach ($notes as $note) {

            if ($note->date !== $lastDate) {
                $d++;
                $lastDate = $note->date;
                $notesResponse[$d] = [];
            }

            $notesResponse[$d][] = $note;
        }

        return new JsonResponse([
            'success' => true,
            'services' => $servicesResponse,
            'notes' => $notesResponse
        ]);
    }
}
