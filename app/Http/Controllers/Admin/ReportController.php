<?php

namespace App\Http\Controllers\Admin;

use App\Expense;
use App\PatientReference;
use App\Payment;
use App\PatientHistory;
use App\Product;
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
            ->withCount('payments')
            ->get()
        ;

        if ($request->filter == 'true') {
            // Elimino los servicios que tienen pagos asociados
            foreach ($services as $i => $service) {
                if ($service->payments_count > 0) {
                    unset($services[$i]);
                }
            }
        }

        $payments = Payment::with([
                'patientHistory',
                'patientHistory.patient',
                'userCreated'
            ])
            ->where('payments.date', '>=', $start)
            ->where('payments.date', '<=', $end)
            ->get()
        ;

        // Agrupar por paciente
        $serviceResponse = [];
        $paymentsResponse = [];

        foreach ($services as $service) {
            $serviceResponse[$service->patient->id][] = $service;
        }

        foreach ($payments as $payment) {
            if ($payment->patientHistory) {
                $paymentsResponse[$payment->patientHistory->patient->id][] = $payment;
            } else {
                $paymentsResponse[0][] = $payment;
            }
        }

        return new JsonResponse([
            'success' => true,
            'services' => $serviceResponse,
            'payments' => $paymentsResponse
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
            ->withCount('payments')
            ->get()
        ;

        if ($request->filter == 'true') {
            // Elimino los servicios que tienen pagos asociados
            foreach ($services as $i => $service) {
                if ($service->payments_count > 0) {
                    unset($services[$i]);
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'services' => $services->toArray()
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
                    ['payments.date', '>=', $start],
                    ['payments.date', '<=', $end]
                ])
                ->orWhere([
                    ['patient_history.created_at', '>=', $start],
                    ['patient_history.created_at', '<=', $end]
                ])
                ->orWhere([
                    ['expenses.date', '>=', $start],
                    ['expenses.date', '<=', $end]
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
                    'date' => $payment->date->format('Y-m-d H:i:s'),
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
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.report.expenses', compact('suppliers'));
    }

    /**
     * Obteniene el reporte de gastos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function expensesData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $expenseIds = Expense::query()->select(['expenses.id'])
            ->whereBetween('date', [
                $start,
                $end
            ]);

        if ($request->type > 0) {
            $expenseIds
                ->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
                ->where('suppliers.type', $request->type);
        }

        $expenses = Expense::query()
            ->whereIn('id', $expenseIds->get()->toArray())
            ->with([
                'supplier',
                'patientHistory',
                'patientHistory.patient',
                'patientHistory.product'
            ])
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
        $references = PatientReference::all();

        return view('admin.report.payments', compact('references'));
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

        $paymentIds = Payment::query()
            ->select(['payments.id'])
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
        ;

        if ($request->type > 0) {
            $paymentIds->where('type', $request->type);
        }

        if ($request->reference > 0) {

            $paymentIds
                ->join('patient_history', 'patient_history.id', '=', 'payments.patient_history_id')
                ->join('patients', 'patients.id', '=', 'patient_history.patient_id')
                ->join('patient_references', 'patient_references.id', '=', 'patients.patient_reference_id')
                ->where('patient_references.id', $request->reference)
            ;
        }

        $payments = Payment::query()
            ->whereIn('id', $paymentIds->get()->toArray())
            ->with([
                'patientHistory',
                'patientHistory.patient',
                'patientHistory.patient.patientReference',
                'patientHistory.product'
            ])
            ->get();

        // Agrupo por paciente
        $paymentsResponse = [];

        foreach ($payments as $payment) {
            if ($payment->patientHistory) {
                $paymentsResponse[$payment->patientHistory->patient->id][] = $payment;
            } else {
                $paymentsResponse[0][] = $payment;
            }
        }

        return new JsonResponse(['success' => true, 'payments' => $paymentsResponse]);
    }

    /**
     * Reporte de garantias
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function guarantees()
    {
        $products = Product::orderBy('name')->get();
        $doctors = User::where('level', User::LEVEL_DOCTOR)->orderBy('name')->get();

        return view('admin.report.guarantees', compact('products', 'doctors'));
    }

    /**
     * Data de reporte de garantias. En este momento una garantia no es
     * mas que un servicio con costo en 0
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function guaranteesData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $guarantees = PatientHistory::query()
            ->with([
                'patient',
                'product',
                'doctor',
                'assistant'
            ])
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->where('price', 0)
        ;

        if ($request->service > 0) {
            // filtro por producto
            $guarantees->where('product_id', $request->service);
        }

        if ($request->doctor > 0) {
            // filtro por doctor
            $guarantees->where('doctor_id', $request->doctor);
        }

        return new JsonResponse([
            'success' => true,
            'guarantees' => $guarantees->get()
        ]);
    }
}
