<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=8">
  <title>CARTA DIGITAL DE ACEPTACIÓN, MATRICULA ESCOLAR 2026 - 2027</title>
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

    .box {
      display: flex;
      /* align-items: center; */
      justify-content: center;
    }

    /* .box div {
      width: 100px;
      height: 100px;
    } */
  </style>

</head>

<body style="display: flex; justify-content: center; border: 1px #ccc solid; align-content: center;max-width: 18cm">

  <div class="continer" style="padding: 4px">
    @include('general.catchments.partials.membrete')

    <h2 style="text-align: center; margin-top: 0px; margin-bottom:0px">MATRICULA ESCOLAR 2026 - 2027</h1>
      <h4 style="text-align: center; margin-top: 0px; margin-bottom:0px">CONSTANCIA DIGITAL DE ACEPTACIÓN</h3>

        @include('general.catchments.partials.accepted')


        @php $token = $catchment_interview->token; $link = route('catchments.accepted',$token) @endphp
        <div style="display: flex; justify-content: center;">{!! DNS2D::getBarcodeHTML("$link", 'QRCODE',3,3) !!}</div>

        <hr>

        <div style="font-size: 0.8rem">
          SAN FELIPE a los <strong>{{Carbon\Carbon::now()->day}}</strong> días del mes
          de <strong>{{Carbon\Carbon::now()->monthName}}</strong> de <strong>{{Carbon\Carbon::now()->year}}</strong> .
        </div>

        <p>&nbsp;</p>

        <table class="table-sm" style="font-size:0.7rem">
          <tr>
            <td width="100%" style="text-align: center">
              @if ($autoridad1)
              {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
              <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
              @endif
            </td>
          </tr>
        </table>


        <div class="text-muted" style="font-size:7px;">
          {{-- Elaborado por: {{ (Auth::user()) ? Auth::user()->profile->full_name : null}} --}}
          <hr>
          <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
          </span>
        </div>
  </div>

</body>

</html>



{{-- religion
awareness_of_catholic_school_affiliation
agreement_to_catholic_formation
agreement_to_participate_in_catholic_activities
justification_for_not_participating_in_catholic_activities --}}