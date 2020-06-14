<?php

namespace App\Http\Controllers\Secretary;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PatientHistory;

class PatientHistorySecreatryController extends Controller
{

    public function updatePatientHistorySecretary(Request $request, $id)
    {
        $patientHistory = PatientHistory::where('public_id', $id)->firstOrFail();
        $patientHistory->created_at = new \DateTime($request->created_at);
        $patientHistory->save();

        return new JsonResponse(['success' => true]);
    }
}