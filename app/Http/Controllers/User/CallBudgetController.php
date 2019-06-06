<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CallBudget;
use App\CallBudgetSource;
use App\CallBudgetHistory;
use Symfony\Component\HttpFoundation\JsonResponse;
use DB;

class CallBudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $callBudgets = CallBudget::with(['callBudgetSource'])->get();

        return view('user.callBudget.index', compact('callBudgets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $callBudgetSources = CallBudgetSource::all();

        return view('user.callBudget.create', compact('callBudgetSources'));
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

        $callBudget = new CallBudget($request->all());

        if ($request->has('contact_repeat')) {
            // Calculo la siguiente fecha de contacto
            $days = $request->get('contact_repeat');
            $contactRepeat = new \DateTime($request->get('last_call'));

            $callBudget->next_call = $contactRepeat->modify("+{$days} days");
        }

        $callBudget->save();

        $history = new CallBudgetHistory();
        $history->call_budget_id = $callBudget->id;
        $history->status = $callBudget->status;
        $history->save();

        DB::commit();

        $this->sessionMessage('message.callBudget.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('callBudget.create')
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
        DB::beginTransaction();

        $callBudget = CallBudget::with(['callBudgetSource'])->where('id', $id)->firstOrFail();
        
        if ($request->status !== $callBudget->status) {

            $callBudget->status = $request->status;

            if ($request->has('contact_repeat')) {
                // Calculo la siguiente fecha de contacto
                $days = $request->get('contact_repeat');
                $contactRepeat = new \DateTime($request->get('last_call'));

                $callBudget->next_call = $contactRepeat->modify("+{$days} days");
                $callBudget->contact_type = $request->contact_type;
            }

            $callBudget->save();

            $history = new CallBudgetHistory();
            $history->call_budget_id = $callBudget->id;
            $history->status = $callBudget->status;
            $history->save();
        }

        DB::commit();

        return new JsonResponse([
            'success' => true,
            'call_budget' => $callBudget
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
        abort(404);
    }
}
