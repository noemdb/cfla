<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Ficha del Estudiante - {{ Session::get('pescolar_name') }}</title>
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

    @forelse ($estudiants as $estudiant)
        @php $student_record = $estudiant->student_record; @endphp
        @include('administracion.bienestars.pdf.partials.membrete')
        @include('administracion.bienestars.pdf.partials.estudiant')
        @include('administracion.bienestars.pdf.partials.table.estudiant')
        @include('administracion.bienestars.pdf.partials.table.illness')
        @include('administracion.bienestars.pdf.partials.table.parents')
        @include('administracion.bienestars.pdf.partials.footer')
        <div style="page-break-after:always;"></div> 

    @empty

        <h1>No hay datos</h1>
        
    @endforelse

</body>

</html>
