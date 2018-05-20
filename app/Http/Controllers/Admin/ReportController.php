<?php

namespace App\Http\Controllers\Admin;

use App\Payment;
use App\PatientHistory;
use App\Supplier;
use App\User;
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

    /**
     * Carga la vista para el reporte de comisiones de doctores
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function doctorCommissions()
    {
        return view('admin.report.doctorCommissions');
    }

    /**
     * Consulta la data para el reporte de comisiones de doctores
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function doctorCommissionsData(Request $request)
    {
        $doctor = User::where('public_id', $request->doctor)->with('commissionProducts')->firstOrFail();
        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $patientHistory = PatientHistory::where('doctor_id', $doctor->id)
            ->with([
                'patient',
                'product',
                'expenses'
            ])
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->get()
        ;

        foreach ($patientHistory as &$history) {

            // Sumo los gastos por cada servicio
            $history->expense_total = 0;

            // Todos los gastos asociados al servicio y al laboratorio
            $expenses = $history->expenses()
                ->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
                ->where('suppliers.type', Supplier::TYPE_LAB)
                ->get();

            foreach ($expenses as $expense) {
                $history->expense_total += $expense->amount;
            }

            // Obtengo el porcentaje de comision para el producto
            foreach ($doctor->commissionProducts as $product) {
                if ($product->id === $history->product->id) {
                    $history->commission_product = $product->pivot->commission;
                }
            }

            // Calcula la comision
            $history->commission = ($history->price - $history->expense_total) * ($history->commission_product / 100);
        }

        return new JsonResponse([
            'success' => true,
            'services' => $patientHistory
        ]);
    }
}
