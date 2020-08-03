<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table border="1">
        <thead>
        <tr style="background-color: #3097D1;color: #ffffff;">
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
        </tr>
        </thead>
        <tbody>
        @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->phone }}</td>
                <td>{{ $patient->email }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>