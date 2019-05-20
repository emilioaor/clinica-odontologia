<?php

namespace App\Http\Controllers\Secretary;

use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SupplierController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('secretary');

        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::orderByDesc('id')->where('doctor_commission', false)->paginate();

        return view('secretary.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('secretary.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supplier = new Supplier($request->all());
        $supplier->nextPublicId();
        $supplier->save();

        $this->sessionMessage('message.supplier.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supplier.index')
        ]);
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
        $supplier = Supplier::where('public_id', $id)->firstOrFail();

        return view('secretary.supplier.edit', compact('supplier'));
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
        $supplier = Supplier::where('public_id', $id)->firstOrFail();
        $supplier->name = $request->name;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->type = $request->type;
        $supplier->save();

        $this->sessionMessage('message.supplier.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supplier.edit', ['supplier' => $id])
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
        $supplier = Supplier::where('public_id', $id)->firstOrFail();
        $supplier->delete();

        $this->sessionMessage('message.supplier.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('supplier.index')
        ]);
    }

    /**
     * Obtiene una lista de proveedores
     *
     * @return JsonResponse
     */
    public function supplierList()
    {
        $suppliers = Supplier::orderBy('name')->where('type', '<>', Supplier::TYPE_DOCTOR_COMMISSION)->get();

        return new JsonResponse([
            'success' => true,
            'suppliers' => $suppliers
        ]);
    }
}
