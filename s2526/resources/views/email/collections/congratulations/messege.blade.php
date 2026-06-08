

@php
$code = '<b>[ <span style="color: #ff0000"> SD.CGR.REP </span> ] </b>';
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/uecfla.jpg">
            </th>
            <th>
                <div class="title"><b>{{ $institucion->name ?? null}}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/amigoniano.png">
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

                    <h1>{!! $subject ?? null !!}</h1>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name ?? null}}</strong>, CI; {{$representant->ci_representant ?? null}},</em>
                    </p>

                    @php $address = ($representant) ? $representant->address : null;  @endphp
                    @if ($address)
                        <p style="text-align: justify;">
                            Domiciliado en: <em><strong>{!!$address ?? null!!}</strong></em>
                        </p>
                    @endif

                    <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                        <span style="margin-bottom: 0rem">Representante de:</span>
                        <div style="padding-left: 1rem; margin-top: 0rem">
                            @php $estudiants = $representant->estudiants_formaly @endphp
                            @foreach ($estudiants as $estudiant)
                                <div style="font-weight: bold">{{$estudiant->fullname ?? null}}</div>
                            @endforeach
                        </div>
                    </div>

                    <hr>

                    @php $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill @endphp
                    @if ($exchange_ammount_expire_bill <= 0)
                        <div style="text-align: right;">
                            <div style="color: green; font-weight: bold"><strong>SOLVENTE</strong></div>
                        </div>
                        <hr>
                    @endif

                    <p style="text-align: justify;">
                        Estimado representante, <b>ESTAMOS AGRADECIDOS POR SU PUNTUALIDAD</b> con los compromisos administrativos de nuestra Institución, su apoyo nos ayuda a seguir avanzando en el mejoramiento del proceso enseñanza-aprendizaje de nuestros educandos.
                    </p>

                    <p style="text-align: justify;">
                        Somos aliados en esta noble tarea de formar una generación con espíritu de grandeza y superación.
                    </p>

                    {{-- <p style="text-align: justify; color:#ccc">
                    -. El único objetivo de este correo electrónico es dar reconocimiento a los representantes que cumplen oportunamente estos compromisos.
                    </p> --}}

                    <hr>

                {{-- <p style="text-align: justify;"></p> --}}
                <p style="text-align: center;">Atte.</p>
                <p style="text-align: center;">DIRECCIÓN DE ADMINISTRACIÓN</p>
                <hr>
                <h4>Directivos de la Institución</h4>
                <p>
                    <div>{{$autoridad1->profile_professional}} {{$autoridad1->fullname ?? null}}</div>
                    <div>{{$autoridad1->position ?? null}}</div>
                </p>
                <p>
                    <div>{{$autoridad2->profile_professional ?? null}} {{$autoridad2->fullname ?? null}}</div>
                    <div>{{$autoridad2->position ?? null}}</div>
                </p>
                <div align="right"> {!! $code ?? null !!} </div>
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
