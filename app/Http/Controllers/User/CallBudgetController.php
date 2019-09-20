<?php

namespace App\Http\Controllers\User;

use App\CallLog;
use App\Patient;
use App\PatientHistory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CallBudget;
use App\CallBudgetSource;
use App\CallBudgetHistory;
use Illuminate\Support\Facades\Auth;
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
        return view('user.callBudget.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexTable()
    {
        return view('user.callBudget.indexTable');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $callBudgetSources = CallBudgetSource::all();
        if (Auth::user()->isAdmin()) {
            $sellManagers = User::query()->hasRole('sell_manager')->get();
        } else {
            $sellManagers = User::query()->where('id', Auth::user()->id)->get();
        }

        return view('user.callBudget.create', compact('callBudgetSources', 'sellManagers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $callBudget = new CallBudget($request->except(['sell_manager_id']));
        $callBudget->sell_manager_id = $request->sell_manager_id > 0 ? $request->sell_manager_id : null;

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

        if (Auth::user()->isAdmin() && $request->sell_manager_id > 0) {
            // Es admin && Asigno un agente de ventas

            $callLog = new CallLog();
            $callLog->public_id = 'CALL' . time();
            $callLog->description = $callBudget->notes;
            $callLog->call_date = new \DateTime();
            $callLog->status = CallLog::STATUS_PENDING;
            $callLog->user_id = $request->sell_manager_id;
            $callLog->call_budget_id = $callBudget->id;
            $callLog->save();
        }

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
        $callBudget = CallBudget::find($id);
        $callBudgetSources = CallBudgetSource::all();
        $sellManagers = User::query()->hasRole('sell_manager')->get();

        return view('user.callBudget.edit', compact('callBudget', 'callBudgetSources', 'sellManagers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $callBudget = CallBudget::with(['callBudgetSource'])->where('id', $id)->firstOrFail();
        $callBudget->phone = $request->phone;
        $callBudget->email = $request->email;
        $callBudget->name = $request->name;
        $callBudget->service = $request->service;
        $callBudget->amount = $request->amount;
        $callBudget->last_call = $request->last_call;
        $callBudget->call_budget_source_id = $request->call_budget_source_id;
        $callBudget->notes = $request->notes;
        $callBudget->sell_manager_id = $request->sell_manager_id > 0 ? $request->sell_manager_id : null;
        $callBudget->save();
        
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
            'call_budget' => $callBudget,
            'redirect' => route('callBudget.edit', ['callBudget' => $id])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $callBudget = CallBudget::findOrFail($id);
        $callBudget->delete();

        return new JsonResponse(['success' => true]);
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
            ->with(['callBudgetSource', 'sellManager'])
        ;

        if (! Auth::user()->isAdmin()) {
            $callBudgets->where(function ($query) {
                $query
                    ->where('sell_manager_id', Auth::user()->id)
                    ->orWhereNull('sell_manager_id')
                ;
            });
        }

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
