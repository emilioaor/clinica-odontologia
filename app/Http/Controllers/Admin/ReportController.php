<?php

namespace App\Http\Controllers\Admin;

use App\Payment;
use App\PatientHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends Controller
{
    /**
     * Carga la vista para el reporte de servicios y pagos
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function servicesAndPayments()
    {
        return view('admin.report.servicesAndPayments');
    }

    /**
     * Consulta la data para el reporte de servicios y pagos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function servicesAndPaymentsData(Request $request)
    {
        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $services = PatientHistory::with([
                'product',
                'doctor',
                'assistant',
                'patient'
            ])
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->get()
        ;

        $payments = Payment::with([
                'patient',
                'userCreated'
            ])
            ->where('payments.created_at', '>=', $start)
            ->where('payments.created_at', '<=', $end)
            ->get()
        ;

        return new JsonResponse([
            'success' => true,
            'services' => $services,
            'payments' => $payments
        ]);
    }
}
