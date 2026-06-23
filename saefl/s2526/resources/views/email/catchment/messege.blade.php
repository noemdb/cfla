<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
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

<table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($catchment)
                    <h1>Ticket de registro inicial, Fase 1</h1>
                    <h3>Proceso de Matriculación Escolar</h3>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment->full_name_representant}}</strong>, CI; {{$catchment->representant_ci}},</em>
                    </p>                    

                    <h1>¡Bienvenido(a) al proceso de Matriculación Escolar 2024-2025!</h1>

                    <h2>Este proceso comprende dos fases: <br><b>Fase 1:</b> es la que has completado al recibir este correo electrónico. <br><b>Fase 2:</b> accedes a ella a través del enlace que se encuentra más abajo.</h2>

                    <hr>

                    <p>La educación es el motor del cambio, la llave que abre las puertas del futuro. Por eso, la matrícula escolar es un proceso fundamental para garantizar que todos los niños y niñas tengan la oportunidad de acceder a una educación de calidad.</p>

                    <p>Cuando un niño o niña se matricula en una escuela, está dando un paso importante en su vida. Está empezando un viaje de aprendizaje que le permitirá desarrollarse como persona y ciudadano.</p>

                    <p>La matrícula escolar también es importante para las familias. Es la oportunidad de elegir la escuela que mejor se adapte a las necesidades de sus hijos e hijas.</p>

                    <p>En nuestra institución, estamos comprometidos con la educación de calidad para todos. Por eso, automatizamos el proceso de matrícula escolar para hacerlo más sencillo, eficiente y transparente.</p>

                    <p>La automatización del proceso de matrícula escolar tiene las siguientes ventajas:</p>

                    <ul>

                    <li>Es más sencillo y rápido para las familias.</li>

                    <li>Reduce el riesgo de errores.</li>

                    <li>Es más eficiente para la institución educativa.</li>

                    <li>Permite recopilar datos de forma más precisa.</li>

                    </ul>

                    <p>Esperamos que usted y su familia encuentren en nuestra institución el lugar ideal para que su hijo o hija reciba una educación de calidad.</p>


                    @if (! empty($catchment->token))
                        <hr>
                        <p>
                            <h3>A continuación, encontrarás el enlace que te llevará al formulario de registro para este proceso de matriculación escolar. </h3>
                            <div style="text-align: center">
                                <div>
                                    Una vez finalizado ese registro, se compromete a participar en las actividades que la institución planifique para este proceso.
                                </div>
                                @php $url = 'https://uefrayluisamigosf.com/s2425/general/catchments/register/'.$catchment->token @endphp
                                <h1><a href="{{$url}}" target="_BLANK">Aquí</a><h1>
                            </div>

                            <div style="text-align: center; font-size: 0.6rem !important">
                                <div>Sí no puede acceder con el enlace de arriba, copie el siguiente texto y peguelo en la barra de direcciones de su navegador</div>
                                <div>{{$url}}</div>
                            </div>
                        </p>
                    @endif

                @endif

                <hr>

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
