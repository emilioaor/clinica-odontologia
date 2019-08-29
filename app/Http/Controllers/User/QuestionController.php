<?php

namespace App\Http\Controllers\User;

use App\Question;
use App\QuestionAttach;
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
       $this->middleware('question');

        $this->middleware('admin')->only([
            'create',
            'store',
            'hide'
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
        $doctors = User::query()
            ->hasRole(['doctor', 'assistant', 'secretary'], 'or')
            ->orderBy('name')
            ->get();

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

        $attaches = $this->uploadAttach($request->attaches);

        foreach ($request->doctors as $doctor) {

            $question = new Question($request->all());
            $question->from_id = Auth::user()->id;
            $question->to_id = $doctor['to_id'];
            $question->generatePublicId();
            $question->save();

            foreach ($attaches as $attach) {

                $questionAttach = new QuestionAttach();
                $questionAttach->url = $attach['url'];
                $questionAttach->filename = $attach['filename'];
                $questionAttach->question_id = $question->id;
                $questionAttach->type = QuestionAttach::TYPE_QUESTION;
                $questionAttach->save();
            }
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
                'to',
                'questionAttaches'
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
        DB::beginTransaction();

        $question = Question::where('public_id', $id)->firstOrFail();
        $question->answer = $request->answer;
        $question->save();

        $answerAttaches = [];

        foreach ($request->question_attaches as $attach) {
            if ($attach['type'] === QuestionAttach::TYPE_ANSWER) {
                $answerAttaches[] = $attach;
            }
        }

        $attaches = $this->uploadAttach($answerAttaches);

        foreach ($attaches as $attach) {

            $questionAttach = new QuestionAttach();
            $questionAttach->url = $attach['url'];
            $questionAttach->filename = $attach['filename'];
            $questionAttach->question_id = $question->id;
            $questionAttach->type = QuestionAttach::TYPE_ANSWER;
            $questionAttach->save();
        }

        DB::commit();

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
        $question = Question::where('public_id', $id)->firstOrFail();
        $question->delete();

        $this->sessionMessage('message.question.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('question.index')
        ]);
    }

    /**
     * Oculta la pregunta para el usuario que la registro
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function hide(Request $request, $id)
    {
        $question = Question::where('public_id', $id)->firstOrFail();
        $question->hide = true;
        $question->save();

        $this->sessionMessage('message.question.hide');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('question.edit', ['id' => $id])
        ]);
    }

    /**
     * Sube los documentos adjuntos
     *
     * @param array $attaches
     * @return array
     */
    private function uploadAttach(array $attaches)
    {
        $responseAttaches = [];

        foreach ($attaches as $attach) {

            $base64 = explode(',', $attach['file']);

            $upload = base64_decode($base64[1]);
            $explode = explode(';', $attach['file']);
            $explode = explode('/', $explode[0]);
            $extension = $explode[1];

            $filename = uniqid() . '.' . $extension;
            $url = 'uploads/question/' . Auth::user()->id . '/' . $filename;
            $path = public_path('uploads/question') . '/' . Auth::user()->id;

            if (! is_dir($path)) {
                mkdir($path);
            }

            $path .= '/' . $filename;

            file_put_contents($path, $upload);

            $attach['url'] = $url;
            $responseAttaches[] = $attach;
        }

        return $responseAttaches;
    }
}
