<table class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">
    <thead>
        <tr>
            <th colspan="10" align="left" style="font-weight: bold !important;font-size: 0.8rem !important">DIRECCIÓN
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="10">{{ $enrollment->dir_address ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="3"> <span style="font-weight: bold !important;font-size: 0.8rem !important">GRUPO
                    SANGUÍNEO:</span> {{ $enrollment->blood_type ?? '' }}</td>
            <td colspan="2" style="font-weight: bold !important;font-size: 0.8rem !important">PESO:</td>
            <td>{{ $enrollment->weight ?? '' }} (Kg)</td>
            <td colspan="3" style="font-weight: bold !important;font-size: 0.8rem !important">ESTATURA:</td>
            <td>{{ $enrollment->height ?? '' }} (centímetros)</td>
        </tr>
        <tr>
            <td colspan="4">LATERALIDAD</td>
            <td colspan="6">ORDEN DE NACIMIENTO</td>
            {{-- <td colspan="3">GRUPO FAMILIAR</td> --}}
        </tr>
        <tr>
            <td colspan="4">
                IZQUIERDO ( {!! optional($enrollment)->laterality == 'IZQUIERDA' ? 'X' : '&nbsp;&nbsp;' !!} ) &nbsp;|&nbsp;
                DERECHO ( {!! optional($enrollment)->laterality == 'DERECHA' ? 'X' : '&nbsp;&nbsp;' !!} )
            </td>
            {{-- <td colspan="2"></td> --}}
            {{-- <td>U</td> --}}
            <td {{ optional($enrollment)->order_born == 'U' ? 'style=background-color:#ccc' : '' }}>U</td>
            <td {{ optional($enrollment)->order_born == '1' ? 'style=background-color:#ccc' : '' }}>1</td>
            <td {{ optional($enrollment)->order_born == '2' ? 'style=background-color:#ccc' : '' }}>2</td>
            <td {{ optional($enrollment)->order_born == '3' ? 'style=background-color:#ccc' : '' }}>3</td>
            <td {{ optional($enrollment)->order_born == '4' ? 'style=background-color:#ccc' : '' }}>4</td>
            <td {{ optional($enrollment)->order_born == '5' ? 'style=background-color:#ccc' : '' }}>5</td>
            {{-- <td class="{{($enrollment->order_born == '6') ? "style='background-color: darkgrey'" : null}}"  >6</td> --}}
            {{-- <td class="{{($enrollment->order_born == '7') ? "style='background-color: darkgrey'" : null}}"  >7</td> --}}
            {{-- <td colspan="2">&nbsp;</td> --}}
        </tr>
        <tr>
            <td colspan="2">TIENE HERMANOS EN EL COLEGIO?</td>
            <td colspan="4">SI ({!! optional($enrollment)->status_brother == 'true' ? 'X' : '&nbsp;&nbsp;' !!})</td>
            <td colspan="4">NO ({!! optional($enrollment)->status_brother == 'false' ? 'X' : '&nbsp;&nbsp;' !!})</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold !important;font-size: 0.8rem !important">GRADOS QUE CURSAN</td>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="8">LITERAL DE PROMOCIÓN DEL GRADO ANTERIOR (SOLO PRIMARIA)</td>
            <td colspan="2">{{ optional($enrollment)->grado_id <= 6 ? optional($enrollment)->literal : null }}</td>
        </tr>
        <tr>
            <td colspan="10">MATERIA (S) PENDIENTE (S) : SI (&nbsp;&nbsp;&nbsp;) | NO (&nbsp;&nbsp;&nbsp;) . DE SER
                AFIRMATIVO, SEÑALALA (S)</td>
    </tbody>
</table>
