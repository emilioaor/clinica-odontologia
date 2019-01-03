<?php

namespace App\Http\Controllers\Admin;

use App\Budget;
use App\Expense;
use App\Patient;
use App\PatientReference;
use App\Payment;
use App\PatientHistory;
use App\Product;
use App\Supplier;
use App\User;
use App\SupplyBrand;
use App\SupplyType;
use App\SupplyInventoryMovement;
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

            if ($request->balance === 'true' && ! $history->hasCompleteToDate($end)) {
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
                ->where('expenses.date', '<=', $end)
                ->get();

            foreach ($expenses as $expense) {

                $response[$patient->id]['data'][$history->id]['services'][] = [
                    'date' => $expense->date->format('Y-m-d H:i:s'),
                    'classification' => trans('message.report.classification.expense'),
                    'description' => $expense->description,
                    'amount' => $expense->amount
                ];
            }

            $payments = $history->payments()->where('payments.date', '<=', $end)->get();

            foreach ($payments as $payment) {

                $type = $payment->isDiscount() ? 'discount' : 'payment';

                $response[$patient->id]['data'][$history->id]['services'][] = [
                    'date' => $payment->date->format('Y-m-d H:i:s'),
                    'classification' => trans('message.report.classification.' . $type),
                    'description' => trans('message.report.classification.' . $type),
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
                'patientHistory.product',
                'patientHistory.doctor',
                'doctorCommission'
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

    /**
     * Reporte de pacientes Vs pacientes con servicios
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function patientsAndPatientsWithServices()
    {
        $references = PatientReference::orderBy('description')->get();

        return view('admin.report.patientsAndPatientsWithService', compact('references'));
    }

    /**
     * Reporte de pacientes Vs pacientes con servicios
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function patientsAndPatientsWithServicesData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $patients = Patient::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->withCount('patientHistory');

        if ($request->reference > 0) {
            // Filtro por referencia
            $patients->where('patient_reference_id', $request->reference);
        }

        $patients = $patients->get();

        $patientsRegistered = 0;
        $patientsWithServices = 0;

        foreach ($patients as $patient) {

            $patientsRegistered++;

            if ($patient->patient_history_count > 0) {
                $patientsWithServices++;
            }
        }

        return new JsonResponse([
            'success' => true,
            'patients' => $patientsRegistered,
            'patientsWithServices' => $patientsWithServices
        ]);
    }

    /**
     * Reporte de cotizaciones
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function budgets()
    {
        return view('admin.report.budgets');
    }

    /**
     * Reporte de cotizaciones
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function budgetsData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $budgets = Budget::query()
            ->where('creation_date_Value', '>=', $start)
            ->where('creation_date_value', '<=', $end)
            ->orderBy('creation_date_value')
            ->with([
                'patient',
                'user',
                'budgetDetails'
            ])
            ->get();

        $budgetsResponse = [];
        $budgetsPerDoctor = [];

        foreach ($budgets as $budget) {

            $amount = 0;

            foreach ($budget->budgetDetails as $budgetDetail) {
                $amount += ($budgetDetail->quantity * $budgetDetail->price);
            }

            $budget->amount = $amount;

            $budgetsResponse[$budget->patient->id][] = $budget;

            if ($budget->user) {
                $budgetsPerDoctor[$budget->user->id][] = $budget;
            }
        }

        return new JsonResponse([
            'success' => true,
            'budgets' => $budgetsResponse,
            'budgetsPerDoctor' => $budgetsPerDoctor
        ]);
    }

    /**
     * Reporte de servicios pagos y gastos
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function servicesPaymentsAndExpenses()
    {
        return view('admin.report.servicesPaymentsAndExpenses');
    }

    /**
     * Reporte de servicios pagos y gastos
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function servicesPaymentsAndExpensesData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $response = [];

        $patientHistory = PatientHistory::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->with([
                'patient',
                'payments',
                'expenses',
                'product'
            ]);

        if ($request->patient > 0) {
            $patientHistory->where('patient_id', $request->patient);
        }

        $patientHistory = $patientHistory->get();

        foreach ($patientHistory as $history) {
            // Agrego los servicios a la respuesta
            $response[$history->patient->id]['patient'] = $history->patient;
            $response[$history->patient->id]['data'][] = [
                'date' => $history->created_at->format('Y-m-d'),
                'type' => 'Servicio',
                'amount' => $history->price,
                'service' => $history->product->name
            ];

            foreach ($history->payments as $payment) {
                // Agrego los pagos a la respuesta
                $response[$history->patient->id]['data'][] = [
                    'date' => $payment->date->format('Y-m-d'),
                    'type' => $payment->isDiscount() ? 'Descuento' : 'Pago',
                    'amount' => $payment->amount,
                    'service' => $history->product->name
                ];
            }

            foreach ($history->expenses as $expense) {
                // Agrego los gastos a la respuesta
                $response[$history->patient->id]['data'][] = [
                    'date' => $expense->date->format('Y-m-d'),
                    'type' => 'Gasto',
                    'amount' => $expense->amount,
                    'service' => $history->product->name
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Reporte de servicios diagnosticados
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function servicesDiagnostics()
    {
        $doctors = User::where('level', User::LEVEL_DOCTOR)->orderBy('name')->get();

        return view('admin.report.servicesDiagnostics', compact('doctors'));
    }

    /**
     * Reporte de servicios diagnosticados
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function servicesDiagnosticsData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $services = PatientHistory::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->with([
                'patient',
                'product',
                'doctor',
                'assistant'
            ])
            ->orderBy('created_at');

        if ($request->doctor > 0) {
            // Si indica el doctor busca todos los servicios diagnosticados por el doctor,
            // pero registrado por otra persona
            $services
                ->where('diagnostic_id', $request->doctor)
                ->where('doctor_id', '<>', $request->doctor);
        } else {
            // Si no indica el doctor busca todos los servicios donde el doctor que lo
            // registro, sea distinto al doctor que diagnostico
            $services
                ->whereColumn('doctor_id', '<>', 'diagnostic_id')
                ->whereNotNull('diagnostic_id');
        }

        $response = [];

        // Agrupa por doctor que diagnostica
        foreach ($services->get() as $service) {
            $response[$service->diagnostic->id][] = $service;
        }

        return new JsonResponse([
            'success' => true,
            'services' => $response
        ]);
    }

    /**
     * Reporte de servicios enviados a laboratorio
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function servicesSendLab()
    {
        return view('admin.report.servicesSendLab');
    }

    /**
     * Reporte de servicios enviados a laboratorio
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function servicesSendLabData(Request $request)
    {
        $start = new \DateTime("{$request->start} 00:00:00");
        $end = new \DateTime("{$request->end} 23:59:59");

        $serviceIds = PatientHistory::query()
            ->select(['patient_history.id'])
            ->where('patient_history.created_at', '>=', $start)
            ->where('patient_history.created_at', '<=', $end)
            ->join('products', 'products.id', '=', 'patient_history.product_id')
            ->where('products.required_lab', true)
            ->get()
            ->toArray();

        $services = PatientHistory::query()
            ->whereIn('id', $serviceIds)
            ->whereNotNull('supplier_id')
            ->with([
                'product',
                'doctor',
                'patient',
                'supplier',
                'responsible'
            ])
            ->get();

        return new JsonResponse([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Reporte de inventario de insumos
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function inventorySupply()
    {
        $supplyBrands = SupplyBrand::all();
        $supplyTypes = SupplyType::all();

        return view('admin.report.inventorySupply', compact('supplyBrands', 'supplyTypes'));
    }

    /**
     * Reporte de inventario de insumos
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function inventorySupplyData(Request $request)
    {
        $inventory = SupplyInventoryMovement::with([
                'supply',
                'supply.supplyBrand',
                'supply.supplyType'
            ])
            ->get();
                
        $response = [];
        $supplyBrand = (int) $request->brand;
        $supplyType = (int) $request->type;
        
        foreach ($inventory as $movement) {
            
            if ($supplyBrand !== 0 && $movement->supply->supplyBrand->id !== $supplyBrand) {
                continue;
            }

            if ($supplyType !== 0 && $movement->supply->supplyType->id !== $supplyType) {
                continue;
            }

            if (! isset($response[$movement->supply->id])) {
                
                $response[$movement->supply->id] = [
                    'supply' => $movement->supply,
                    'qty' => 0
                ];
            }
            
            $response[$movement->supply->id]['qty'] += $movement->qty;
        }

        return new JsonResponse(['success' => true, 'inventory' => $response]);
    }
}
