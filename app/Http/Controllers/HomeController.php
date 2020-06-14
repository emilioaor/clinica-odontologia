<?php

namespace App\Http\Controllers;

use App\Question;
use App\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = [];
        $trackingNumbers = 0;
        if (Auth::user()->isAdmin()) {
            // Preguntas enviadas por el admin
            $questions = Question::orderByDesc('id')
                ->where('from_id', Auth::user()->id)
                ->where('hide', false)
                ->with(['from', 'to'])
                ->get();
            
            $trackingList = Tracking::with('secretary')->where('status', Tracking::STATUS_PENDING)->get();
            $trackingNumbers = $trackingList->count();
        } elseif (Auth::user()->isDoctor() || Auth::user()->isAssistant() || Auth::user()->isSecretary()) {
            // Preguntas sin contestar por el doctor o asistente o secretaria
            $questions = Question::orderByDesc('id')
                ->whereNull('answer')
                ->where('to_id', Auth::user()->id)
                ->with(['from', 'to'])
                ->get();
        }

        return view('home', compact('questions', 'trackingNumbers'));
    }
}
