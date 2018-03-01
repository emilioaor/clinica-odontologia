<?php

namespace App\Http\Controllers\User;

use App\Budget;
use App\BudgetDetail;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use PDF;

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

        return new JsonResponse(['success' => true, 'redirect' => route('budget.edit', ['budget' => $budget->id])]);

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
        $budget = Budget::where('public_id', $id)->with('budgetDetails')->first();

        if (! $budget) {
            abort(404);
        }

        $budget->details = $budget->budgetDetails;
        $products = Product::all();

        return view('user.budget.edit', compact('budget', 'products'));
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
        DB::beginTransaction();

        $budget = Budget::where('public_id', $id)->with('budgetDetails')->firstOrFail();
        $budget->update($request->all());

        $budget->budgetDetails()->delete();

        foreach ($request->details as $detail) {
            $budgetDetail = new BudgetDetail($detail);
            $budgetDetail->budget_id = $budget->id;
            $budgetDetail->save();
        }

        DB::commit();

        $this->sessionMessage('message.budget.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('budget.edit', ['budget' => $id])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }

    /**
     * Carga el logo de la cotizacion
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadLogo(Request $request)
    {
        $base64 = explode(',', $request->logo);

        $logo = base64_decode($base64[1]);
        $extension = str_replace('image/png', '', $base64[0]) !== $base64[0] ? '.png' : '.jpg';

        $filename = time() . $extension;
        $path = public_path('uploads') . '/' . $filename;

        file_put_contents($path, $logo);

        return new JsonResponse(['success' => true, 'filename' => $filename]);
    }

    /**
     * Genera un pdf de una cotizacion
     *
     * @param $id
     * @return mixed
     */
    public function generatePdf($id)
    {
        $budget = Budget::where('public_id', $id)->first();

        if (! $budget) {
            abort(404);
        }

        $pdf = PDF::loadView('user.budget.pdf', compact('budget'));

        return $pdf->stream();
    }
}
