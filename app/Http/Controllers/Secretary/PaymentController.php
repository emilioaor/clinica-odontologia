<?php

namespace App\Http\Controllers\Secretary;

use App\Payment;
use App\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('secretary');
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
        return view('secretary.payment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payment = new Payment();
        $payment->patient_id = $request->patient_id;
        $payment->amount = $request->amount;
        $payment->type = intval($request->type);
        $payment->user_created = Auth::user()->id;
        $payment->save();

        return new JsonResponse(['success' => true, 'payment' => $payment]);
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

    /**
     * Busca los pagos y servicios asociados a un paciente
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function search(Request $request, $id)
    {
        $patient = Patient::where('public_id', $id)->firstOrFail();

        $services = $patient->patientHistory()
            ->with([
                'product',
                'doctor',
                'assistant'
            ])
        ;
        $payments = $patient->payments()
            ->with([
                'patient',
                'userCreated'
            ])
        ;

        if ($request->all === 'false') {

            $start = new \DateTime($request->start);
            $start->setTime(00, 00, 00);
            $end = new \DateTime($request->end);
            $end->setTime(23, 59, 59);

            if ($start > $end) {
                return new JsonResponse(['success' => true, 'services' => [], 'payments' => [] ]);
            }

            $services
                ->where('patient_history.created_at', '>=', $start)
                ->where('patient_history.created_at', '<=', $end);

            $payments
                ->where('payments.created_at', '>=', $start)
                ->where('payments.created_at', '<=', $end);
        }

        return new JsonResponse([
            'success' => true,
            'services' => $services->get(),
            'payments' => $payments->get()
        ]);
    }
}
