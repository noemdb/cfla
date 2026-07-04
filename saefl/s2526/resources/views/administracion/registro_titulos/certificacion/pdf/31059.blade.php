<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>

<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <TITLE>CERTIFICACI&#211;N DE CALIFICACIONES EMG</TITLE>
    <META name="generator" content="BCL easyConverter SDK 5.0.210">
    <link href="{{ asset('css/pdf/cn.css') }}" rel="stylesheet">
    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
            width: 778px !important;
            max-width: 778px !important;
        }
        .title{
            font-weight: bold;
            font-size: 14px;
        }
        footer {
            position: fixed;
            top: -2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
        }
    </style>
    <style>
        @font-face {
          font-family: 'Helvetica';
          font-weight: normal;
          font-style: normal;
          font-variant: normal;
          src: url("font url");
        }
        body {
          font-family: Helvetica, sans-serif;
          /* text-transform: uppercase; */
        }
    </style>
</HEAD>

<BODY>

    <div class="page">

        {{-- I. Plan de Estudio: --}}
        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t1')

        {{-- II. Datos del Plantel o Zona Educativa que emite la Certificación --}}
        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t2')

        <table cellpadding="1" cellspacing="1" width="100%" style="padding-top:0.2rem">
            <thead>
                <tr>
                    <th align="left" colspan="2">
                        V. Plan de Estudio
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="50%">
                        @php $grado = $pestudio->grados->slice(0,1)->first() @endphp
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t3',['grado'=>$grado])
                    </td>
                    <td width="50%">
                        @php $grado = $pestudio->grados->slice(1,1)->first() @endphp
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t3',['grado'=>$grado])
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        @php $grado = $pestudio->grados->slice(2,1)->first() @endphp
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t3',['grado'=>$grado])
                    </td>
                    <td width="50%">
                        @php $grado = $pestudio->grados->slice(3,1)->first() @endphp
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t3',['grado'=>$grado])
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        @php $grado = $pestudio->grados->slice(4,1)->first() @endphp
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t3',['grado'=>$grado])
                    </td>
                    <td valign="top" width="50%">
                        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t4')
                    </td>
                </tr>
            </tbody>
        </table>

        <br>

        @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t5')

    </div>

    <div style="page-break-after:always;"></div>

    @includeIf('administracion.registro_titulos.certificacion.pdf.partials.t6')



</BODY>

</HTML>
