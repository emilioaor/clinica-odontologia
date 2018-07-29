<?php

namespace App\Http\Controllers\Assistant;

use App\Supply;
use App\SupplyRequest;
use App\SupplyRequestDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class SupplyRequestController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('admin')->only([
            'index',
            'update',
            'search',
            'searchSupplyRequest'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplyRequests = SupplyRequest::orderBy('id', 'DESC')
            ->where('status', SupplyRequest::STATUS_PENDING)
            ->with('supplyRequestDetails')
            ->paginate()
        ;

        return view('assistant.supplyRequest.index', compact('supplyRequests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplies = Supply::orderBy('name')->get();

        return view('assistant.supplyRequest.create', compact('supplies'));
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

        $supplyRequest = new SupplyRequest();
        $supplyRequest->public_id = 'SR' . time();
        $supplyRequest->user_id = Auth::user()->id;
        $supplyRequest->status = SupplyRequest::STATUS_PENDING;
        $supplyRequest->save();

        foreach ($request->details as $detail) {

            $supplyRequestDetail = new SupplyRequestDetail();
            $supplyRequestDetail->supply_request_id = $supplyRequest->id;
            $supplyRequestDetail->qty = $detail['qty'];

            if ($detail['freeText']) {

                $supplyRequestDetail->description = $detail['description'];
            } else {
                $supplyRequestDetail->supply_id = Supply::findOrFail($detail['supply'])->id;
            }

            $supplyRequestDetail->save();
        }

        DB::commit();

        $this->sessionMessage('message.supplyRequest.create');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyRequest.create')]);
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
        abort(404);
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

        $supplyRequest = SupplyRequest::where('public_id', $id)
            ->with('supplyRequestDetails')
            ->firstOrFail();
        $supplyRequest->status = SupplyRequest::STATUS_APPROVED;
        $supplyRequest->save();

        foreach ($supplyRequest->supplyRequestDetails as $i => $detail) {

            foreach ($request->details as $d) {

                if ($d['id'] === $detail->id) {
                    $detail->approved = $d['approved'];
                    $detail->save();
                }
            }
        }

        DB::commit();

        $this->sessionMessage('message.supplyRequest.approved');

        return new JsonResponse(['success' => true, 'redirect' => route('supplyRequest.index')]);
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
     * Carga el formulario de busqueda
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        return view('assistant.supplyRequest.search');
    }

    /**
     * Busca las solicitudes de insumo en un rango de fechas
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSupplyRequest(Request $request)
    {
        $start = (new \DateTime($request->start))->setTime(00, 00, 00);
        $end = (new \DateTime($request->end))->setTime(23, 59, 59);

        $supplyRequests = SupplyRequest::orderBY('created_at')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->with('user')
            ->with('supplyRequestDetails')
            ->get()
        ;

        return new JsonResponse([
            'success' => true,
            'data' => $supplyRequests
        ]);
    }
}
