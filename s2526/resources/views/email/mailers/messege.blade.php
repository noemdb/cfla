{{-- 'user_id','name','code','description','seccion_id','grado_id','fecha', 'subject','title','subtitle','greeting','body','footer',  'status','status_adviders', --}}

@php
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);
    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
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

<table  cellpadding="2" cellspacing="2"  style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($mailer)
                    <h1>{!!$mailer->title ?? null!!}</h1>
                    <h5>{!!$mailer->subtitle ?? null!!}</h5>

                    <hr>

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
                            @foreach ($representant->estudiants_formaly as $estudiant)
                                <div style="font-weight: bold">

{{$estudiant->fullname}} 

{{--
|| {{$estudiant->fullinscripcion}}
--}}


</div>
                            @endforeach
                        </div>
                    </div>

                    @if ($mailer->greeting)
                        <hr>
                        <p style="text-align: justify;">
                            {!!$mailer->greeting ?? null!!}
                        </p>
                    @endif

                    @if ($mailer->body)
                        <hr>
                        <p style="text-align: justify;">
                            {!!$mailer->body ?? null!!}
                        </p>
                    @endif

                    @if ($mailer->insert)
                        <hr>
                        <p style="text-align: justify;">
                            {!!$mailer->insert ?? null!!}
                        </p>
                    @endif

                    

                    @if ($mailer->footer)
                        <hr>
                        <p style="text-align: justify;">
                            {!!$mailer->footer ?? null!!}
                        </p>
                    @endif

                    <hr>

                    <p style="text-align: center;">Atte.</p>
                    <p style="text-align: center;">
                        <span>{{$mailer->atte ?? null}}</span>
                    </p>

                @endif
                
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
