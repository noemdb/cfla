@php
    $estudiant = $incident->estudiant;
    $profesor = $incident->profesor;
    $profesor_guia = $incident->profesor_guia;
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
                <h3>Incidencia registrada.</h3>

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
                        @foreach ($estudiants as $item)
                            <div style="font-weight: bold">{{$item->fullname}}</div>
                        @endforeach
                    </div>
                </div>

                <hr>

                <p style="text-align: justify;">
                    Se le notifica que la <span style="font-weight: bold;">Coordinación de Bienestar Estudiantil</span> y el docente guía [{{ ($profesor_guia) ? $profesor_guia->fullname : null}}], 
                    han registrado una incidencia realcionada al estudiante <span style="font-weight: bold;">{{$estudiant->fullname ?? null}}</span>, la cuál contiene los siguiente detalles:
                </p>

                <ul style="text-transform: uppercase;">
                    <li><span style="font-weight: bold">Código</span>: BE-I{{ $incident->id ?? null}}</li>
                    <li><span style="font-weight: bold">Docente</span>: {{$profesor->fullname ?? null}}</li>
                    <li><span style="font-weight: bold">Deber</span>: {{$incident->duty ?? null}}</li>
                    <li><span style="font-weight: bold">Fecha</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
                    <li><span style="font-weight: bold">Se convoca al representante</span>: {{ ($incident->status_announcement) ? 'SI':'NO'}} </li>
                </ul>

                <hr>

                <p style="text-align: justify; color:red;font-weight: bold;">
                El único objetivo de este correo electrónico es enviar una notificación del registro de una incidencia (Académica o disciplinaria) realcionada a nuestra comunidad estudiantil.
                </p>

                @if ($incident->status_announcement)
                    <hr>
                    <p style="text-align: justify; color:orange; font-weight: bold;" style="font-">
                        Se solicita una entrevista con el representante.
                        @if ( $incident->time_announcement)
                            <div style="margin-left:1rem">
                                <span>Para la fecha: {{ ($incident->time_announcement) ? $incident->time_announcement->format('d-m-Y h:i a') : null}}</span>
                            </div>
                        @endif
                    </p>
                @endif

                <hr>

                <p style="text-align: center;">Atte.</p>
                @php $profesor = $estudiant->profesor_guia;  @endphp
                <p style="text-align: center;">PROF: {{$profesor->fullname ?? null}}<br>CI:{{$profesor->ci_profesor ?? null}}</p>
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
                {{-- <p>
                    <div>{{$autoridad3->profile_professional}} {{$autoridad3->fullname}}</div>
                    <div>{{$autoridad3->position}}</div>
                </p> --}}
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
