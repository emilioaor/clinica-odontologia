<?php

namespace App\Http\Controllers\User;

use App\Question;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
       $this->middleware('doctor');

        $this->middleware('admin')->only([
            'create',
            'store'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::orderBy('id', 'DESC')
            ->where('from_id', Auth::user()->id)
            ->orWhere('to_id', Auth::user()->id)
            ->with([
                'from',
                'to'
            ])
            ->paginate()
        ;

        return view('user.question.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $doctors = User::orderBy('name')->where('level', User::LEVEL_DOCTOR)->get();

        return view('user.question.create', compact('doctors'));
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

        foreach ($request->doctors as $doctor) {

            $question = new Question($request->all());
            $question->from_id = Auth::user()->id;
            $question->to_id = $doctor['to_id'];
            $question->generatePublicId();
            $question->save();
        }

        DB::commit();

        $this->sessionMessage('message.question.create');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('question.index')
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
        $question = Question::where('public_id', $id)
            ->with([
                'from',
                'to'
            ])
            ->firstOrFail();

        $question->answered = $question->isAnswered();
        $question->question = str_replace("\n", '<br>', $question->question);
        $question->answer = str_replace("\n", '<br>', $question->answer);

        return view('user.question.edit', compact('question'));
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
        $question = Question::where('public_id', $id)->firstOrFail();
        $question->answer = $request->answer;
        $question->save();

        $this->sessionMessage('message.question.answered');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('question.edit', ['question' => $id])
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
