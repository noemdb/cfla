@php
    $estudiant = $incident->estudiant;
    $profesor = $incident->profesor;
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="https://sae.uefrayluisamigosf.com/images/avatar/uecfla.jpg">
            </th>
            <th>
                <div align="center" style="align-content: center"><b>{{ $institucion->name }}</b></div>
                <div align="center" style="align-content: center"><b>DIRECCIÓN DE ACADÉMICA</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="https://sae.uefrayluisamigosf.com/images/avatar/amigoniano.png">
            </th>
        </tr>
    </thead>
</table>

<hr>

<table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate}}</p>

                <h1>Notificación</h1>
                <h3>Acuerdos registrados.</h3>

                <p style="text-align: justify;">
                    Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name}}</strong>, CI; {{$representant->ci_representant}},</em>
                </p>

                @php $address = ($representant) ? $representant->address : null;  @endphp
                @if ($address)
                    <p style="text-align: justify;">
                        Domiciliado en: <em><strong>{!!$address!!}</strong></em>
                    </p>
                @endif

                <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                    <span style="margin-bottom: 0rem">Representante de:</span>
                    <div style="padding-left: 1rem; margin-top: 0rem">
                        @php $estudiants = $representant->estudiants_formaly @endphp
                        @foreach ($estudiants as $estudiant)
                            <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                        @endforeach
                    </div>
                </div>

                <hr>

                <div style="">
                    <div style="">
                        <p style="text-align: justify;">
                            @php $profesor_guia = $incident->profesor_guia; @endphp
                            Se le notifica que la <span style="font-weight: bold;"> Coordinación de Bienestar Estudiantil</span> y el docente guía [{{ ($profesor_guia) ? $profesor_guia->fullname : null}}],
                            han registrado los acuerdos alcanzados en relación a la incidencia identificada con el código BE-I{{ $incident->id ?? null}}:
                        </p>
                    </div>
                </div>

                <hr>
                <div style="border:#ccc 1px solid;padding:0.5rem;border-radius: 0.2rem">
                    <div style="font-weight: bold;">Quedando establecido de la siguiente manera:</div>

                    <div style="margin-left:0.0rem">


                            @php $incident_agreements = $incident->incident_agreements; @endphp

                            @foreach ($incident_agreements as $incident_agreement)
                                <div style="display: flex;">
                                    <div style="width: 2%; display:flex;align-items: center;justify-content: center;">{{ $loop->iteration}}</div>
                                    <div style="width: 98%;">
                                        <ul style="font-weight: bold;margin-bottom: 0.1rem;">
                                            <li>
                                                <span>Descripción:</span>
                                                <span style="margin-left:0.0rem; font-weight:normal;">{{$incident_agreement->description ?? null}}</span>
                                            </li>

                                            @if ($incident_agreement->observations)
                                                <li>
                                                    <span>Observaciones:</span>
                                                    <span style="margin-left:0.0rem; font-weight:normal;">{{$incident_agreement->observations ?? null}}</span>
                                                </li>
                                            @endif

                                            <li>
                                                <div>Fecha: <span style="font-weight:normal;">{{ ($incident_agreement->created_at) ? $incident_agreement->created_at->format('d-m-Y') : null }}</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                <hr style="margin: 0rem">
                            @endforeach

                    </div>

                <br>

                <div style="margin-left:1rem">
                    <div>Detalles de la Incidencia:</div>
                    <div>
                        <ul style="text-transform: uppercase; font-size:0.6rem">
                            <li><span style="font-weight: bold">Estudiante</span>: {{$estudiant->fullname ?? null}} <span style="color: #ccc">{{$estudiant->ci_estudiant ?? null}}</span></li>
                            <li><span style="font-weight: bold">Docente</span>: {{$profesor->fullname ?? null}}</li>
                            <li><span style="font-weight: bold">Deber</span>: {{$incident->duty ?? null}}</li>
                            <li><span style="font-weight: bold">Falta</span>: {{$incident->fault ?? null}}</li>
                            <li><span style="font-weight: bold">Descripción</span>: {{$incident->description ?? null}}</li>
                            @if ($incident->observations) <li><span style="font-weight: bold">Observaciones</span>: {{$incident->observations ?? null}}</li> @endif
                            <li><span style="font-weight: bold">Acciones tomadas</span>: {{$incident->taken_actions ?? null}}</li>
                            <li><span style="font-weight: bold">Fecha</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
                            <li><span style="font-weight: bold">Registrado por</span>: {{ ($incident->user) ? $incident->user->profile->full_name : null}}</li>
                        </ul>
                    </div>
                </div>

                <hr>

                <p style="text-align: justify; color:red;font-weight: bold;">
                    El único objetivo de este correo electrónico es enviar una notificación del registro de los acuerdos alcanzados en entrevistas previas, en relación a la incidencia antes mencionada.
                </p>

                <hr>

                <p style="text-align: center;">Atte.</p>
                {{-- @php $user = $incident->user;  @endphp --}}
                @php $profesor = $estudiant->profesor_guia;  @endphp
                @php $user = ($profesor) ? $profesor->user : null;  @endphp
                <p style="text-align: center;">{{($profesor) ? $profesor->fullname : null}} <br> {{($profesor) ? $profesor->ci_profesor : null}}</p>
                <hr>
                <h4>Directivos de la Institución</h4>
                <p>
                    <div>{{$autoridad1->profile_professional}} {{$autoridad1->fullname}}</div>
                    <div>{{$autoridad1->position}}</div>
                </p>
                <p>
                    <div>{{$autoridad2->profile_professional}} {{$autoridad2->fullname}}</div>
                    <div>{{$autoridad2->position}}</div>
                </p>
                <p>
                    <div>{{$autoridad3->profile_professional}} {{$autoridad3->fullname}}</div>
                    <div>{{$autoridad3->position}}</div>
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
