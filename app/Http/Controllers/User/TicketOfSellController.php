<?php

namespace App\Http\Controllers\User;

use App\PatientHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.ticketOfSell.create');
    }

    /**
     * generate PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Request $request)
    {
        $total = 0;
        $serviceIds = explode(',', $request->services);
        $services = PatientHistory::query()
            ->with(['product'])
            ->whereIn('id', $serviceIds)
            ->get()
        ;
        $patient = $services[0]->patient;

        foreach ($services as $service) {
            $total += $service->price;
        }

        $pdf = PDF::loadView('user.ticketOfSell.pdf', compact('services', 'patient', 'total'));

        return $pdf->stream();
    }
}
