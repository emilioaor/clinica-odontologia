@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>
                <i class="glyphicon glyphicon-home"></i>
                Bienvenido!
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Preguntas</div>

                <div class="panel-body">

                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>De</th>
                                <th>Para</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                                <tr>
                                    <td>
                                        <a href="{{ route('question.edit', ['edit' => $question->public_id]) }}">
                                            {{ $question->public_id }}
                                        </a>
                                    </td>
                                    <td>{{ $question->created_at->format('m/d/Y H:i') }}</td>
                                    <td>{{ $question->from->name }}</td>
                                    <td>{{ $question->to->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
