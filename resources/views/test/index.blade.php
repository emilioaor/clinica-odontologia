<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input type="file" name="image" id="image">
        <br><br>
        <button>Cargar</button>
    </form>
</body>
</html>