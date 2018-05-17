@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de preguntas
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-responsive table-striped">
                            <thead>
                                <tr>
                                    <th width="20%">Código</th>
                                    <th>Fecha</th>
                                    <th>De</th>
                                    <th>Para</th>
                                    <th class="text-center">Respondida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($questions))
                                    @foreach($questions as $question)
                                        <tr>
                                            <td>
                                                <a href="{{ route('question.edit', ['question' => $question->public_id]) }}">
                                                    {{ $question->public_id }}
                                                </a>
                                            </td>
                                            <td>{{ $question->created_at->format('m/d/Y H:i') }}</td>
                                            <td>{{ $question->from->name }}</td>
                                            <td>{{ $question->to->name }}</td>
                                            <td class="text-center">{{ $question->isAnswered() ? 'Si' : 'No' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">
                                            No hay preguntas registradas.
                                            @if(Auth::user()->hasPermission('question.create'))
                                                <a href="{{ route('question.create') }}">Registrar pregunta</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="text-center">
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection