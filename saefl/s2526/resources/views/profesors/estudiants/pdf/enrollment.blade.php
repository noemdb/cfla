<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Planilla del Estudiante</title>
    <link href="{{ asset('css/table_pdf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pdf/rt.css') }}" rel="stylesheet">

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
        html { margin: 1cm !important;}
    </style>
</head>

<body>

    {{-- profesors.estudiants.pdf.partials.membrete --}}

    @include('profesors.estudiants.pdf.partials.membrete')

    @include('profesors.estudiants.pdf.partials.titlle')

    @include('profesors.estudiants.pdf.partials.estudiant')
    {{-- <hr> --}}
    @include('profesors.estudiants.pdf.partials.direccion')
    {{-- <hr> --}}

    <div style="page-break-after:always;"></div>

    <h3 align="center" style="margin-bottom: 0rem">Bienestar Estudiantíl</h3>

    @include('administracion.enrollments.pdf.binestar.estudiant')
    
    {{-- <hr> --}}

    @include('administracion.enrollments.pdf.binestar.illness')
    <br>
    @include('administracion.enrollments.pdf.binestar.parents')
    <br>
    {{-- @include('profesors.estudiants.pdf.partials.representant') --}}

    @include('profesors.estudiants.pdf.partials.signal')

    {{-- @include('administracion.bienestars.pdf.partials.table.illness') --}}
    {{-- @include('administracion.bienestars.pdf.partials.table.parents') --}}

{{--

'ci_estudiant','lastname','name','cellphone','gender','date_birth',
'age','town_hall_birth','state_birth','country_birth','dir_address','pestudio_id',
'grado_id','grupo_estable_id','pending_matter','blood_type','weight','height','laterality',
'order_born','status_brother','literal','ci_representant','name_representant','lastname_representant',
'relationship','profession_representant','phone_representant','cellphone_representant','email_representant','twitter','instagram'

--}}

</body>

</html>
