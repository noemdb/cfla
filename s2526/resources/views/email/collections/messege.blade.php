<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/uecfla.jpg ">
            </th>
            <th>
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE ADMINISTRACIÓN</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="https://uefrayluisamigosf.com/s2425/images/avatar/amigoniano.png ">
            </th>
        </tr>
    </thead>
</table>

<hr>

<table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0" cell>
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate}}</p>

                @if ($coll_message)
                    <h1>{!!$coll_message->title ?? null!!}</h1>
                    <h3>{!!$coll_message->subtitle ?? null!!}</h3>

                    <!-- NUEVO TEXTO AGREGADO -->
                    <h3 style="text-align: justify; color:red">
                        <b>Acudir al departamento de administración en un lapso de 48 horas de haber recibido este correo.</b>
                    </h3>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$representant->name}}</strong>, CI; {{$representant->ci_representant}},</em>
                    </p>

                    @php $address = ($representant) ? $representant->address : null; @endphp
                    @if ($address)
                        <p style="text-align: justify;">
                            Domiciliado en: <em><strong>{!!$address!!}</strong></em>
                        </p>
                    @endif

                    <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                        <span style="margin-bottom: 0rem">Representante de:</span>
                        <div style="padding-left: 1rem; margin-top: 0rem">
                            @php $estudiants = $representant->estudiants_formaly @endphp
                            {{-- @php $estudiants = $representant->estudiants @endphp --}}
                            @foreach ($estudiants as $estudiant)
                                {{-- @if ($estudiant->exchange_ammount_expire_bill > 0) --}}
                                    <div style="font-weight: bold">{{$estudiant->fullname}}</div>
                                {{-- @endif --}}
                            @endforeach
                        </div>
                    </div>

                    <hr>

                    @include('email.collections.partials.bills')

                    <p style="text-align: justify;">
                        {!!$coll_message->greeting ?? null!!}
                    </p>

                    @if ($coll_message->consider)
                        <p style="text-align: justify;">
                            {!!$coll_message->consider ?? null!!}
                        </p>
                    @endif

                    <hr>

                    

                    <h5 style="color: red"><b>Tenga en cuenta lo siguiente</b></h5>

                    <p style="text-align: justify; color:red">
                    -. Los pagos realizados por transferencia y/o pago móvil, deben ser reportados en nuestro portal web <b>uefrayluisamigosf.com</b>.
                    </p>

                    <p style="text-align: justify; color:red">
                    -. <b>ESTE CORREO ELECTRÓNICO ES GENERADO DE FORMA AUTOMATIZADA, NO DEBE SER USADO PARA ENVIAR IMÁGENES DE TRANSFERENCIAS Y/O PAGOS MÓVILES.</b> Los reportes de pago se hacen única y exclusivamente por nuestro portal web <b>uefrayluisamigosf.com</b>
                    </p>

                    <p style="text-align: justify; color:black">
                        Enlace directo para el reporte de pagos: <a href="https://uefrayluisamigosf.com/reporte  ">Aquí</a> 
                    </p>

                    <p style="text-align: justify; color:red">
                    {{-- -. El único objetivo de este correo electrónico es realizar un recordatorio de la(s) deuda(s) y compromiso(s) de pago(s). --}}
                    </p>

                    <hr>

                    <p style="text-align: justify;">
                        {!!$coll_message->sentence ?? null!!}
                    </p>

                    @if ($coll_message->waiting)
                        <p style="text-align: justify;">
                            {!!$coll_message->waiting ?? null!!}
                        </p>
                    @endif
                    <p style="text-align: justify;">
                        {!! $coll_message->footer ?? null!!}
                    </p>
                @endif

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
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>