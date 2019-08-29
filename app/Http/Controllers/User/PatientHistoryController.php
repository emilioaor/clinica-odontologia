<?php

namespace App\Http\Controllers\User;

use App\Appointment;
use App\Mail\ServiceRegisterEmail;
use App\EmailSpooler;
use App\Note;
use App\Notifications\SendLabNotification;
use App\Patient;
use App\PatientHistory;
use App\Product;
use App\RayX;
use App\Supplier;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
        $today = new \DateTime('now 00:00:00');
        $date = new \DateTime(empty($request->date) ? 'now' : $request->date);

        if ($date < $today && ! Auth::user()->isAdmin()) {
            return redirect()->route('service.edit', ['service' => $id]);
        }

        $patient = Patient::where('public_id', $id)->firstOrFail();

        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $patient->patient_history = [];
        $patient->notes = [];
        $products = Product::orderBy('name')->get();
        $assistants = User::query()->hasRole('assistant')->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->where('type', '<>', Supplier::TYPE_DOCTOR_COMMISSION)->get();
        $doctors = User::query()->hasRole('doctor')->orderBy('name')->get();

        return view('user.patientHistory.edit', compact(
            'patient',
            'products',
            'date',
            'assistants',
            'suppliers',
            'doctors'
        ));
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
        $data = json_decode($request->data, true);
        $patient = Patient::where('public_id', $id)->firstOrFail();

        DB::beginTransaction();

        $date = new \DateTime(empty($data['date']) ? 'now' : $data['date']);
        $start = clone $date;
        $start->setTime(00, 00, 00);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        foreach ($data['services'] as $serviceArray) {
            $service = new PatientHistory($serviceArray);
            $service->nextPublicId();
            $service->patient_id = $patient->id;
            $service->created_at = $date;
            $service->price = $service->unit_price * $service->qty;
            $service->diagnostic_id = $data['diagnostic'];

            if ($service->product->required_lab) {
                // Si el servicio requiere laboratiro, guardo los datos de envio
                $service->supplier_id = ! empty($serviceArray['supplier_id']) ? $serviceArray['supplier_id'] : null;
                $service->responsible_id = ! empty($serviceArray['responsible_id']) ? $serviceArray['responsible_id'] : null;
                $service->send_date = new \DateTime();

                if (! empty($serviceArray['hour']) && ! empty($serviceArray['minute'])) {
                    $service->delivery_date = new \DateTime("{$serviceArray['delivery_date']} {$serviceArray['hour']}:{$serviceArray['minute']}");

                    // Genero la notificacion
                    $users[] = $service->doctor;
                    $users[] = $service->assistant;
                    $users[] = $service->responsible;

                    Notification::send($users, new SendLabNotification($service, 24));
                    Notification::send($users, new SendLabNotification($service, 1));
                }
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

        if (count($data['services'])) {
            // Verifico si el paciente es recurrente
            $treeWeeksAfter = (clone $date)->modify('+3 weeks');
            $treeWeeksBefore = (clone $date)->modify('-3 weeks');
            $isRecurrent = $patient->patientHistory()
                ->where('created_at', '<=', $treeWeeksBefore)
                ->orWhere('created_at', '>=', $treeWeeksAfter)
                ->count()
            ;

            if ($isRecurrent) {
                $patient->recurrent = true;
                $patient->save();
            }
        }

        foreach ($data['notes'] as $newNote) {

            if (! empty($newNote['content'])) {
                $note = new Note();
                $note->patient_id = $patient->id;
                $note->user_id = Auth::user()->id;
                $note->content = $newNote['content'];
                $note->date = $date;
                $note->save();
            }
        }

        $images = $request->image ?? [];
        foreach ($images as $image) {

            $extension = '.' . $image->guessClientExtension();

            $filename = uniqid() . $extension;
            $url = 'uploads/ray-x/' . Auth::user()->id . '/' . $filename;
            $path = public_path('uploads/ray-x') . '/' . Auth::user()->id;

            if (! is_dir($path)) {
                mkdir($path);
            }

            $image->move($path, $filename);

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

        return redirect()->route('service.edit', [
            'service' => $id,
            'date' => $date->format('Y-m-d')
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

    /**
     * Vista para cargar imagenes por form html
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function uploadImage($id)
    {
        $patient = Patient::where('public_id', $id)->firstOrFail();

        return view('user.patientHistory.upload', compact('patient'));
    }

    /**
     * Carga la imagen
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeImage(Request $request, $id)
    {
        $patient = Patient::where('public_id', $id)->firstOrFail();

        /** @var UploadedFile $image */
        foreach ($request->files as $image) {

            $extension = '.' . $image->guessExtension();

            $filename = uniqid() . $extension;
            $url = 'uploads/ray-x/' . Auth::user()->id . '/' . $filename;
            $path = public_path('uploads/ray-x') . '/' . Auth::user()->id;

            if (! is_dir($path)) {
                mkdir($path);
            }

            $image->move($path, $filename);

            $rayX = new RayX();
            $rayX->patient_id = $patient->id;
            $rayX->url = $url;
            $rayX->save();
        }

        $this->sessionMessage('message.service.upload');

        return redirect()->route('service.upload', $id);
    }
}
