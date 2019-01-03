<?php

namespace App\Http\Controllers\Secretary;

use App\Expense;
use App\Patient;
use App\Supplier;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExpenseController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('secretary');

        $this->middleware('admin')->only([
            'destroy',
            'expenseCommission'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('secretary.expense.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->where('doctor_commission', false)->get();

        return view('secretary.expense.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        foreach ($request->all() as $exp) {

            $expense = new Expense($exp);
            $expense->save();
        }

        DB::commit();

        $this->sessionMessage('message.expense.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('expense.create')
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
        $expense = Expense::findOrFail($id);
        $expense->supplier_id = $request->supplier_id;
        $expense->description = $request->description;
        $expense->patient_history_id = $request->patient_history_id;
        $expense->amount = $request->amount;
        $expense->date = new \DateTime($request->date);
        $expense->save();

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
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Busca todos los gastos asociados a un paciente para un
     * rango de fechas
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function search(Request $request, $id)
    {
        $patient = Patient::where('public_id', $id)->firstOrFail();

        $start = new \DateTime($request->start);
        $start->setTime(0, 0, 0);

        $end = new \DateTime($request->end);
        $end->setTime(23, 59, 59);

        $expenseIds = Expense::select(['expenses.id'])
            ->join('patient_history', 'patient_history.id', '=', 'expenses.patient_history_id')
            ->join('patients', 'patients.id', '=', 'patient_history.patient_id')
            ->where('patients.id', $patient->id)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->distinct()
            ->get()
        ;

        $expenses = Expense::whereIn('id', $expenseIds->toArray())
            ->with([
                'patientHistory',
                'supplier'
            ])
            ->orderBy('date')
            ->get();

        return new JsonResponse([
            'success' => true,
            'expenses' => $expenses
        ]);
    }

    /**
     * Registra un gasto a partir de la comision de un doctor
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function expenseCommission(Request $request)
    {
        $supplier = Supplier::where('doctor_commission', true)->first();
        $doctor = User::where('public_id', $request->public_id)->first();

        $expense = new Expense();
        $expense->amount = $request->amount;
        $expense->date = new \DateTime();
        $expense->description = trans('message.expense.doctor.commission');
        $expense->supplier_id = $supplier->id;
        $expense->doctor_commission_id = $doctor->id;
        $expense->save();

        $this->sessionMessage('message.expense.create');

        return new JsonResponse(['success' => true]);
    }
}
