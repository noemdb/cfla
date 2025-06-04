@php
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);
    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
    $estudiants = $representant->estudiants_formaly;
@endphp

<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
    <!-- Header -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
        <tr>
            <td width="80" style="vertical-align: middle;">
                <img width="80" height="80" style="border-radius: 8px;" src="{{asset('images/avatar/uecfla.jpg')}}">
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div style="font-size: 20px; font-weight: bold; color: #2c3e50;">{{ $institucion->name }}</div>
                <div style="font-size: 16px; color: #34495e; margin-top: 5px;">DIRECCIÓN DE ADMINISTRACIÓN</div>
            </td>
            <td width="100" style="vertical-align: middle;">
                <img width="100" height="70" style="border-radius: 8px;" src="{{asset('images/avatar/amigoniano.png')}}">
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">

    <!-- Content -->
    <div style="background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="text-align: right; color: #666; font-size: 14px; margin-bottom: 20px;">
            En San Felipe - Edo. Yaracuy a {{$toDate}}
        </p>

        <h1 style="color: #2c3e50; font-size: 24px; margin-bottom: 10px;">Reporte de Pago</h1>
        <h3 style="color: #34495e; font-size: 18px; margin-bottom: 20px;">Realizado a través del portal web</h3>

        <div style="margin-bottom: 20px;">
            <p style="color: #2c3e50; font-size: 16px; line-height: 1.6;">
                Estimado(a) Sr(a). <strong>{{$representant->name}}</strong>, CI: {{$representant->ci_representant}}
            </p>

            @php $address = ($representant) ? $representant->address : null;  @endphp
            @if ($address)
                <p style="color: #2c3e50; font-size: 16px; line-height: 1.6;">
                    Domiciliado en: <strong>{!!$address!!}</strong>
                </p>
            @endif
        </div>

        <!-- Students Section -->
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <div style="font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Representante de:</div>
            @if (!empty($estudiants))
                @foreach ($estudiants as $estudiant)
                    <div style="color: #34495e; padding: 5px 0;">{{$estudiant->fullname}}</div>
                @endforeach
            @endif
        </div>

        <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">

        <!-- Payment Ticket -->
        <div style="margin: 20px 0;">
            @include('email.payments.partials.data')
        </div>

        <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">

        <!-- Important Notes -->
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <h5 style="color: #856404; margin: 0 0 10px 0;">Tenga en cuenta lo siguiente:</h5>
            <ul style="color: #856404; margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Los reportes realizados en nuestro portal web, son conciliados en dos o tres días hábiles.</li>
                <li style="margin-bottom: 8px;"><strong>ESTE CORREO ELECTRÓNICO ES GENERADO DE FORMA AUTOMATIZADA, NO DEBE SER USADO PARA ENVIAR IMÁGENES DE TRANSFERENCIAS Y/O PAGOS MÓVILES.</strong> Los reportes de pago se hacen única y exclusivamente por nuestro portal web <strong>uefrayluisamigosf.com</strong></li>
                <li>El único objetivo de este correo electrónico es dejar constancia del registro exitoso de su reporte de pago.</li>
            </ul>
        </div>

        <!-- Signatures -->
        <div style="margin-top: 30px;">
            <p style="text-align: center; color: #2c3e50; font-weight: bold;">Atte.</p>
            <p style="text-align: center; color: #2c3e50; font-weight: bold;">DIRECCIÓN DE ADMINISTRACIÓN</p>
        </div>

        <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">

        <!-- Authorities -->
        <div style="margin-top: 20px;">
            <h4 style="color: #2c3e50; margin-bottom: 15px;">Directivos de la Institución</h4>
            <div style="margin-bottom: 15px;">
                <div style="font-weight: bold; color: #34495e;">{{$autoridad1->profile_professional}} {{$autoridad1->fullname}}</div>
                <div style="color: #666;">{{$autoridad1->position}}</div>
            </div>
            <div>
                <div style="font-weight: bold; color: #34495e;">{{$autoridad2->profile_professional}} {{$autoridad2->fullname}}</div>
                <div style="color: #666;">{{$autoridad2->position}}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 20px; padding: 15px; background-color: #2c3e50; color: white; border-radius: 8px;">
        <div style="font-size: 14px; line-height: 1.6;">
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: + 058 0424-5891682 || + 058 0414-5442298 || + 058 0424-5027880<br>
            Correo electrónico: colegiofrayluisa@gmail.com || direcciónacadémica.c.e.cfla@gmail.com || controldeestudios.c.e.cfla@gmail.com
        </div>
    </div>
</div>
