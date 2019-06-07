<?php

namespace App\Http\Controllers\User;

use App\CallLog;
use App\Patient;
use App\PatientReference;
use App\CallBudget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{

    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            if (! Auth::user()->isAdmin() && ! Auth::user()->isDoctor() && ! Auth::user()->isSellManager()) {
                if ($request->ajax()) {
                    return new JsonResponse(null, 403);
                }

                return redirect()->route('home');
            }

            return $next($request);
        })->except([
            'index',
            'search'
        ]);

        $this->middleware('noAssistant')->except([
            'search'
        ]);

        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $patients = Patient::orderBy('id', 'DESC');

        if (Auth::user()->isSellManager()) {
            $patients->where('sell_manager_id', Auth::user()->id);
        }

        if (! empty($request->search)) {
            $patients = $patients
                ->where(function ($query) use ($request) {
                    $query
                        ->where('phone', 'LIKE', "%$request->search%")
                        ->orWhere('name', 'LIKE', "%$request->search%");
                })
                ->limit(15)
            ;
        }

        $patients = $patients->paginate();

        return view('user.patient.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $patientReferences = PatientReference::orderBy('description')->get();

        return view('user.patient.create', compact('patientReferences'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $patient = new Patient($request->all());
        $patient->nextPublicId();

        if (Auth::user()->isSellManager()) {
            $patient->sell_manager_id = Auth::user()->id;
        }

        $patient->save();

        if (Auth::user()->isSellManager() && (bool) $request->register_call) {
            // Registra una llamada pendiente para este paciente
            $callLog = new CallLog();
            $callLog->public_id = 'CALL' . time();
            $callLog->description = trans('message.callLog.note.newPatient');
            $callLog->patient_id = $patient->id;
            $callLog->call_date = new \DateTime('+1 day');
            $callLog->status = CallLog::STATUS_PENDING;
            $callLog->user_id = Auth::user()->id;
            $callLog->save();
        }

        DB::commit();

        $this->sessionMessage('message.patient.create');

        return new JsonResponse(['success' => true, 'redirect' => route('patient.index')]);
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
        $patient = Patient::where('public_id', $id)->firstOrFail();
        $patientReferences = PatientReference::orderBy('description')->get();

        $patient->budgets = $patient->budgets()
            ->orderBy('id', 'DESC')
            ->paginate(12)
        ;

        return view('user.patient.edit', compact('patient', 'patientReferences'));
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
        $patient->patient_reference_id = $request->patient_reference_id;
        $patient->cancel_appointment = $request->cancel_appointment;
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
        $patient = Patient::where('public_id', $id)->firstOrFail();
        $patient->delete();

        $this->sessionMessage('message.patient.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('patient.index')
        ]);
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
        $phoneError = true;
        $isPatient = true;

        if (! is_null($public_id)) {
            $patient->where('public_id', '<>', $public_id);
        }

        $patient = $patient->first();

        if (! $patient) {
            // Si no existe el paciente, lo busco en la tabla de presupuestos enviados
            $callBudget = CallBudget::where('phone', $phone)->orderBy('id', 'DESC')->first();
            $phoneError = false;
            
            if ($callBudget) {
                $isPatient = false;

                $patient = [
                    'name' => $callBudget->name,
                    'email' => $callBudget->email
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'valid' => $patient ? false : true,
            'patient' => $patient,
            'phoneError' => $phoneError,
            'isPatient' => $isPatient
        ]);
    }

    /**
     * Obtiene una lista de pacientes disponibles y aplicando
     * filtros
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $patients = Patient::orderBy('id', 'DESC')
            ->orderBy('phone')
            ->orderBy('name')
            ->limit(10)
        ;

        if (! empty($request->search)) {
            $patients
                ->where('phone', 'LIKE', "%$request->search%")
                ->orWhere('name', 'LIKE', "%$request->search%")
            ;
        }

        return new JsonResponse([
            'success' => true,
            'patients' => $patients->get()
        ]);
    }
}
