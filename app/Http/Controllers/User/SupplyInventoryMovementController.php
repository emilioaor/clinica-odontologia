<?php

namespace App\Http\Controllers\User;

use App\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supply;
use Illuminate\Http\JsonResponse;
use App\SupplyInventoryMovement;
use App\InventoryMovement;
use Illuminate\Support\Facades\DB;
use App\User;
use Auth;

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
        $loans = Loan::with([
            'supplyInventoryMovementOut.supply.supplyBrand',
            'supplyInventoryMovementOut.supply.supplyType',
            'supplyInventoryMovementOut.InventoryMovement.user',
        ])
            ->whereNull('in_id')
            ->get();

        return view('user.supplyInventoryMovement.in.create', compact('supplies', 'loans'));
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

            if ((bool) $movement['isLoan']) {
                $loan = Loan::find($movement['loan']['id']);
                $loan->in_id = $supplyInventoryMovement->id;
                $loan->save();
            }
        }

        DB::commit();

        $this->sessionMessage('message.supplyInventoryMovement.in');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyInventoryMovement.createIn')]);
    }

    /**
     * Create out
     *
     * @return \Illuminate\Http\Response
     */
    public function createOut()
    {
        $supplies = Supply::orderBy('name')->with(['supplyBrand', 'supplyType'])->get();
        $users = User::query()
            ->hasNotRole('admin')
            ->where('users.id', '<>', Auth::user()->id)
            ->orderBy('name')
            ->get()
        ;

        return view('user.supplyInventoryMovement.out.create', compact('supplies', 'users'));
    }

    /**
     * Crea una salida de insumo
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeOut(Request $request)
    {
        DB::beginTransaction();

        $inventoryMovements = $request->movements;

        $inventoryMovement = new InventoryMovement();
        $inventoryMovement->type = InventoryMovement::TYPE_OUT;
        $inventoryMovement->mark_confirmation = $request->mark_confirmation;
        $inventoryMovement->user_id = $request->user_id;
        $inventoryMovement->save();

        foreach ($inventoryMovements as $movement) {
            $supplyInventoryMovement = new SupplyInventoryMovement();
            $supplyInventoryMovement->inventory_movement_id = $inventoryMovement->id;
            $supplyInventoryMovement->supply_id = $movement['supply_id'];
            $supplyInventoryMovement->qty = $movement['qty'] * -1;
            $supplyInventoryMovement->description = 'Salida de insumo';
            $supplyInventoryMovement->save();

            if ((bool) $movement['supply']['loan_default']) {
                $loan = new Loan();
                $loan->out_id = $supplyInventoryMovement->id;
                $loan->save();
            }
        }

        DB::commit();

        $this->sessionMessage('message.supplyInventoryMovement.out');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyInventoryMovement.createOut')]);
    }
}
