<?php

namespace App\Http\Controllers\User;

use App\Appointment;
use App\Mail\ServiceRegisterEmail;
use App\EmailSpooler;
use App\Note;
use App\Patient;
use App\PatientHistory;
use App\Product;
use App\RayX;
use App\Supplier;
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

        $this->middleware('admin')->only([
            'destroy',
            'deleteNote',
            'deleteImage',
            'updatePatientHistory'
        ]);
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
        $patient = Patient::where('public_id', $id)->firstOrFail();

        $date = new \DateTime(empty($request->date) ? 'now' : $request->date);
        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $patient->patient_history = [];
        $patient->notes = [];
        $products = Product::orderBy('name')->get();
        $assistants = User::where('level', User::LEVEL_ASSISTANT)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->where('type', '<>', Supplier::TYPE_DOCTOR_COMMISSION)->get();

        return view('user.patientHistory.edit', compact('patient', 'products', 'date', 'assistants', 'suppliers'));
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

        foreach ($request->services as $serviceArray) {
            $service = new PatientHistory($serviceArray);
            $service->nextPublicId();
            $service->patient_id = $patient->id;
            $service->created_at = $date;
            $service->price = $service->unit_price * $service->qty;

            if ($service->product->required_lab) {
                // Si el servicio requiere laboratiro, guardo los datos de envio
                $service->supplier_id = $serviceArray['supplier_id'];
                $service->responsible_id = $serviceArray['responsible_id'];
                $service->send_date = new \DateTime();
                $service->delivery_date = new \DateTime("{$serviceArray['delivery_date']} {$serviceArray['hour']}:{$serviceArray['minute']}");
            }

            $service->save();

            if ($service->product->send_email) {
                $emailSpooler = new EmailSpooler();
                $emailSpooler->setRecipients([$service->patient->email]);
                $emailSpooler->setParams(['product_id' => $service->product_id]);
                $emailSpooler->class = ServiceRegisterEmail::class;
                $emailSpooler->status = EmailSpooler::STATUS_PENDING;
                $emailSpooler->save();
            }
        }

        foreach ($request->notes as $newNote) {

            if (! empty($newNote['content'])) {
                $note = new Note();
                $note->patient_id = $patient->id;
                $note->user_id = Auth::user()->id;
                $note->content = $newNote['content'];
                $note->date = $date;
                $note->save();
            }
        }

        foreach ($request->images as $image) {

            $base64 = explode(',', $image);

            $upload = base64_decode($base64[1]);
            $extension = str_replace('image/png', '', $base64[0]) !== $base64[0] ? '.png' : '.jpg';

            $filename = uniqid() . $extension;
            $url = 'uploads/ray-x/' . Auth::user()->id . '/' . $filename;
            $path = public_path('uploads/ray-x') . '/' . Auth::user()->id;

            if (! is_dir($path)) {
                mkdir($path);
            }

            $path .= '/' . $filename;

            file_put_contents($path, $upload);

            $rayX = new RayX();
            $rayX->patient_id = $patient->id;
            $rayX->url = $url;
            $rayX->save();
        }

        // Si hay alguna cita asociada al paciente para ese dia se marca completa
        $appointments = Appointment::query()->whereBetween('date', [
                $start,
                $end
            ])
            ->where('patient_id', $patient->id)
            ->where('status', Appointment::STATUS_PENDING)
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->status = Appointment::STATUS_COMPLETE;
            $appointment->save();
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
        $patientHistory = PatientHistory::findOrFail($id);
        $patientHistory->delete();

        return new JsonResponse(['success' => true]);
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
        $patient = Patient::where('public_id', $id)->with('rayX')->firstOrFail();

        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        if ($start > $end) {
            return new JsonResponse([
                'success' => true,
                'data' => [],
                'images' => []
            ]);
        }

        $services = $patient->patientHistory()
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->orderBy('created_at')
            ->with('doctor')
            ->with('product')
            ->with('assistant')
            ->get()
        ;

        $notes = $patient->notes()->orderBy('date')
            ->where('content', '<>', '')
            ->whereBetween('date', [$start, $end])
            ->with('user')
            ->get()
        ;

        $response = [];
        $c = 0;
        // Armo un array de fechas
        while ($start <= $end) {

            $response[$c] = [
                'date' => $start->format('Y-m-d'),
                'services' => [],
                'notes' => []
            ];

            // Guardo en la fecha todos los servicios y notas del dia
            foreach ($services as $service) {
                if ($service->created_at->format('Y-m-d') === $start->format('Y-m-d')) {
                    $response[$c]['services'][] = $service;
                }
            }

            foreach ($notes as $note) {
                if ($note->date === $start->format('Y-m-d')) {
                    $response[$c]['notes'][] = $note;
                }
            }

            $c++;
            $start->modify('+1 day');
        }

        return new JsonResponse([
            'success' => true,
            'data' => $response,
            'images' => $patient->rayX
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteNote($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Elimina una imagen del historial de paciente
     *
     * @param $id
     * @return JsonResponse
     */
    public function deleteImage($id)
    {
        $rayX = RayX::findOrFail($id);
        $rayX->delete();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Actualiza un servicio
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updatePatientHistory(Request $request, $id)
    {
        $patientHistory = PatientHistory::where('public_id', $id)->firstOrFail();
        $patientHistory->created_at = new \DateTime($request->created_at);
        $patientHistory->product_id = $request->product_id;
        $patientHistory->tooth = $request->tooth;
        $patientHistory->doctor_id = $request->doctor_id;
        $patientHistory->assistant_id = $request->assistant_id;
        $patientHistory->price = $request->price;
        $patientHistory->save();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Registra un servicio
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerPatientHistory(Request $request)
    {
        $patientHistory = new PatientHistory($request->all());
        $patientHistory->doctor_id = Auth::user()->id;
        $patientHistory->price = $patientHistory->qty * $patientHistory->unit_price;
        $patientHistory->save();

        return new JsonResponse(['success' => true]);
    }
}
