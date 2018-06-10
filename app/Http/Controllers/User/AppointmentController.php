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
            ->with([
                'patient'
            ])
            ->get();

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
