<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Resumen Final del Rendimiento Estudiantil - Revisión {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/pdf/hr.css') }}" rel="stylesheet">

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

            /* src: url("font url"); */
        }
        /* html { margin: 0.7cm!important;} */
    </style>
</head>

<body>

    @php
        $limit_page = 35;
        $estudiants_full = $estudiants;
        $estudiants_chunks = $estudiants;
        $count_item = 0;
    @endphp

    <div class="page">

        @include('administracion.boletin_revisions.pdf.resumen_revision.partials.31059.t1')
        @include('administracion.boletin_revisions.pdf.resumen_revision.partials.31059.t2')
        @include('administracion.boletin_revisions.pdf.resumen_revision.partials.31059.t3')
        @include('administracion.boletin_revisions.pdf.resumen_revision.partials.31059.t4')
        @include('administracion.boletin_revisions.pdf.resumen_revision.partials.31059.t5')

    </div>

    <div style="page-break-after:always;"></div>

</body>

</html>
