<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test envio de Email</title>
</head>
<body>
    <h2>Formulario de contacto</h2>
    <form action={{route('contact')}} method="POST">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name">Nombre</label>
            <input name="name" type="text">
        </div>
        <div class="form-group">
            <label for="message">Mensaje</label>
            <input name="message" type="text">
        </div>
        <div class="form-group">
            <button type="submit" id='btn-contact' class="btn">Enviar</button>
        </div>
    </form>
</body>
</html>
