<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Secreataria</th>
                <th>Paciente</th>
                <th>Estatus</th>
                <th>Nota</th>
                <th>Telefono</th>
            </tr>
        </thead>
        <tbody>
            @foreach($callLogs as $callLog)
                <tr>
                    <td>{{ (new \DateTime($callLog['call_date']))->format('m/d/Y') }}</td>
                    <td>{{ $callLog['to']['name'] }}</td>
                    <td>{{ $callLog['patient']['name'] }}</td>
                    <td>{{ $callLog['statusText'] }}</td>
                    <td>{{ $callLog['status_history'][count($callLog['status_history']) -1]['note'] }}</td>
                    <td>{{ $callLog['patient']['phone'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>