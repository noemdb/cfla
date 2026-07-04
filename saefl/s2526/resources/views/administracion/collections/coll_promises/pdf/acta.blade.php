<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Acta Compromiso {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/pdf/hr.css') }}" rel="stylesheet">

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
</head>

<body>
    @include('email.collections.coll_promise')
</body>

</html>
