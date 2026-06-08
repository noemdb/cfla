<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                {{-- <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}"> --}}
                <img width="70px" height="70px" class="card-img-top"
                    src="https://uefrayluisamigosf.com/s2425/images/avatar/uecfla.jpg">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top"
                    src="https://uefrayluisamigosf.com/s2425/images/avatar/amigoniano.png">
                {{-- <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}"> --}}
            </th>
        </tr>
    </thead>
</table>

<hr>

<table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{ $toDate ?? null }}</p>

                @switch($attendee->rol)
                    @case('ESTUDIANTE')
                        <p style="text-align: justify;">
                            Estimado(a) estudiante de nuestro colegio.&nbsp;<em><strong>{{ $attendee->fullname }}</strong>, CI;
                                {{ $attendee->ci }},</em>
                        </p>
                    @break

                    @default
                        <p style="text-align: justify;">
                            Estimado(a) Sr(a).&nbsp;<em><strong>{{ $attendee->fullname }}</strong>, CI;
                                {{ $attendee->ci }},</em>
                        </p>
                @endswitch

                @php $dir_address = $attendee->dir_address;  @endphp
                @if ($dir_address)
                    <p style="text-align: justify;">
                        Domiciliado(a) en: <em><strong>{!! $dir_address !!}</strong></em>
                    </p>
                @endif

                @switch($attendee->rol)
                    @case('REPRESENTANTE')
                        @php $representant = $attendee->representant @endphp
                        <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                            <span style="margin-bottom: 0rem">Representante de:</span>
                            <div style="padding-left: 1rem; margin-top: 0rem">
                                @foreach ($representant->estudiants_formaly as $estudiant)
                                    <div style="font-weight: bold">{{ $estudiant->fullname }} ||
                                        {{ $estudiant->fullinscripcion }}</div>
                                @endforeach
                            </div>
                        </div>
                    @break

                    @case('PROFESOR')
                        @php $profesor = $attendee->profesor @endphp
                        <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                            <span style="margin-bottom: 0rem">Docente de la institución con la siguiente carga académica
                                registrada en el <b>{{ env('APP_NAME') }}</b>:</span>
                            <div style="padding-left: 1rem; margin-top: 0rem">
                                @foreach ($profesor->carga_academica as $pensum)
                                    <div style="font-weight: bold">{{ $pensum->asignatura->name }}</div>
                                @endforeach
                            </div>
                        </div>
                    @break

                    @default
                        <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                            <span style="margin-bottom: 0rem">Con el siguiente rol en el <b>{{ env('APP_NAME') }}</b>:</span>
                            <div style="padding-left: 1rem; margin-top: 0rem">
                                Área: {{ $attendee->area }} || Rol: {{ $attendee->rol }}
                            </div>
                        </div>
                @endswitch

                <hr>

                <h1>
                    Se le invita a participar en el siguiente proceso de consultas:
                </h1>
                <h3>{!! $poll_main->name ?? null !!} </h3>
                <p> {!! $poll_main->description ?? null !!} </p>
                <ul>
                    {{-- <li> <span> Fecha de Inicio: </span> <span>{!! $poll_main->start->format('d-m-Y g:i A') ?? null!!}</span> </li> --}}
                    <li> <span> Fecha de Finalización: </span> <span>{!! $poll_main->end->format('d-m-Y g:i A') ?? null !!}</span> </li>
                    @if ($poll_main->observations)
                        <li> <span> Observaciones: </span> <span>{!! $poll_main->observations ?? null !!}</span> </li>
                    @endif
                </ul>

                {{-- {{$poll_questions}} --}}


                @if ($poll_questions->isNotEmpty())
                    <hr>

                    <div class="" id="ticketRegister" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                        aria-hidden="true">
                        <div class="" role="document">
                            <div class="modal-content">
                                <div style="margin-left: 1rem">
                                    <p>Preguntas:</p>
                                    <ul class="px-2">
                                        @foreach ($poll_questions as $poll_question)
                                            @php $poll_options = $poll_question->poll_options; @endphp
                                            <li>
                                                <div style="font-size: 0.8rem">{{ $poll_question->text }}: </div>
                                                {{-- @if ($poll_question->body) <hr><div>{{ $poll_question->body ?? null}}</div> @endif --}}
                                                {{-- @if ($poll_question->observations) <hr><div>{{ $poll_question->observations ?? null}}</div> @endif --}}
                                                <div style="margin-left: 1rem;">
                                                    <div><b>Opciones:<b></div>
                                                    <ul>
                                                        @foreach ($poll_options as $poll_option)
                                                            <li style="padding-bottom: 20px;">
                                                                <div style="font-weight: bold;">
                                                                    <div>{{ $poll_option->text }}</div>
                                                                    <i style="font-weight: normal;">{{ $poll_option->description }}<i>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <h4 style="color:red">No se han generado las opciones de participación</h4>
                @endif

                @if (!empty($poll_token->token))
                    <div style="margin: 20px 0; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;">
                        <h3 style="margin-top: 0; color: #2d3748; font-size: 16px; line-height: 1.5; text-align: center;">
                            Haga clic en el siguiente enlace para acceder a su ticket único de participación:
                        </h3>
                        
                        <div style="text-align: center; margin: 25px 0;">
                            @php $url = url('/general/polls/' . $poll_token->token); @endphp
                            <a href="{{ $url }}" target="_blank" 
                               style="background-color: #2c5282; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 18px; display: inline-block;">
                                Acceder al Ticket de Participación
                            </a>
                        </div>

                        <div style="text-align: center; font-size: 11px; color: #718096; line-height: 1.4;">
                            <p style="margin: 0 0 8px 0;">Si el botón de arriba no funciona, copie y pegue la siguiente URL en su navegador:</p>
                            <a href="{{ $url }}" style="color: #2c5282; word-break: break-all;">{{ $url }}</a>
                        </div>
                    </div>
                @else
                    <h5 style="color:red; margin-top: 1rem;">No se han generado los tokens de participación</h5>
                @endif
 

                @if ($poll_main->status_estudiant == 'true' && $attendee->isRepresentant())
                    @php
                        $representant = $attendee->representant;
                        $estudiants = $representant->estudiants_formaly;
                    @endphp
                    @if ($estudiants->isNotEmpty())
                        <div style="border: 1px solid #ccc; padding: 1rem;border-radius:1rem;">
                            <h5>A continuación, se proporcionan los enlaces correspondientes a los tickets de
                                participación para su(s) representado(s):</h5>
                            @foreach ($estudiants as $estudiant)
                                @php
                                    $user = $estudiant->user;
                                    $poll_token = $user->getPollTokenId($poll_main->id);
                                    $token = $poll_token ? $poll_token->token : null;
                                    $url = env('APP_URL') . '/general/polls/' . $token;
                                @endphp

                                @if ($token)
                                    <div style="text-align: left">
                                        <div>
                                            {{-- <span>{{$user->id}}</span> --}}
                                            <span>{{ $loop->iteration ?? null }} {{ $estudiant->fullname }}</span>
                                            <span><a href="{{ $url }}" target="_BLANK">Aquí</a></span>
                                        </div>

                                        <div style="text-align: left; font-size: 0.6rem !important">
                                            <div>Sí no puede acceder con el enlace de arriba, copie el siguiente texto y
                                                peguelo en la barra de direcciones de su navegador</div>
                                            <div>{{ $url }}</div>
                                        </div>
                                    </div>
                                    <hr>
                                @else
                                    <h6 style="color:red">No se han generado los tokens de participación</h6>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

                <hr>

                <p style="text-align: center;">Atte.</p>
                @php $autoridad = $poll_main->autoridad; @endphp
                <div style="text-align: center;">
                    <div>{{ $autoridad->profile_professional }} {{ $autoridad->fullname }}</div>
                    <div>{{ $autoridad->position }}</div>
                </div>
                <hr>
                <p>
                <div>{{ $director->profile_professional }} {{ $director->fullname }}</div>
                <div>{{ $director->position }}</div>
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
