<?php

namespace App\Http\Controllers\Assistant;

use App\Supply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\SupplyType;
use App\SupplyBrand;
use App\SupplyInventoryMovement;

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

        if ($request->has('width')) {

            $width = str_replace('.', '', $request->width);
            $width = str_replace(',', '.', $width);
            $width = (float) $width;

            $supplies->where('width', $width);
        }

        if ($request->has('height')) {

            $height = str_replace('.', '', $request->height);
            $height = str_replace(',', '.', $height);
            $height = (float) $height;

            $supplies->where('height', $height);
        }

        $supplies = $supplies->paginate();
        $supplyBrands = SupplyBrand::orderBy('name')->get();
        $supplyTypes = SupplyType::orderBy('name')->get();
        $filters = [
            'brand' => 0,
            'type' => 0,
            'width' => '0,00',
            'height' => '0,00'
        ];

        if ($request->has('brand')) {
            $supplies->appends('brand', $request->get('brand'));
            $filters['brand'] = $request->get('brand');
        }

        if ($request->has('type')) {
            $supplies->appends('type', $request->get('type'));
            $filters['type'] = $request->get('type');
        }

        if ($request->has('width')) {
            $supplies->appends('width', $request->get('width'));
            $filters['width'] = $request->get('width');
        }

        if ($request->has('height')) {
            $supplies->appends('height', $request->get('height'));
            $filters['height'] = $request->get('height');
        }

        return view('assistant.supply.index', compact('supplies', 'supplyBrands', 'supplyTypes', 'filters'));
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
        $width = str_replace('.', '', $request->width);
        $width = str_replace(',', '.', $width);
        $height = str_replace('.', '', $request->height);
        $height = str_replace(',', '.', $height);

        $supply = new Supply();
        $supply->name = $request->name;
        $supply->public_id = 'SUP' . time();
        $supply->supply_brand_id = $request->supply_brand_id;
        $supply->supply_type_id = $request->supply_type_id;
        $supply->width = $width;
        $supply->height = $height;
        $supply->loan_policy = $request->loan_policy;
        $supply->loan_default = $request->loan_default;
        $supply->price = $request->price;
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
        $width = str_replace('.', '', $request->width);
        $width = str_replace(',', '.', $width);
        $height = str_replace('.', '', $request->height);
        $height = str_replace(',', '.', $height);

        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supply->name = $request->name;
        $supply->supply_brand_id = $request->supply_brand_id;
        $supply->supply_type_id = $request->supply_type_id;
        $supply->width = $width;
        $supply->height = $height;
        $supply->loan_policy = $request->loan_policy;
        $supply->loan_default = $request->loan_default;
        $supply->price = $request->price;
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

    /**
     * Actualiza insumos desde el listado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateList(Request $request, $id)
    {
        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supply->name = $request->name;
        $supply->supply_brand_id = $request->supply_brand_id;
        $supply->supply_type_id = $request->supply_type_id;
        $supply->save();

        $this->sessionMessage('message.supply.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supply.index')
        ]);
    }

    public function supplyStock()
    {   
        $supplyBrands = SupplyBrand::all();
        $supplyTypes = SupplyType::all();
        return view('assistant.supply.stock', compact('supplyBrands', 'supplyTypes'));
    }

    public function supplyStockData(Request $request)
    {   
        $inventory = SupplyInventoryMovement::select(
            'supplies.id',
            'supplies.name as nameSupply',
            'supplies.width',
            'supplies.height',
            'supplies.supply_brand_id',
            'supplies.supply_type_id',
            'supply_inventory_movements.qty',
            'supply_brands.name as nameBrands',
            'supply_types.name as nameType'
            )
            ->join( 'supplies', 'supply_inventory_movements.supply_id', '=', 'supplies.id')
            ->join('supply_brands', 'supply_brands.id', '=', 'supplies.supply_brand_id')
            ->join('supply_types', 'supply_types.id', '=', 'supplies.supply_type_id');
        /*$inventory = SupplyInventoryMovement::with([
            'supply',
            'supply.supplyBrand',
            'supply.supplyType'
        ]);*/

        $response = [];
        $supplyBrand = (int) $request->brand;
        $supplyType = (int) $request->type;
        $supplyQuantity = (int) $request->quantity;
        $width = str_replace('.', '', $request->width);
        $width = str_replace(',', '.', $width);
        $width = (float) $width;
        $height = str_replace('.', '', $request->height);
        $height = str_replace(',', '.', $height);
        $height = (float) $height;
        if (! empty($request->dataSupply)) {
            $inventory->where('supplies.name', 'LIKE', "%$request->dataSupply%")
                ->orWhere('supply_brands.name', 'LIKE', "%$request->dataSupply%")
                ->orWhere('supply_types.name', 'LIKE', "%$request->dataSupply%");
        }
        //return $inventory->get();
        foreach ($inventory->get() as $movement) {

            if ($supplyBrand !== 0 && $movement->supply_brand_id !== $supplyBrand) {
                continue;
            }

            if ($supplyType !== 0 && $movement->supply_type_id !== $supplyType) {
                continue;
            }

            if ($width > 0 && $width !== $movement->width) {
                continue;
            }

            if ($height > 0 && $height !== $movement->height) {
                continue;
            }

            if ($supplyQuantity > 0 && $supplyQuantity < $movement->qty) {
                continue;
            }

            if (! isset($response[$movement->id])) {
                
                $response[$movement->id] = [
                    'supply' => $movement,
                    'qty' => 0
                ];
            }
            
            $response[$movement->id]['qty'] += $movement->qty;
        }
        //return $response;
        return new JsonResponse(['success' => true, 'inventory' => $response]);
    }

}
