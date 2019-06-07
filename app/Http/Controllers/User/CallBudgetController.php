<?php

namespace App\Http\Controllers\User;

use App\Patient;
use App\PatientHistory;
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

    /**
     * Busqueda
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $start = (new \DateTime($request->start))->setTime(0, 0, 0);
        $end = (new \DateTime($request->end))->setTime(23, 59, 59);

        $callBudgets = CallBudget::query()
            ->whereBetween('last_call', [$start, $end])
            ->with(['callBudgetSource'])
        ;

        if (! empty($filter = $request->filter)) {
            // Filtro por telefono || nombre || email
            $callBudgets->where(function ($query) use ($filter) {
                $query
                    ->where('name', $filter)
                    ->orWhere('phone', $filter)
                    ->orWhere('email', $filter)
                ;
            });
        }

        $callBudgets = $callBudgets->get();

        if ($request->with_service === 'true') {
            // Filtro los presupuestos que no tuvieron conversion a servicio

            $callBudgets = $callBudgets->filter(function ($callBudget) use ($start, $end) {

                $patient = Patient::query()->where('phone', $callBudget->phone)->first();

                if (! $patient) {
                    return false;
                }

                $patientHistories = PatientHistory::query()
                    ->whereBetween('created_at', [$start, $end])
                    ->where('patient_id', $patient->id)
                    ->count()
                ;

                if ($patientHistories === 0) {
                    return false;
                }

                return true;
            });
        }

        return new JsonResponse([
            'success' => true,
            'callBudgets' => $callBudgets
        ]);
    }
}
