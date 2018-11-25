<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SupplyBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SupplyBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplyBrands = SupplyBrand::all();

        return view('user.supplyBrand.index', compact('supplyBrands'));
    }

    /**
     * Actualiza los tipos
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        $brands = $request->supplyBrands;

        $publicIds = [];
        foreach ($brands as $brand) {

            $supplyBrand = SupplyBrand::where('public_id', $brand['public_id'])->firstOrNew([]);
            $supplyBrand->name = $brand['name'];

            if (is_null($brand['public_id'])) {
                $supplyBrand->generatePublicId();
            }

            $supplyBrand->save();

            $publicIds[] = $supplyBrand->public_id;
        }

        // Si no llega del front, asumo que fue eliminado
        SupplyBrand::whereNotIn('public_id', $publicIds)->delete();

        DB::commit();

        $this->sessionMessage('message.supplyBrand.update');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyBrand.index')]);
    }
}
