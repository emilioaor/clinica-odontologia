<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CallBudgetSource;
use DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class CallBudgetSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $callBudgetSources = CallBudgetSource::all();

        return view('user.callBudgetSource.index', compact('callBudgetSources'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
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

        $callBudgetSourceIds = [];

        foreach ($request->references as $reference) {

            if (! isset($reference['id'])) {
                $callBudgetSource = new CallBudgetSource();
            } else {
                $callBudgetSource = CallBudgetSource::findOrFail($reference['id']);
            }

            $callBudgetSource->name = $reference['name'];
            $callBudgetSource->save();

            $callBudgetSourceIds[] = $callBudgetSource->id;
        }

        CallBudgetSource::query()->whereNotIn('id', $callBudgetSourceIds)->delete();

        DB::commit();

        $this->sessionMessage('message.callBudgetSource.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('callBudgetSource.index')
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
        abort(404);
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
}
