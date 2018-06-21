<?php

namespace App\Http\Controllers;

use App\Question;
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

        if (Auth::user()->isAdmin()) {
            // Preguntas enviadas por el admin
            $questions = Question::orderByDesc('id')
                ->where('from_id', Auth::user()->id)
                ->where('hide', false)
                ->with(['from', 'to'])
                ->get();

        } elseif (Auth::user()->isDoctor() || Auth::user()->isAssistant()) {
            // Preguntas sin contestar por el doctor o asistente
            $questions = Question::orderByDesc('id')
                ->whereNull('answer')
                ->where('to_id', Auth::user()->id)
                ->with(['from', 'to'])
                ->get();
        }

        return view('home', compact('questions'));
    }
}
