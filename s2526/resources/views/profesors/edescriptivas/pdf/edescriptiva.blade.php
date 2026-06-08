<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Informe descriptivo y analítico de los resultados de la evaluación {{ Session::get('pescolar_name') }}</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">

    <style type="text/css" media="print">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }
        .title{
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
    </style>
    <style>
        body{
            text-transform: uppercase;
        }
        footer {
            position: fixed;
            bottom: 2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
        }
    </style>
</head>

<body>

    @foreach ($edescriptivas as $edescriptiva)

        @php $lapso = (!empty($edescriptiva->lapso)) ? $edescriptiva->lapso:null; @endphp

        @if ($lapso)

            @includeIf('profesors.edescriptivas.pdf.partials.lapso.main')

        @else

            @includeIf('profesors.edescriptivas.pdf.partials.final.main')

        @endif

    @endforeach


</body>

</html>
