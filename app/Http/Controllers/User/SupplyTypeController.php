<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SupplyType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SupplyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplyTypes = SupplyType::all();

        return view('user.supplyType.index', compact('supplyTypes'));
    }

    /**
     * Actualiza los tipos
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        $types = $request->supplyTypes;

        $publicIds = [];
        foreach ($types as $type) {

            $supplyType = SupplyType::where('public_id', $type['public_id'])->firstOrNew([]);
            $supplyType->name = $type['name'];

            if (is_null($type['public_id'])) {
                $supplyType->generatePublicId();
            }

            $supplyType->save();

            $publicIds[] = $supplyType->public_id;
        }

        // Si no llega del front, asumo que fue eliminado
        SupplyType::whereNotIn('public_id', $publicIds)->delete();

        DB::commit();

        $this->sessionMessage('message.supplyType.update');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyType.index')]);
    }
}
