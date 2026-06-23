<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Pase Escolar - {{$estudiant->fullname}} {{ Session::get('pescolar_name') }}</title>
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

    {{-- /home/nuser/code/s2324/resources/views/permissions/pases/pdf/partials/membrete.blade.php --}}

    @include('permissions.pases.pdf.partials.membrete')

    @include('permissions.pases.pdf.partials.title')

    @include('permissions.pases.pdf.partials.estudiant')

    @include('permissions.pases.pdf.partials.profesor')

    {{-- @include('permissions.pases.pdf.partials.pensum') --}}

    @include('permissions.pases.pdf.partials.representant')

    @include('permissions.pases.pdf.partials.main')

    <hr>

    @include('permissions.pases.pdf.partials.sign')

    @include('permissions.pases.pdf.partials.footer')
    
</body>

</html>
