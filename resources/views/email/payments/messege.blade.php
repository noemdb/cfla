@php
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);
    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
    $estudiants = $representant->estudiants_formaly;
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
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

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate}}</p>

                <h1>Reporte de Pago</h1>
                <h3>Realizado a través del portal web.</h3>

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
                        @if (!empty($estudiants))
                            @foreach ($estudiants as $estudiant)
                                <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <hr>

                <p style="text-align: justify;">
                    @include('email.payments.partials.data')
                </p>

                <hr>

                <h5 style="color: red"><b>Tenga en cuenta lo siguiente</b></h5>

                <p style="text-align: justify; color:red">
                -. Los reportes realizados en nuestro portal web, son concialidos en dos o tres días hábiles.
                </p>

                <p style="text-align: justify; color:red">
                -. <b>ESTE CORREO ELECTRÓNICO ES GENERADO DE FORMA AUTOMATIZADA, NO DEBE SER USADO PARA ENVIAR IMÁGENES DE TRANSFERENCIAS Y/O PAGOS MÓVILES.</b> Los reportes de pago se hacen única y exclusivamente por nuestro portal web <b>uefrayluisamigosf.com</b>
                </p>

                <p style="text-align: justify; color:red">
                -. El único objetivo de este correo electrónico es dejar constancia del registro exitoso de su reporte de pago.
                </p>

                <hr>

                <p style="text-align: center;">Atte.</p>
                <p style="text-align: center;">DIRECCIÓN DE ADMINISTRACIÓN</p>
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
        Teléfonos: 0424-5891682 - 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>