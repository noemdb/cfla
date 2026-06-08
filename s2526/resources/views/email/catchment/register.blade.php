@php
    $catchment_group = $catchment->catchment_group;
    $activities = $catchment_group->activities->where('status_active',true);
@endphp

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

<table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0">
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($catchment)
                    <h1>Ticket de registro, Fase 2.</h1>
                    <h3>Proceso de Matriculación Escolar</h3>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment->full_name_representant}}</strong>, CI; {{$catchment->representant_ci}},</em>
                        <div> Dirección: {{$catchment->direction}} </div>
                        <div> Representante de: {{$catchment->firstname}} {{$catchment->lastname}}</div>
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

                    <h1>¡Bienvenido(a) al proceso de Matriculación Escolar 2024-2025!</h1>

                    <p>
                        En nombre del {{$institucion->name ?? null}}, les damos la bienvenida al proceso de matrícula escolar 2024-2025. 
                        Nos complace poder ofrecerles a sus hijos la oportunidad de formar parte de nuestra comunidad educativa,
                        que está comprometida con la excelencia académica y el desarrollo integral de los estudiantes.
                    </p>

                    <p>Sabemos que la decisión de elegir una institución es importante, y por eso queremos que estén informados de todos los pasos que deben seguir para completar el proceso de matrícula.</p>


                    Las actividades correspondientes a su registro son las siguientes: de lunes a viernes desde las 8am hasta las 11am.

                    {{-- 
                    <div>
                        {{ ($activities->count()>1) ? 'Las actividades correspondientes a su registro son las siguientes:' : 'La actividad correspondiente a su registro es la siguiente:'}}
                    </div>
                    @foreach ($activities as $activity)
                        <div>{{$activity->name}}</div>
                        <ul style="margin-left: 1rem">
                            <li>Descripción: {{$activity->description}}</li>
                            <li>Fecha: {{$activity->date}}</li>
                            <li>Hora: {{$activity->time}}</li>
                        </ul>
                        <hr>
                    @endforeach
                    --}}

                @endif

                <p>
                    <strong>Les pedimos que cumplan con las actividades programadas para garantizar que sus hijos puedan comenzar el año escolar en el {{$institucion->name ?? null}}. Los esperamos en nuestra institución en la fecha programada.</strong>
                </p>

                <hr>

                <p>
                    Sabemos que sus hijos son lo más importante para ustedes, y queremos asegurarnos de que reciban la mejor educación posible. 
                    Estamos comprometidos a trabajar con ustedes para que sus hijos tengan éxito en nuestra institución y en la vida.
                </p>

                <hr>

                <p>
                    Como toda institución, mantenemos dentro de nuestros valores el código de vestimenta para ingresar en cualquier momento a nuestro colegio. Por lo tanto, agradecemos encarecidamente evitar el uso de pantalones rotos, transparencias, blusas escotadas o muy cortas, shorts, monos, leggings y minifaldas.
                </p>

                <hr>

                <div><strong>Agradecemos su interés en nuestra institución.</strong></div>  
                
                <p>
                    <div><strong>Requisitos a consignar el día que te hayan asignado:</strong></div>

                    <ul>
                        <li>Boletín o informe descriptivo del I Momento Pedagógico</li>
                        <li>Fotocopia de la partida de nacimiento</li>
                        <li>Fotocopia de la cédula de identidad del representante y del representado (sí la tiene)</li>
                        <li>Solvencia administrativa en caso de venir de una institución privada</li>
                    </ul>

                    <hr>

                    <div>
                        <h4 style="color: red">Importante, Educación Inicial:</h4>
                        <div>Consideraciones para la edad.</div>
                        <ul>
                            <li><strong>1er Nivel/Año de Preescolar</strong>: Nacidos en el 2021, próximo a cumplir los 3 años hasta noviembre de 2024.</li>
                            <li><strong>2do Nivel/Año de preescolar</strong>: Nacidos en el 2020, 4 años ó próximo a cumplirlos hasta noviembre 2024.</li>
                            <li><strong>3er Nivel/Año de Preescolar</strong>: Nacidos en el 2019, 5 años ó próximo a cumpliros hasta noviembre 2024.</li>
                        </ul>
                    </div>
                    
                </p>

                <hr>

                <p>
                    <div><strong>Siguientes actividades:</strong></div>

                    <ul>
                        <li>Envío de un correo electrónico a cada representante aceptado, para formalizar la inscripción de su representado en nuestra institución (Carta Digital de Aceptación)</li>
                        <li>Formalización de la inscripción de los estudiantes aceptados.</li>
                    </ul>
                    
                </p>

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
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: {{ ($institucion) ? $institucion->email_institution : null }}
    </span>
</footer>
