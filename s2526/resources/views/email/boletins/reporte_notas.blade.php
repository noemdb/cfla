<div style="max-width: 18cm">

    @php $pescolar = $institucion->pescolar; @endphp
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem;margin-bottom:0.2rem;">
        <thead>
            <tr>
                <th width="70px">
                    <img width="70px" height="70px" src="{{ asset('images/avatar/uecfla.jpg') }}">
                </th>
                <th style="text-align: center;">
                    <div><b>{{ $institucion->name }}</b></div>
                    <div><b>DIRECCIÓN ACADÉMICA</b></div>
                </th>
                <th width="70px">
                    <img width="100px" height="70px" src="{{ asset('images/avatar/amigoniano.png') }}">
                </th>
            </tr>
        </thead>
    </table>

    <hr>

    <p style="text-align: right;">San Felipe, {{ $toDate ?? \Carbon\Carbon::now()->format('d/m/Y') }}</p>

    <h2 style="color: #2b4c7e;">Informe de Notas Disponible</h2>

    <p style="text-align: justify;">
        Estimado(a) representante <strong>{{ $representant->name }}</strong>, titular de la cédula de identidad 
        <strong>{{ $representant->ci_representant }}</strong>,
    </p>

    <p style="text-align: justify;">
        Le informamos que el <strong>Informe de Notas</strong> correspondiente al 
        <strong>{{ $lapso->name }}</strong> del año escolar 
        <strong>{{ $pescolar->name ?? '' }}</strong> de su representado(a)
        <strong>{{ $estudiant->full_name }}</strong> ya se encuentra disponible para su descarga.
    </p>

    <p style="text-align: justify;">
        Hemos adjuntado el respectivo informe de notas en formato PDF para que pueda consultarlo fácilmente..
    </p>

    <hr>

    <div>
        <ul>
            <li style="background-color: #f0f0f0;"><strong>Estudiante</strong></li>
            <li><b>Nombre:</b> {{ $estudiant->full_name }}</li>
            <li><b>Grado:</b> {{ $estudiant->grado->name ?? 'N/A' }}</li>
        </ul>
    </div>

    <hr>

    <p style="text-align: justify;">
        Si presenta algún inconveniente con la descarga o tiene preguntas sobre el contenido del informe, por favor no dude en contactarnos a través de los medios oficiales.
    </p>

    <p style="text-align: center;">Atentamente,</p>

    <div style="text-align: left;">
        <div>{{ $autoridad->profile_professional }} {{ $autoridad->fullname }}</div>
        <div>{{ $autoridad->position }}</div>
    </div>

    <br>

    <div style="text-align: left;">
        <div>{{ $director->profile_professional }} {{ $director->fullname }}</div>
        <div>{{ $director->position }}</div>
    </div>

    <hr>

    <footer style="font-size: 0.8rem; text-align: center;">
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Email: frayluisamigoyara@hotmail.com
    </footer>
</div>
