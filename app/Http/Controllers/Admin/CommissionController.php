<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Carga la vista de configuración
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config()
    {
        return view('admin.commission.config');
    }

    /**
     * Actualiza las comisiones de un usuario
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $doctor = User::where('public_id', $id)->with('commissionProducts')->firstOrFail();

        DB::beginTransaction();

        foreach ($doctor->commissionProducts as $product) {

            foreach ($request->commission_products as $p) {

                if ($product->id === $p['id']) {

                    $product->pivot->commission = $p['pivot']['commission'];
                    $product->pivot->save();
                }
            }
        }

        DB::commit();

        $this->sessionMessage('message.commission.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('commission.config')
        ]);
    }
}
