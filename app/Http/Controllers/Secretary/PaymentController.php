<?php

namespace App\Http\Controllers\Secretary;

use App\Payment;
use App\Patient;
use App\Product;
use App\User;
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

        $this->middleware('admin')->only([
            'destroy',
            'update'
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
        $products = Product::orderBy('name')->get();
        $doctors = User::query()->hasRole(['admin', 'doctor'], 'or')->orderBy('name')->get();
        $assistants = User::query()->hasRole('assistant')->get();
        $secretaries = User::query()->hasRole(['admin', 'secretary'], 'or')->orderBy('name')->get();

        return view('secretary.payment.create', compact('products', 'doctors', 'assistants', 'secretaries'));
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
        $payment->amount = $request->amount;
        $payment->type = intval($request->type);
        $payment->patient_history_id = $request->patient_history_id;
        $payment->user_created_id = Auth::user()->id;
        $payment->date = $request->date;
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
        $payment = Payment::findOrFail($id);
        $payment->date = new \DateTime($request->date);
        $payment->user_created_id = $request->user_created_id;
        $payment->type = $request->type;
        $payment->amount = $request->amount;
        $payment->save();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return new JsonResponse(['success' => true]);
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
            ->orderBy('created_at')
            ->with([
                'product',
                'doctor',
                'assistant'
            ])
        ;
        $paymentIds = Payment::query()
            ->select('payments.id')
            ->join('patient_history', 'patient_history.id', '=', 'payments.patient_history_id')
            ->where('patient_history.patient_id', $patient->id)
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

            $paymentIds
                ->where('payments.date', '>=', $start)
                ->where('payments.date', '<=', $end);
        }

        $services = $services->get();
        $payments = Payment::query()
            ->orderBy('date')
            ->whereIn('id', $paymentIds->get()->toArray())
            ->with([
                'userCreated',
                'patientHistory',
                'patientHistory.product'
            ])
            ->get()
        ;

        foreach ($services as $service) {
            $service->pending_amount = $service->pendingAmount();
        }

        return new JsonResponse([
            'success' => true,
            'services' => $services,
            'payments' => $payments
        ]);
    }
}
