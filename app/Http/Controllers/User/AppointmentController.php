<?php

namespace App\Http\Controllers\User;

use App\Mail\RegisterAppointmentEmail;
use App\Appointment;
use App\AppointmentDetail;
use App\EmailSpooler;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppointmentController extends Controller
{
    /**
     * construct
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! Auth::user()->isAdmin() && ! Auth::user()->isSecretary() && ! Auth::user()->isSellManager()) {
                if ($request->ajax()) {
                    return new JsonResponse(null, 403);
                }

                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = new \DateTime(
            isset($request->start) ? $request->start : 'now'
        );

        if ($start->format('l') !== 'Sunday') {
            // Si hoy no es domingo, voy al domingo anterior para marcar el inicio de semana
            $start->modify('last Sunday');
        }

        $start->setTime(0, 0, 0);

        // Calculo el final de la semana
        $end = clone $start;
        $end->modify('+6 days');
        $end->setTime(23, 59, 59);

        // Siguiente y anterior semana
        $next = clone $end;
        $next->modify('+1 day');
        $last = clone $start;
        $last->modify('-7 days');

        // Consulto todas las citas para esa semana
        $appointments = Appointment::query()
            ->whereBetween('date', [
                $start,
                $end
            ])
            ->orderBy('date')
            ->perUser()
            ->with([
                'patient',
                'user',
                'doctor',
                'appointmentDetails.product'
            ])->get();

        $response = [
            'sunday' => [], // Domingo
            'monday' => [], // Lunes
            'tuesday' => [], // Martes
            'wednesday' => [], // Miercoles
            'thursday' => [], // Jueves
            'friday' => [], // Viernes
            'saturday' => [], // Sabado
        ];

        foreach ($appointments as $appointment) {
            $response[ strtolower($appointment->date->format('l')) ][] = $appointment;
        }

        return view('user.appointment.index', compact('response', 'start', 'end', 'next', 'last'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('user.appointment.create', compact('products'));
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

        $date = $request->date . ' ' . $request->hour . ':' . $request->minute;

        $appointment = new Appointment();
        $appointment->user_id = Auth::user()->id;
        $appointment->date = new \DateTime($date);
        $appointment->patient_id = $request->patient_id;
        $appointment->doctor_id = $request->doctor_id;
        $appointment->status = Appointment::STATUS_PENDING;
        $appointment->save();

        foreach ($request->details as $detail) {

            $appointmentDetail = new AppointmentDetail();
            $appointmentDetail->appointment_id = $appointment->id;
            $appointmentDetail->product_id = $detail['product_id'];
            $appointmentDetail->save();
        }

        $email = new EmailSpooler();
        $email->class = RegisterAppointmentEmail::class;
        $email->status = EmailSpooler::STATUS_PENDING;
        $email->setRecipients([$appointment->patient->email]);
        $email->setParams(['appointment_id' => $appointment->id]);
        $email->save();

        DB::commit();

        $this->sessionMessage('message.appointment.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('appointment.index')
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
        $products = Product::orderBy('name')->get();
        $appointment = Appointment::query()
            ->with([
                'patient',
                'doctor',
                'appointmentDetails',
                'appointmentDetails.product'
            ])
            ->findOrFail($id);

        return view('user.appointment.edit', compact('products', 'appointment'));
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
        DB::beginTransaction();

        $date = $request->date . ' ' . $request->hour . ':' . $request->minute;

        $appointment = Appointment::findOrFail($id);
        $appointment->date = new \DateTime($date);
        $appointment->patient_id = $request->patient_id;
        $appointment->doctor_id = $request->doctor_id;
        $appointment->save();

        $detailIds = [];

        // Agrego y actualizo los detalles
        foreach ($request->details as $detail) {

            if (! isset($detail['id'])) {
                $appointmentDetail = new AppointmentDetail();
            } else {
                $appointmentDetail = AppointmentDetail::find($detail['id']);
            }

            $appointmentDetail->appointment_id = $appointment->id;
            $appointmentDetail->product_id = $detail['product_id'];
            $appointmentDetail->save();

            $detailIds[] = $appointmentDetail->id;
        }

        // Elimino todos los detalles que no llegaron del frontend, asumo que fueron eliminados
        AppointmentDetail::query()->where('appointment_id', $appointment->id)->whereNotIn('id', $detailIds)->delete();

        DB::commit();

        $this->sessionMessage('message.appointment.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('appointment.edit', ['id' => $id])
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
     * Cancela una cita
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = Appointment::STATUS_CANCEL;
        $appointment->save();

        $this->sessionMessage('message.appointment.cancel');

        return redirect()->route('appointment.index');
    }
}
