<?php

namespace App\Http\Controllers\Secretary;

use App\Expense;
use App\Patient;
use App\Supplier;
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

        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('secretary.expense.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('secretary.expense.create', compact('patients', 'suppliers'));
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
        abort(404);
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

        $expenses = $patient->expenses()
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->orderBy('date')
            ->with('patient')
            ->with('supplier')
            ->get();

        return new JsonResponse([
            'success' => true,
            'expenses' => $expenses
        ]);
    }
}
