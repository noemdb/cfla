@php
    $institucion = $data['institucion'];
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido</title>
</head>
<body>

    <table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
        <thead>
            <tr>
                <th scope="row" width="70px">
                    <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
                </th>
                <th align="center" style="text-align: center;">
                    <div class="title"><b>{{ $institucion->name }}</b></div>
                    <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
                </th>
                <th scope="row" width="70px">
                    <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
                </th>
            </tr>
        </thead>
    </table>


    <h1>¡Hola, {{ $data->name ?? null }}!</h1>
    <p>Gracias por registrarte en nuestra plataforma.</p>
</body>
</html>
