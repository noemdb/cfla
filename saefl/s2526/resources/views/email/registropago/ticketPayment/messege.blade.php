@php $code = '<b>[ <span style="color: #ff0000"> RB-P-'.$registro_pago_combinado->id.' </span> ] </b>'; @endphp

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

                    <div style="text-align: right;">
                        @php $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2) @endphp
                        @if ($exchange_ammount_expire_bill <= 0)
                            <div style="text-align: right;">
                                <div style="color: green; font-weight: bold"><strong>SOLVENTE</strong></div>
                            </div>
                            <hr>
                        @else
                            <div>Para la fecha usted tiene una deuda de: <b style="font-size:1.8rem !important;color:red">$ {{f_float($exchange_ammount_expire_bill)}}</b></div>
                        @endif
                    </div>

                    <p style="text-align: justify;">
                        Adjunto a este correo electrónico se encuentra el recibo de pago asociado al número de facturación: {{$registro_pago_combinado->id}}.
                    </p>

                    {{-- <div>
                        @include('administracion.representants.recibo.mail')
                    </div> --}}

                    <p style="text-align: justify;">
                    -. Sin mas a que hacer referencia.
                    </p>

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

{{-- <a target="_blank" title="Recibo de Pago" class="btn btn-dark btn-sm {{ ($registro_pago_combinado->status_irregular) ? 'disabled': null}}" href="{{ route('administracion.representants.recibo.pdf',$registro_pago_combinado->id) }}">
    <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i> haga clic aquí para descargar el recivo
</a> --}}

<hr>

<footer class="text-muted" style="font-size:0.8rem">
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>
