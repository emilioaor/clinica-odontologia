<?php

namespace App\Http\Controllers\User;

use App\Patient;
use App\PatientHistory;
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

        DB::commit();

        $this->sessionMessage('message.ticketOfSell.create');

        return new JsonResponse(['success' => true, 'redirect' => route('ticketOfSell.index')]);
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
                'ticketOfSellDetails.patientHistory.assistant'
            ])
            ->where('public_id', $id)
            ->firstOrFail();


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
                'patient'
            ])
            ->where('public_id', $id)
            ->firstOrFail();

        $total = 0;
        foreach ($ticketOfSell->ticketOfSellDetails as $detail) {
            $total += $detail->patientHistory->price;
        }

        $pdf = PDF::loadView('user.ticketOfSell.pdf', compact('ticketOfSell', 'total'));

        return $pdf->stream();
    }
}
