<?php

namespace App\Http\Controllers\User;

use App\Budget;
use App\BudgetDetail;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $budgets = Budget::orderByDesc('created_at')->paginate();

        return view('user.budget.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::all();

        return view('user.budget.create', compact('products'));
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

        $budget = new Budget($request->all());
        $budget->subtotal_footer_value = 1000; // TODO quitar ?
        $budget->total_footer_value = 1000; // TODO quitar ?
        $budget->save();

        foreach ($request->details as $detail) {

            $budgetDetail = new BudgetDetail();
            $budgetDetail->budget_id = $budget->id;
            $budgetDetail->product_id = $detail['product_id'];
            $budgetDetail->quantity = $detail['quantity'];
            $budgetDetail->price = $detail['price'];
            $budgetDetail->save();
        }

        $this->sessionMessage('message.budget.create');

        DB::commit();

        return new JsonResponse(['success' => true, 'redirect' => route('budget.index')]);

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
        //
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
}
