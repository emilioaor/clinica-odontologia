<?php

namespace App\Http\Controllers\User;

use App\Patient;
use App\Payment;
use App\TicketOfSell;
use App\TicketOfSellDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class TicketOfSellController extends Controller
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! empty($request->search)) {
            $patients = Patient::query()
                ->where('phone', 'LIKE', "%$request->search%")
                ->orWhere('name', 'LIKE', "%$request->search%")
                ->orderByDesc('id')
                ->get(['id'])
                ->pluck('id')
                ->toArray()
            ;

            $tickets = TicketOfSell::query()
                ->orderBy('id', 'DESC')
                ->whereIn('patient_id', $patients)
                ->limit(15)
                ->paginate();

        } else {
            $tickets = TicketOfSell::query()->orderBy('id', 'DESC')->paginate();
        }

        return view('user.ticketOfSell.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.ticketOfSell.create');
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

        $ticketOfSell = new TicketOfSell();
        $ticketOfSell->generatePublicId();
        $ticketOfSell->patient_id = $request->patient;
        $ticketOfSell->user_id = Auth::user()->id;
        $ticketOfSell->save();

        foreach ($request->services as $service) {
            $ticketOfSellDetail = new TicketOfSellDetail();
            $ticketOfSellDetail->ticket_of_sell_id = $ticketOfSell->id;
            $ticketOfSellDetail->patient_history_id = $service;
            $ticketOfSellDetail->save();
        }

        // Indico los pagos que fueron procesados en este ticket
        Payment::query()
            ->whereIn('patient_history_id', $request->services)
            ->whereNull('ticket_of_sell_id')
            ->update(['ticket_of_sell_id' => $ticketOfSell->id])
        ;

        DB::commit();

        $this->sessionMessage('message.ticketOfSell.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('ticketOfSell.edit', ['ticketOfSell' => $ticketOfSell->public_id])
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
        $ticketOfSell = TicketOfSell::query()
            ->with([
                'patient',
                'user',
                'ticketOfSellDetails.patientHistory.product',
                'ticketOfSellDetails.patientHistory.doctor',
                'ticketOfSellDetails.patientHistory.payments'
            ])
            ->where('public_id', $id)
            ->firstOrFail();

        foreach ($ticketOfSell->ticketOfSellDetails as $detail) {
            $servicePaid = 0;
            $servicePaidWithoutTicket = 0;
            $serviceDiscount = 0;

            foreach ($detail->patientHistory->payments as $payment) {
                if (! $payment->isDiscount()) {

                    $servicePaid += $payment->amount;

                    if (! $payment->ticket_of_sell_id) {
                        $servicePaidWithoutTicket += $payment->amount;
                    }
                } else {
                    $serviceDiscount += $payment->amount;
                }
            }

            $detail->patientHistory->paid = $servicePaid;
            $detail->patientHistory->paidWithoutTicket = $servicePaidWithoutTicket;
            $detail->patientHistory->discount = $serviceDiscount;
        }


        return view('user.ticketOfSell.edit', compact('ticketOfSell'));
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

    /**
     * generate PDF
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function generatePdf($id)
    {
        $ticketOfSell = TicketOfSell::query()
            ->with([
                'ticketOfSellDetails.patientHistory.product',
                'ticketOfSellDetails.patientHistory.payments',
                'patient'
            ])
            ->where('public_id', $id)
            ->firstOrFail();

        $subtotal = 0;
        $paid = 0;
        $discount = 0;
        $paymentMethods = [];
        foreach ($ticketOfSell->ticketOfSellDetails as $detail) {
            $subtotal += $detail->patientHistory->price;

            foreach ($detail->patientHistory->payments as $payment) {

                if (! $payment->isDiscount()) {
                    $paid += $payment->amount;

                    if (! in_array($payment->paymentMethod(), $paymentMethods)) {
                        $paymentMethods[] = $payment->paymentMethod();
                    }
                } else {
                    $discount += $payment->amount;
                }
            }
        }

        $total = $subtotal - $discount;
        $balance = $total - $paid;

        $pdf = PDF::loadView('user.ticketOfSell.pdf', compact('ticketOfSell', 'subtotal', 'total', 'paymentMethods', 'paid', 'balance', 'discount'));

        return $pdf->stream();
    }
}
