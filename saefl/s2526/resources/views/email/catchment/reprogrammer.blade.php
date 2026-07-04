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
                        <div> Dirección: {{$catchment->direction}} </div>
                        <div> Representante de: {{$catchment->firstname}} {{$catchment->lastname}}</div>
                    </p>

                    <hr>

                    <p style="font-size: 0.8rem;">
                        Sabemos de las múltiples ocupaciones que pudieran originar el olvido involuntario a este respecto,
                        es por esta razón y a fin de garantizar que el censo se lleve a cabo de manera completa y satisfactoria
                        que reorientamos las actividades según lo sigueinte: <br> 
                        <ul>
                            <li>-. De 1er Grado a 4to Año [con excepción de 1er y 2do Año]: <strong style="font-size: 1rem;">21/06/2024</strong>.</li>
                            <li>-. Todos los niveles de <strong>Educación Inicial</strong>: <strong style="font-size: 1rem;">29/05/2024 y 21/06/2024</strong>.</li>
                        </ul>
                    </p>

                    <div>
                        <p>Sus datos registrados:</p>
                        
                        <ul>
                            <li style="background-color: darkgray">Información del Representante</li>
                            <li><b>Nombre:</b> {{$catchment->full_name_representant ?? null}}</li>
                            <li><b>CI:</b> {{$catchment->representant_ci ?? null}}</li>
                            <li><b>Correo electrónico:</b> {{$catchment->email ?? null}}</li>
                            <li><b>Parentesco con el estudiante:</b> {{$catchment->relationship ?? null}}</li>
                        </ul>

                        <ul>
                            <li style="background-color: darkgray">Información General del estudiante</li>
                            <li><b>Nombre:</b> {{$catchment->firstname}} {{$catchment->lastname}}</li>
                            <li><b>Grado:</b> {{$catchment->grado->name ?? null}}</li>
                            <li><b>Fecha de nacimiento:</b> {{ f_date($catchment->date_birth) }}</li>
                        </ul>
                        
                    </div>

                    <hr>

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
