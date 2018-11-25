<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supply;
use Illuminate\Http\JsonResponse;
use App\SupplyInventoryMovement;
use App\InventoryMovement;
use Illuminate\Support\Facades\DB;

class SupplyInventoryMovementController extends Controller
{
    /**
     * Create in
     *
     * @return \Illuminate\Http\Response
     */
    public function createIn()
    {
        $supplies = Supply::orderBy('name')->with(['supplyBrand', 'supplyType'])->get();

        return view('user.supplyInventoryMovement.in.create', compact('supplies'));
    }

    /**
     * Crea una entrada de insumo
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function storeIn(Request $request)
    {
        DB::beginTransaction();

        $inventoryMovements = $request->inventoryMovements;

        $inventoryMovement = new InventoryMovement();
        $inventoryMovement->type = InventoryMovement::TYPE_IN;
        $inventoryMovement->save();

        foreach ($inventoryMovements as $movement) {
            $supplyInventoryMovement = new SupplyInventoryMovement();
            $supplyInventoryMovement->inventory_movement_id = $inventoryMovement->id;
            $supplyInventoryMovement->supply_id = $movement['supply_id'];
            $supplyInventoryMovement->qty = $movement['qty'];
            $supplyInventoryMovement->description = 'Entrada de insumo';
            $supplyInventoryMovement->save();
        }

        DB::commit();

        $this->sessionMessage('message.supplyInventoryMovement.in');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyInventoryMovement.createIn')]);
    }
}
