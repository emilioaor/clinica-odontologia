<?php

namespace App\Http\Controllers\Admin;

use App\Expense;
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
                'patientHistory',
                'patientHistory.patient',
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
     * Carga la vista para el reporte de servicios y pagos por paciente
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function servicesAndPaymentsPerPatient()
    {
        return view('admin.report.servicesAndPaymentsPerPatient');
    }

    /**
     * Consulta la data para el reporte de servicios y pagos por paciente
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function servicesAndPaymentsPerPatientData(Request $request)
    {
        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $services = PatientHistory::with([
                'product',
                'doctor',
                'assistant',
                'patient',
                'payments',
            ])
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->where('patient_id', $request->patient_id)
            ->get()
        ;

        return new JsonResponse([
            'success' => true,
            'services' => $services
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
        $response = [];

        // Obtengo el ID de todos los servicios registrados o con un pago
        // registrado en el rango de fechas
        $patientHistoryIds = PatientHistory::select('patient_history.id')
            ->leftJoin('payments', 'patient_history.id', '=', 'payments.patient_history_id')
            ->leftJoin('expenses', 'patient_history.id', '=', 'expenses.patient_history_id')
            ->where('doctor_id', $doctor->id)
            ->where(function ($query) use ($start, $end) {

                $query->where([
                    ['payments.created_at', '>=', $start],
                    ['payments.created_at', '<=', $end]
                ])
                ->orWhere([
                    ['patient_history.created_at', '>=', $start],
                    ['patient_history.created_at', '<=', $end]
                ])
                ->orWhere([
                    ['expenses.created_at', '>=', $start],
                    ['expenses.created_at', '<=', $end]
                ]);
            })
            ->distinct()
            ->get();

        // Consulto los servicios
        $patientHistory = PatientHistory::whereIn('id', $patientHistoryIds->toArray())
            ->with([
                'patient',
                'product',
                'expenses',
                'payments'
            ])
            ->get();

        foreach ($patientHistory as $history) {

            if ($request->balance === 'true' && ! $history->hasComplete()) {
                // Si se pide solo balance 0 y este servicio tiene un monto pendiente, paso al siguiente
                continue;
            }

            $patient = $history->patient;

            if (! isset($response[$patient->id])) {
                $response[$patient->id] = [
                    'patient' => $patient,
                    'data' => [$history->id => []]
                ];
            }

            // Obtengo la comision configurada para este doctor y producto
            $commission = $doctor->commissionProducts()->where('product_id', $history->product->id)->first()->pivot->commission;

            $response[$patient->id]['data'][$history->id]['commission'] = $commission;
            $response[$patient->id]['data'][$history->id]['price'] = $history->price;
            $response[$patient->id]['data'][$history->id]['pendingAmount'] = $history->pendingAmount();
            $response[$patient->id]['data'][$history->id]['services'][] = [
                'date' => $history->created_at->format('Y-m-d H:i:s'),
                'classification' => trans('message.report.classification.service'),
                'description' => $history->product->name,
                'amount' => $history->price
            ];

            // Todos los gastos asociados al servicio y al laboratorio
            $expenses = $history->expenses()
                ->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
                ->where('suppliers.type', Supplier::TYPE_LAB)
                ->get();

            foreach ($expenses as $expense) {

                $response[$patient->id]['data'][$history->id]['services'][] = [
                    'date' => $expense->date,
                    'classification' => trans('message.report.classification.expense'),
                    'description' => $expense->description,
                    'amount' => $expense->amount
                ];
            }

            foreach ($history->payments as $payment) {

                $response[$patient->id]['data'][$history->id]['services'][] = [
                    'date' => $payment->created_at->format('Y-m-d H:i:s'),
                    'classification' => trans('message.report.classification.payment'),
                    'description' => trans('message.report.classification.payment'),
                    'amount' => $payment->amount
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'response' => $response
        ]);
    }

    /**
     * Reporte de gastos
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function expenses()
    {
        return view('admin.report.expenses');
    }

    /**
     * Obteniene el reporte de gastos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function expensesData(Request $request)
    {
        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $expenses = Expense::query()
            ->whereBetween('date', [
                $start,
                $end
            ])
            ->with('supplier')
            ->with('patientHistory')
            ->with('patientHistory.patient')
            ->orderBy('date')
            ->get();

        return new JsonResponse([
            'success' => true,
            'expenses' => $expenses
        ]);
    }

    /**
     * Reporte de pagos
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payments()
    {
        return view('admin.report.payments');
    }

    /**
     * Reporte de pagos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function paymentsData(Request $request)
    {
        $start = new \DateTime($request->start);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $payments = Payment::query()
            ->whereBetween('created_at', [
                $start,
                $end
            ])
            ->with([
                'patientHistory',
                'patientHistory.patient'
            ]);

        if ($request->type > 0) {
            $payments->where('type', $request->type);
        }

        return new JsonResponse(['success' => true, 'payments' => $payments->get()]);
    }
}
