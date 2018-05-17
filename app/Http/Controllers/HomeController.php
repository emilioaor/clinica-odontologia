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
            // Preguntas ya contestadas
            $questions = Question::orderByDesc('id')
                ->whereNotNull('answer')
                ->where('from_id', Auth::user()->id)
                ->orWhere('to_id', Auth::user()->id)
                ->with(['from', 'to'])
                ->limit(10)
                ->get();

        } elseif (Auth::user()->isDoctor()) {
            // Preguntas sin contestar
            $questions = Question::orderByDesc('id')
                ->whereNull('answer')
                ->where('from_id', Auth::user()->id)
                ->orWhere('to_id', Auth::user()->id)
                ->with(['from', 'to'])
                ->get();
        }

        return view('home', compact('questions'));
    }
}
