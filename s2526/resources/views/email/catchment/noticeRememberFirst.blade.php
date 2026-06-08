@php
    $catchment_group = $catchment->catchment_group;
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
                    {{-- <h1>Entrevista.</h1> --}}
                    {{-- <h3>Proceso de Matriculación Escolar</h3> --}}

                    <h3>¡Bienvenido(a) al proceso de Matriculación Escolar 2026-2027!</h3>

                    <hr>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment->full_name_representant}}</strong>, CI; {{$catchment->representant_ci}},</em>
                        <div> Representante de: {{$catchment->firstname}} {{$catchment->lastname}}</div>
                    </p>

                    <div>
                        <p>Sus datos registrados:</p>
                        
                        <ul>
                            <li style="background-color: darkgray">Información del Representante</li>
                            <li><b>Nombre:</b> {{$catchment->full_name_representant ?? null}}</li>
                            <li><b>CI:</b> {{$catchment->representant_ci ?? null}}</li>
                            <li><b>Correo electrónico:</b> {{$catchment->email ?? null}}</li>
                            {{-- <li><b>Parentesco con el estudiante:</b> {{$catchment->relationship ?? null}}</li> --}}
                        </ul>

                        <ul>
                            <li style="background-color: darkgray">Información General del estudiante</li>
                            <li><b>Nombre:</b> {{$catchment->firstname}} {{$catchment->lastname}}</li>
                            <li><b>Grado:</b> {{$catchment->grado->name ?? null}}</li>
                            <li><b>Fecha de nacimiento:</b> {{ f_date($catchment->date_birth) }}</li>
                        </ul>
                        
                    </div>                    

                    <p>
                        En nombre del <strong>{{$institucion->name ?? null}}</strong>, les damos la bienvenida al proceso de matrícula escolar 2026-2027. 
                        Nos complace poder ofrecerles a sus hijos la oportunidad de formar parte de nuestra comunidad educativa,
                        que está comprometida con la excelencia académica y el desarrollo integral de los estudiantes.
                    </p>

                    <p>
                        Es un gusto saludarle. Le informamos que la entrevista previamente pautada para hoy ha sido reprogramada para el día lunes 7 de abril a las 2:00 pm. Su presencia es muy importante para nosotros, ya que nos permite fortalecer el trabajo en equipo por la educación de su representado/a.
                    </p>                    

                    <p>
                        Además, le hacemos un llamado a considerar el código de vestimenta descrito en los Acuerdos de Convivencia al asistir al plantel. Esto nos ayuda a mantener un ambiente de respeto y armonía. A continuación, le compartimos algunas indicaciones clave:  
                        <ul>
                            <li>
                                <strong>Para damas</strong>: Evitar licras, shorts, minifaldas, strapless o prendas con escotes pronunciados.  
                            </li>
                            <li>
                                <strong>Para caballeros</strong>: Pantalón formal y camisa/camiseta adecuada (sin tirantes o prendas rotas).
                            </li>
                        </ul>

                        <div>
                            Como toda institución, mantenemos dentro de nuestros valores el código de vestimenta para ingresar en cualquier momento a nuestro colegio. 
                            Por lo tanto, agradecemos encarecidamente evitar el uso de pantalones rotos, transparencias, blusas escotadas o muy cortas, shorts, monos, leggings y minifaldas.
                        </div>

                    </p>

                @endif

                <hr>

                <p>
                    Sabemos que sus hijos son lo más importante para ustedes, y queremos asegurarnos de que reciban la mejor educación posible. 
                    Estamos comprometidos a trabajar con ustedes para que sus hijos tengan éxito en nuestra institución.
                </p>

                <hr>

                <div><strong>Agradecemos su interés en nuestra institución.</strong></div>  
                
                <p>
                    <div>
                        Para esta primera entrevista no es necesario presentar documentación. Le informaremos posteriormente si se requiere alguna documentación adicional.
                    </div>

                    <hr>

                    <div>
                        <h4 style="color: red">Importante, Educación Inicial:</h4>
                        <div>Consideraciones para la edad.</div>

                        <ul>
                            <li><strong>1er Nivel/Año de Preescolar</strong>: Nacidos en el 2022, próximo a cumplir los 3 años hasta noviembre de 2025.</li>
                            <li><strong>2do Nivel/Año de preescolar</strong>: Nacidos en el 2021, 4 años ó próximo a cumplirlos hasta noviembre 2025.</li>
                            <li><strong>3er Nivel/Año de Preescolar</strong>: Nacidos en el 2020, 5 años ó próximo a cumpliros hasta noviembre 2025.</li>
                        </ul>

                    </div>
                    
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
