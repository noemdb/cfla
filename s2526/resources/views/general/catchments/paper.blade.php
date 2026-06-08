<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=8">
  <title>ENTREVISTA AL RERESENTANTE, CENSO {{ Session::get('pescolar_name') ?? null}}</title>
  <link href="{{ asset('css/table_pdf.css') ??  null}}" rel="stylesheet">

  <style type="text/css" media="print">
    div.page {
      page-break-after: always;
      page-break-inside: avoid;
    }

    .title {
      font-weight: bold;
      font-size: 14px;
    }

    footer {
      position: fixed;
      top: -60px;
      left: 0px;
      right: 0px;
      height: 50px;
    }

    body {
      text-transform: uppercase;
    }
  </style>

  <style>
    .field-label {
      /* width: 20rem; */
    }

    .field-label {
      /* max-width: 5rem; */
      margin-top: 1rem;
      padding-top: 1rem;
      white-space: wrap !important;
      font-weight: bold;
    }

    .field-value {
      white-space: nowrap !important;
    }
  </style>

</head>

<body>

  @include('administracion.boletins.pdf.partials.31059.membrete')

  <h2 style="text-align: center; margin-top: 0px; margin-bottom:0px">Censo Escolar 2026 - 2027</h1>
  <h4 style="text-align: center; margin-top: 0px; margin-bottom:0px">ENTREVISTA AL RERESENTANTE</h3>

    <title>
        {{ $catchment_interview->exists ? 'ENTREVISTA AL REPRESENTANTE' : 'FORMATO DE ENTREVISTA EN BLANCO' }}
    </title>

    @include('general.catchments.partials.interview_paper')

  <hr>
  <div style="font-size: 0.8rem">
    SAN FELIPE a los <strong>{{Carbon\Carbon::now()->day}}</strong> días del mes
    de <strong>{{Carbon\Carbon::now()->monthName}}</strong> de <strong>{{Carbon\Carbon::now()->year}}</strong> .
  </div>

  <p>&nbsp;</p>

  <table width="100%" style="margin-top:12px; margin-bottom:18px;">
    <tr>
        <td style="
            font-size:0.65rem;
            text-align:justify;
            line-height:1.4;
            padding:0 12px;
            word-wrap:break-word;
            white-space:normal;
        ">
            <em>
                Una vez emitida y notificada oportunamente la 
                <strong>carta de aceptación</strong> por parte de la institución, 
                la reserva del cupo escolar requerirá el pago único de 
                <strong>USD 120</strong>, 
                correspondiente al proceso de apartado.
            </em>
        </td>
    </tr>
</table>

   <p>&nbsp;</p>

  <table class="table-sm" width="100%" style="font-size:0.7rem; margin-top:20px;">
    <tr>
        <td width="50%" style="text-align:center; vertical-align:top;">
            <div style="border-top:1px solid #000; width:80%; margin:0 auto; padding-top:5px;">
                @if ($autoridad2)
                    {{ $autoridad2->profile_professional.' '. $autoridad2->name.' '.$autoridad2->lastname }}<br>
                    <span class="text-muted">{{ $autoridad2->position ?? '' }}</span>
                @endif
            </div>
        </td>

        <td width="50%" style="text-align:center; vertical-align:top;">
            <div style="border-top:1px solid #000; width:80%; margin:0 auto; padding-top:5px;">
                FIRMA DEL REPRESENTANTE<br>
                <span class="text-muted">
                    {{ $catchment_interview->full_name ?? '' }}
                </span>
            </div>
        </td>
    </tr>
</table>

  <div class="text-muted" style="font-size:7px;">
    Elaborado por: {{ (Auth::user()) ? Auth::user()->profile->full_name : null}}
    <hr>
    <span>
      AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
      Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
  </div>


</body>

</html>



{{-- religion
awareness_of_catholic_school_affiliation
agreement_to_catholic_formation
agreement_to_participate_in_catholic_activities
justification_for_not_participating_in_catholic_activities --}}