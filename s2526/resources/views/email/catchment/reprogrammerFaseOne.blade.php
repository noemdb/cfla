@php $pescolar = $institucion->pescolar; @endphp
<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN ACADÉMICA</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
            </th>
        </tr>
    </thead>
</table>

<hr>

<table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0">
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($catchment)

                    <h1>Reprogramación de la convocatoria</h1>
                    <h3>Proceso de Matriculación Escolar {{ ($pescolar) ? $pescolar->name : null }}</h3>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment->full_name_representant}}</strong>, CI; {{$catchment->representant_ci}},</em>
                        <div>Fecha de nacimiento: {{$catchment->representant_date_birth ?? null}}</div>
                        <div>Teléfono: {{$catchment->representant_phone ?? null}}</div>
                    </p>

                    <hr>

                    <p style="font-size: 0.8rem;">
                        Comprendemos que su ajetreada rutina puede ocasionar olvidos involuntarios.
                        Por eso, hemos reorientado la actividades de matriculación, ampliando el plazo para que pueda acudir sin inconvenientes y garantizar que ningún niño o niña se quede sin su cupo escolar.
                        <h2 style="font-size: 1rem;">Le esperamos en nuestra institución el día 08/05/2024 a las 8am</h2>. No pierda esta importante oportunidad.
                    </p>

                    <p>
                        ¿Por qué es importante acudir a estas actividades de matriculación?
                        <ul>
                            <li>Asegura el registro de su interés en nuestra institución con el fin de optar a un cupo escolar en nuestra institución.</li>
                            <li>Nos permite conocer la demanda real de matrícula.</li>
                            <li>Contribuye a la planificación estratégica de la institución para el próximo año escolar.</li>
                        </ul>
                    </p>

                    <div>
                        <p>Sus datos registrados:</p>
                        
                        <ul>
                            <li style="background-color: darkgray">Información del Representante</li>
                            <li><b>Nombre:</b> {{$catchment->full_name_representant ?? null}}</li>
                            <li><b>CI:</b> {{$catchment->representant_ci ?? null}}</li>
                            <li><b>Correo electrónico:</b> {{$catchment->email ?? null}}</li>
                        </ul>
                        
                    </div>

                    <hr>

                @endif

                <hr>

                <strong>Dé el primer paso hacia un futuro brillante para su representdo.</strong>

                <strong>Agradecemos su interés en nuestra institución.</strong>

                <hr>

                <p style="text-align: center;">Atte.</p>
                <div style="text-align: center;">
                    <div>{{$autoridad->profile_professional}} {{$autoridad->fullname}}</div>
                    <div>{{$autoridad->position}}</div>
                </div>
                <hr>
                <p>
                <div>{{$director->profile_professional}} {{$director->fullname}}</div>
                <div>{{$director->position}}</div>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<hr>

<footer class="text-muted" style="font-size:0.8rem">
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>
