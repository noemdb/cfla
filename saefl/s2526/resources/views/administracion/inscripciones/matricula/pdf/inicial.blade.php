
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Lista de Estudiantes Inscritos {{ Session::get('pescolar_name') }}</title>
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/btable.css') }}" rel="stylesheet"> --}}

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
            font-size: 9px;
        }
        .w100{
            width: 100%;
            display: inline;
            text-decoration: underline;
        }
        td,th{
            /* border: 1px solid #ccc; */
            margin: 0;
            padding: 0;
        }
        .table {
            width: 100%;
            max-width: 100%;
            /* margin-bottom: 1rem; */
        }
        .table-sm th,
        .table-sm td {
            padding: 0rem;
        }
        .uline{
            border-bottom: 1px solid #000;
            text-align: center;
        }
        .blank{
            text-align: center;

        }

    </style>
</head>

<body>

    @foreach ($pestudios as $pestudio)
    @php
        $grados = $pestudio->grados->where('id',$grado_id);
    @endphp
        @foreach ($grados as $grado)
            @if($grado->status_active=="true")
                @foreach ($grado->seccions as $seccion)
                    @if($seccion->status_active=="true")
                    <table class="table table-striped table-sm" style="font-size:8px" cellpadding="0" cellspacing="0" border="0">

                    @php
                        $offset = 0;
                        $total_std = count($seccion->inscripcions);
                        $num_estuden = $total_std;
                    @endphp

                    @if ($total_std >= 30)
                        @php
                            $num_estuden = 30;
                        @endphp
                    @endif

                    {{-- Cerrar tabla --}}
                    @php
                        $cerrar_tabla = '</tbody></table>'
                    @endphp
                    {{-- abrir tabla --}}
                    @php
                        $abrir_tabla = '<table class="table table-striped table-sm" style="font-size:8px"  cellpadding="0" cellspacing="0" border="0">'
                    @endphp
                    {{-- footer --}}
                    @php
                        $footer = '
                            <footer class="text-muted" style="font-size:7px;">
                                Elaborado por: '.Auth::user()->profile->full_name.'
                                <hr>
                                <u>
                                    AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
                                    Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
                                </u>
                            </footer>
                        '
                    @endphp

                    {{-- observaciones --}}
                    @php
                        $observaciones = '

                        <tr>
                            <td colspan="12">
                                <table class="table table-striped table-sm" style="font-size:8px" cellpadding="0" cellspacing="0" border="1">
                                    <tr><th colspan="4"><span style="font-weight:bold">V. Observaciones</span></th></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr><td class="uline" colspan="4">&nbsp;</td></tr>
                                    <tr>
                                        <td colspan="2"><span style="font-weight:bold">VI. Fecha de Remisión:</span></td>
                                        <td colspan="2"><span style="font-weight:bold">VII. Fecha de Recepción:</span></td>
                                    </tr>
                                    <tr>
                                        <td>Directora:</td>
                                        <td rowspan="6"><center>Sello del Plantel</center></td>
                                        <td>Funcionario Receptor:</td>
                                        <td rowspan="6"><center>Sello de la Zona Educativa</center></td>
                                    </tr>
                                    <tr><td>Apellidos y Nombres</td><td>Apellidos y Nombres</td></tr>
                                    <tr><td>'.$autoridad1->name.'<br>'.$autoridad1->lastname.'</td><td></td></tr>
                                    <tr><td>Número de C.I.</td><td>Número de C.I.</td></tr>
                                    <tr><td>V-'.$autoridad1->ci.'</td><td></td></tr>
                                    <tr>
                                        <td align="center" style="height: 30px;vertical-align: bottom;">Firma<br></td>
                                        <td align="center" style="height: 30px;vertical-align: bottom">Firma</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        ';
                    @endphp


                    {{-- salto de pagina --}}
                    @php
                        $salto_page = '<div style="page-break-after:always;"></div>';
                    @endphp

                    {{-- cerrar_page --}}
                    @php
                        $cerrar_page = '</div>';
                    @endphp

                                {{-- {!!$header!!} --}}

                                @php
                                    $collections = $seccion->inscripcions
                                @endphp

                                @switch($order)
                                    @case('ci_estudiant')
                                        @php
                                            $inscripcions = $collections->sortBy(function ($value, $key) {return $value->estudiant->ci_estudiant;})
                                        @endphp
                                        @break
                                    @case('lastname')
                                        @php
                                            $inscripcions = $collections->sortBy(function ($value, $key) {return $value->estudiant->lastname;})
                                        @endphp
                                        @break
                                    @default
                                @endswitch

                                @php
                                    $inscripcion = $inscripcions->first();
                                    $n = 0;
                                @endphp

                                @if ($num_estuden>0)
                                {!! $inscripcion->print_header($num_estuden,$pestudio->name,$pestudio->code_oficial,$grado->name,$seccion->name,$total_std) !!}
                                @endif

                                @foreach ($inscripcions as $inscripcion)
                                    @php
                                        $n++;
                                    @endphp
                                    @if ($inscripcion->estudiant->status_active=='true')

                                        <tr>
                                            <td>{{zfill($n,2)}}</td>
                                            <td>C-{{$inscripcion->estudiant->ci_estudiant ?? 'C'.$inscripcion->estudiant->ci_estudiant_temp }}</td>
                                            <td>{{$inscripcion->estudiant->lastname ?? ''}}</td>
                                            <td>{{$inscripcion->estudiant->name ?? ''}}</td>
                                            <td>{{$inscripcion->estudiant->gender[0] ?? ''}}</td>
                                            <td>
                                                {{ (isset($inscripcion->estudiant->date_birth) && $inscripcion->estudiant->date_birth<>'0000-00-00') ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('d') : ' - ' }}
                                            </td>
                                            <td>
                                                {{ (isset($inscripcion->estudiant->date_birth) && $inscripcion->estudiant->date_birth<>'0000-00-00') ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('m') : ' - ' }}
                                            </td>
                                            <td>
                                                {{ (isset($inscripcion->estudiant->date_birth) && $inscripcion->estudiant->date_birth<>'0000-00-00') ? Carbon\Carbon::parse($inscripcion->estudiant->date_birth)->format('Y') : ' - ' }}
                                            </td>
                                            <td class="uline">{{ ($inscripcion->escolaridad->code == "RG") ? 'X':'-' }}</td>
                                            <td class="uline">{{ ($inscripcion->escolaridad->code == "RP") ? 'X':'-' }}</td>
                                            <td class="uline">{{ ($inscripcion->escolaridad->code == "MP") ? 'X':'-' }}</td>
                                            <td class="uline">{{ ($inscripcion->escolaridad->code == "DI") ? 'X':'-' }}</td>
                                        </tr>
                                        @if ($n == 30)

                                            @php
                                                $offset = $offset + 30;
                                                $num_estuden = $total_std - $offset;
                                                $n = 0;
                                            @endphp

                                            {{-- <tr><td colspan="5">{{$num_estuden.' - '.$pestudio->name.' - '.$pestudio->code.' - '.$grado->name.' - '.$seccion->name.' - '.$total_std}}</td></tr> --}}


                                            {!! $observaciones !!}
                                            {!! $cerrar_tabla !!}
                                            {{-- {!! $footer !!} --}}

                                            {!! $salto_page !!}
                                            @if ($num_estuden<>0)
                                            {!! $abrir_tabla !!}

                                            @if ($num_estuden>0)
                                            {!! $inscripcion->print_header($num_estuden,$pestudio->name,$pestudio->code_oficial,$grado->name,$seccion->name,$total_std) !!}
                                            @endif

                                            @endif
                                        @endif

                                    @endif
                                @endforeach

                                @if ($num_estuden<30 && $num_estuden<>0)

                                    @php
                                        $limit = 30 - $num_estuden;
                                    @endphp

                                    {{-- <tr><td colspan="5">{{$limit.' - '.$num_estuden}}</td></tr> --}}

                                    @for ($i = 0; $i < $limit; $i++)
                                        <tr class="blank">
                                            <td>*</td><td>*</td> <td>*</td> <td>*</td> <td>*</td> <td>*</td><td>*</td><td>*</td><td>*</td><td>*</td><td>*</td><td>*</td>
                                        </tr>
                                    @endfor

                                    {{-- {!! $observaciones !!} --}}
                                    {!! $observaciones !!}
                                    {!! $cerrar_tabla !!}
                                    {{-- {!! $footer !!} --}}
                                    {!! $salto_page !!}
                                    {!! $abrir_tabla !!}

                                    {{-- {!! $inscripcion->print_header($num_estuden,$pestudio->name,$pestudio->code,$grado->name,$seccion->name,$total_std) !!} --}}

                                @endif

                            {{-- {!!$footer!!} --}}
                            {!!$cerrar_page!!}

                        {{-- <hr> --}}
						{{-- <div style="page-break-after:always;"></div> --}}
                    @endif
                @endforeach
                {{-- <div style="page-break-after:always;"></div> --}}
            @endif
        @endforeach
    @endforeach



</body>

</html>
