@php
    $poll_main = $poll_token->poll_main;
    $poll_questions = $poll_main->poll_questions;
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                {{-- <img width="70px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/uecfla.jpg"> --}}
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                {{-- <img width="100px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/amigoniano.png"> --}}
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

                @if ($poll_token)
                    <h1>Ticket de participación en la consulta</h1>
                    <h3>{!!$poll_main->name ?? null!!} {!!$poll_main->description ?? null!!}</h3>

                    <hr>

                    @switch($attendee->rol)
                        @case('ESTUDIANTE')
                            <p style="text-align: justify;">
                                Estimado(a) estudiante de nuestro colegio.&nbsp;<em><strong>{{$attendee->fullname}}</strong>, CI; {{$attendee->ci}},</em>
                            </p>
                        @break
                        @default
                            <p style="text-align: justify;">
                                Estimado(a) Sr(a).&nbsp;<em><strong>{{$attendee->fullname}}</strong>, CI; {{$attendee->ci}},</em>
                            </p>                        
                    @endswitch

                    @php $dir_address = $attendee->dir_address;  @endphp
                    @if ($dir_address)
                        <p style="text-align: justify;">
                            Domiciliado(a) en: <em><strong>{!!$dir_address!!}</strong></em>
                        </p>
                    @endif                    

                    @switch($attendee->rol)
                        @case('REPRESENTANTE')
                            @php $representant = $attendee->representant @endphp
                            <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                                <span style="margin-bottom: 0rem">Representante de:</span>
                                <div style="padding-left: 1rem; margin-top: 0rem">
                                    @foreach ($representant->estudiants_formaly as $estudiant)
                                        <div style="font-weight: bold">{{$estudiant->fullname}} || {{$estudiant->fullinscripcion}}</div>
                                    @endforeach
                                </div>
                            </div>
                            @break
                        @case('PROFESOR')
                            @php $profesor = $attendee->profesor @endphp
                            <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                                <span style="margin-bottom: 0rem">Docente de la institución con la siguiente carga académica registrada en el <b>{{env('APP_NAME')}}</b>:</span>
                                <div style="padding-left: 1rem; margin-top: 0rem">
                                    @foreach ($profesor->carga_academica as $pensum)
                                        <div style="font-weight: bold">{{$pensum->asignatura->name}}</div>
                                    @endforeach
                                </div>
                            </div>
                            @break
                        @default
                            <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                                <span style="margin-bottom: 0rem">Con el siguiente rol en el <b>{{env('APP_NAME')}}</b>:</span>
                                <div style="padding-left: 1rem; margin-top: 0rem">
                                    Área: {{$attendee->area}} || Rol: {{$attendee->rol}}
                                </div>
                            </div>
                    @endswitch

                    <hr>

                    <span class="font-weight-bold p-2 text-success">Su información fue procesada y registrada exitosamente.</span>

                    @php
                        $ci = $attendee->ci; $fullname = $attendee->fullname;
                    @endphp

                    @if ($poll_token)
                        @include('email.polls.partials.ticket')
                    @else
                    <div>No tiene token</div>
                    @endif

                    @switch($attendee->rol)
                        @case('REPRESENTANTE')
                            @if ($poll_main->status_estudiant=='true' && $attendee->isRepresentant())
                                @php
                                    $representant = $attendee->representant;
                                    $estudiants = ($representant) ? $representant->estudiants_formaly : collect();
                                @endphp
                                @foreach ($estudiants as $estudiant)
                                    @php
                                        $user = $estudiant->user;
                                        $poll_token = $user->getPollTokenId($poll_main->id);
                                        $competitor = clone $attendee;
                                        $competitor->ci = $estudiant->ci_estudiant;
                                        $competitor->fullname = $estudiant->fullname;
                                        $ci = $estudiant->ci_estudiant;
                                        $fullname = $estudiant->fullname;
                                    @endphp
                                    <hr>
                                    <div style="margin-right: 2rem; margin-left: 2rem;border: 1px solid #ccc;border-radius: 1rem;padding: 1rem;" style="">
                                        <h6>{{$estudiant->fullname}} - {{$estudiant->ci_estudiant}}</h6>
                                        @include('email.polls.partials.ticket')
                                    </div>
                                @endforeach

                            @endif

                        @break
                    @endswitch

                @endif

                <hr>

                <p style="text-align: center;">Atte.</p>
                @php $autoridad = $poll_main->autoridad; @endphp
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
