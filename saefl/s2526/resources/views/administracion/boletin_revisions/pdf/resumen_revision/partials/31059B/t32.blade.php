<tr>
    <th colspan="5" style="font-size: 0.7rem !important">V. Profesores por Áreas</th>
    <td colspan="4" rowspan="2" align="center" style="font-weight: bold">Apellidos y Nombres</td>
    <td colspan="3" rowspan="2" align="center" style="font-weight: bold">Cédula de Identidad</td>
    @php
    $pensums_count = $pensums->count() - 3;
    $pensums_par = ( $pensums_count % 2 == 0 ) ? true:false;
    $colspan_firma = ( $pensums_par ) ? 3 : 4 ;
    $colspan_vi = ( $pensums_par ) ? $pensums_count + 1 : $pensums_count ;
    @endphp
    <td colspan="{{ $colspan_firma }}" rowspan="2" align="center" style="font-weight: bold">Firma</td>
    <th colspan="{{ $colspan_vi - 1 }}" align="center" style="font-size: 0.7rem !important">VI. Identificaci&oacute;n del Curso</th>
</tr>
<tr>
    <td align="center" style="font-weight: bold;">N°</td>
    <td colspan="4" align="center" style="font-weight: bold">Áreas de Formación</td>
    <td colspan="{{ $colspan_vi -1 }}" align="center">PLAN DE ESTUDIO</td>
</tr>
@foreach ($pensums as $pensum)
@php
$asignatura = $pensum->asignatura;
$profesor = $pensum->pevaluacions->last()->profesor;
$class_name_profesor = (strlen($profesor->fullname) > 15) ? 'small':null ;
$class_name_asignatura = (strlen($asignatura->name) > 25) ? 'text_small':null ;
@endphp
<tr>
    <td>{{$loop->iteration }}</td>
    <td style="font-weight: bold; width:18px !important;">{{$pensum->asignatura->code_sm ?? ''}}</td>
    <td colspan="3" class="{{ $class_name_asignatura ?? '' }}">{{$asignatura->name ?? ''}}</td>
    <td colspan="4" class="td-nowrap {{$class_name_profesor ?? 'small-90'}}">{{$profesor->fullname ?? ''}}</td>
    <td colspan="3">{{$profesor->getCiFullF2() ?? ''}}</td>
    <td colspan="{{ $colspan_firma }}">&nbsp;</td>

    {{-- <td colspan="{{ $colspan_vi }}">&nbsp;</td> --}}

    @php $colspan = $colspan_vi - 1 ; @endphp
    @switch($loop->iteration)
    @case(1) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: bold">{{ $pestudio->description_aux ?? '' }} </td> @break
    @case(2) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: normal">CÓDIGO</td> @break
    @case(3) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: bold">{{ $pestudio->code_oficial ?? '' }}</td> @break
    @case(4) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: normal">AÑO CURSADO</td> @break
    @case(5) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: bold">{{ $grado->short_name ?? '' }}</td> @break
    @case(6) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: normal">SECCIÓN</td> @break
    @case(7) <td colspan="{{ $colspan ?? ''}}" align="center" style="font-weight: bold">{{ $seccion->name ?? '' }} </td> @break
    @case(8)
        @php $colspan_floor = floor($colspan/2); $colspan_ceil = ceil($colspan/2);  @endphp
        <td colspan="{{ $colspan_floor }}" align="center" style="font-weight: normal;" class="text_small">
            N° DE ESTUDIANTES<br>POR SECCI&Oacute;N
        </td>
        <td colspan="{{ $colspan_ceil }}" align="center" style="font-weight: normal; min-height: 10rem !important;" class="text_small">
            N° DE ESTUDIANTES<br> EN ESTA P&Aacute;GINA
        </td>
    @break
    @case(9)
        @php $rowspan = $pensums->count() - 8 ; @endphp
        @php $colspan_floor = floor($colspan/2); $colspan_ceil = ceil($colspan/2);  @endphp
        <th rowspan="{{$rowspan}}" colspan="{{ $colspan_floor }}" align="center" style="font-size: 0.7rem !important">{{ zfill($estudiants_full->count(),2) ?? '' }}</th>
        @php $count = str_pad($estudiants->count(),2, "0", STR_PAD_LEFT); @endphp
        <th rowspan="{{$rowspan}}" colspan="{{ $colspan_ceil }}" align="center" style="font-size: 0.7rem !important">{{ zfill($estudiants->count(),2) ?? '' }}</th>
    @break

    {{-- @default <td colspan="{{ $colspan_vi ?? ''}}" rowspan="" align="center">*</td> --}}

    @endswitch

    {{-- @php $rowspan = $pensums->count() - 9 @endphp --}}
    @if ($loop->iteration==10)
    {{-- <td colspan="{{ $colspan_vi ?? ''}}" rowspan="{{ $rowspan ?? ''}}" align="center">*</td> --}}
    @endif
</tr>
@endforeach

{{-- vertical-align: top !important; --}}
