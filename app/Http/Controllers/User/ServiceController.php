<?php

namespace App\Http\Controllers\User;

use App\Budget;
use App\Product;
use App\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
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
        $budget = Budget::where('public_id', $id)
            ->with('services')
            ->with('patient')
            ->first()
        ;

        if (! $budget) {
            abort(404);
        }

        $products = Product::all();

        return view('user.service.edit', compact('budget', 'products'));
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
        $budget = Budget::where('public_id', $id)->firstOrFail();

        DB::beginTransaction();

        $budget->services()->delete();

        foreach ($request->services as $service) {
            $service = new Service($service);
            $service->budget_id = $budget->id;
            $service->save();
        }

        DB::commit();

        $this->sessionMessage('message.service.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('service.edit', ['service' => $id])
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
}
