<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Constancia de Buena Conducta {{ Session::get('pescolar_name') }}</title>
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
    </style>
</head>

<body>

    @include('representants.estudiants.pdf.carta_bconducta.partials.31059.membrete')

    @include('representants.estudiants.pdf.carta_bconducta.partials.31059.titlle')

    @include('representants.estudiants.pdf.carta_bconducta.partials.31059.main')

    @include('representants.estudiants.pdf.carta_bconducta.partials.31059.signal')

    @include('representants.estudiants.pdf.carta_bconducta.partials.31059.footer')

</body>

</html>
