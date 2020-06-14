<?php

namespace App\Http\Controllers\Admin;

use App\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PatientHistory;
use Illuminate\Support\Facades\Auth;

use App\Payment;
use App\Role;
use App\User;
use Illuminate\Http\JsonResponse;

class GraphicsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isSellManager()) {
                if ($request->ajax()) {
                    return new JsonResponse(null, 403);
                }

                return redirect()->route('home');
            }

            return $next($request);
        });
    }
    public function paymentCharts()
    {
        return view('admin.graphics.paymentCharts');
    }

    public function commmissionCharts() 
    {
        return view('admin.graphics.commissiontCharts');
    }

    public function getDataPaymentCharts($dateSatrt = NULL, $dateEnd = NULL, $type = NULL)
    {   
        return new JsonResponse([
            'success' => 200,
            'totalAmounForType' => Payment::getTotalAmountForType($this->getDateRange($dateSatrt, $dateEnd), $type),
            'totalPayments' => Payment::getTotalPayments($this->getDateRange($dateSatrt, $dateEnd), $type)
        ]);
    }

    public function getCommmissionCharts($dateSatrt = NULL, $dateEnd = NULL)
    {   
        $start = new \DateTime($dateSatrt);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($dateEnd);
        $end->setTime(23, 59, 59);
        return new JsonResponse([
            'success' => 200,
            'totalPayments' => Payment::getTotalAmounPayment($start, $end),
            'totalExpenses' => Expense::getTotalAmounExpense($start, $end)
        ]);
    }

    public function getDataExpenseForType($dateSatrt = NULL, $dateEnd = NULL) {
        $start = new \DateTime($dateSatrt);
        $start->setTime(00, 00, 00);
        $end = new \DateTime($dateEnd);
        $end->setTime(23, 59, 59);
        $expensesType = [];
        foreach (Expense::expenseForType($start, $end) as $value) {
            array_push($expensesType, $value->supplier->name);
        }
        return new JsonResponse([
            'success' => 200,
            'totalPayments' => Payment::getTotalAmounPayment($start, $end),
            'totalExpenses' => Expense::expenseForType($start, $end),
            'typeExpenses' => $expensesType
        ]);
    }

    private function getDateRange($dateSatrt, $dateEnd)
    {
        $start = new \DateTime();
        $start->setTime(00, 00, 00);
        $end = new \DateTime();
        $end->setTime(23, 59, 59);

        if ($dateSatrt) {
            $start = new \DateTime($dateSatrt);
            $start->setTime(00, 00, 00);
        }

        if ($dateEnd) {
            $end = new \DateTime($dateEnd);
            $end->setTime(23, 59, 59);
        }

        return [$start, $end];
    }
}
