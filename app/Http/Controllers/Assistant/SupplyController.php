<?php

namespace App\Http\Controllers\Assistant;

use App\Supply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\SupplyType;
use App\SupplyBrand;

class SupplyController extends Controller
{

    public function __construct()
    {
        $this->middleware('managementSupply');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $supplies = Supply::orderBy('id', 'DESC')
            ->with(['supplyType', 'supplyBrand']);

        if ($request->has('brand')) {
            $supplies->where('supply_brand_id', $request->get('brand'));
        }

        if ($request->has('type')) {
            $supplies->where('supply_type_id', $request->get('type'));
        }

        $supplies = $supplies->paginate();
        $supplyBrands = SupplyBrand::orderBy('name')->get();
        $supplyTypes = SupplyType::orderBy('name')->get();

        if ($request->has('brand')) {
            $supplies->appends('brand', $request->get('brand'));
        }

        if ($request->has('type')) {
            $supplies->appends('type', $request->get('type'));
        }

        return view('assistant.supply.index', compact('supplies', 'supplyBrands', 'supplyTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplyBrands = SupplyBrand::orderBy('name')->get();
        $supplyTypes = SupplyType::orderBy('name')->get();

        return view('assistant.supply.create', compact('supplyBrands', 'supplyTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $supply = new Supply();
        $supply->name = $request->name;
        $supply->public_id = 'SUP' . time();
        $supply->supply_brand_id = $request->supply_brand_id;
        $supply->supply_type_id = $request->supply_type_id;
        $supply->width = $request->width;
        $supply->height = $request->height;
        $supply->loan_policy = $request->loan_policy;
        $supply->save();

        $this->sessionMessage('message.supply.create');

        return new JsonResponse(['success' => true, 'redirect' => route('supply.index')]);
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
        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supplyBrands = SupplyBrand::orderBy('name')->get();
        $supplyTypes = SupplyType::orderBy('name')->get();

        return view('assistant.supply.edit', compact('supply', 'supplyBrands', 'supplyTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supply->name = $request->name;
        $supply->supply_brand_id = $request->supply_brand_id;
        $supply->supply_type_id = $request->supply_type_id;
        $supply->width = $request->width;
        $supply->height = $request->height;
        $supply->loan_policy = $request->loan_policy;
        $supply->save();

        $this->sessionMessage('message.supply.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supply.edit', ['supply' => $id])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supply->delete();

        $this->sessionMessage('message.supply.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supply.index')
        ]);
    }
}
