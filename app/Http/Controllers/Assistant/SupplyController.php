<?php

namespace App\Http\Controllers\Assistant;

use App\Supply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SupplyController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplies = Supply::orderBy('id', 'DESC')->paginate();

        return view('assistant.supply.index', compact('supplies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assistant.supply.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supply = new Supply();
        $supply->name = $request->name;
        $supply->public_id = 'SUP' . time();
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
        $supply = Supply::where('public_id', $id)->first();

        if (! $supply) {
            abort(404);
        }

        return view('assistant.supply.edit', compact('supply'));
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
        $supply = Supply::where('public_id', $id)->firstOrFail();
        $supply->name = $request->name;
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
     * @return \Illuminate\Http\Response
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
