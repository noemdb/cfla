@php
    $representant = $estudiant->representant;
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);
    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
@endphp
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="https://sae.uefrayluisamigosf.com/images/avatar/uecfla.jpg">
            </th>
            <th>
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN DE CONTROL DE ESTUDIO</b></div>
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

                    <h3>{!!$subject ?? null!!}</h3>

                    <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$autoridad2->name}}</strong>, CI: {{$autoridad2->ci}},</em>
                    </p>

                    <div>
                        El estudiante:
                        <div>Nombre: <span style="font-weight: bold">{{$estudiant->fullname ?? null}}</span>.</div>
                        <div>Correo: <span style="font-weight: bold">{{$estudiant->gsemail ?? null}}</span>.</div>
                    </div>

                    <div style="text-align: justify; padding-left: 1rem; padding-botton: 1rem;margin-bottom: 1rem">
                        <span style="margin-bottom: 0rem">Representado por:</span>
                        <div style="padding-left: 1rem; margin-top: 0rem">
                            <span style="font-weight: bold">{{$representant->name  ?? null}} - CI: {{$representant->ci_representant ?? null}}</span>
                        </div>
                    </div>

                    <h2>
                        Requiere reestablecimiento de contraseña de acceso a Classroom
                    </h2>

                    <hr>

                    <p><i>Requerimiento enviado a través del Autorrespondedor - Control de Estudio.<i></p>

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
