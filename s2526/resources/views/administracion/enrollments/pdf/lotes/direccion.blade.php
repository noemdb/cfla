<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">
    <thead>
      <tr>
        <th colspan="10" align="left" style="font-weight: bold !important;font-size: 0.8rem !important">DIRECCIÓN</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="10">{!! $enrollment->dir_address ?? '&nbsp;' !!}</td>
      </tr>
      <tr>
        <td colspan="2"> <span style="font-weight: bold !important;font-size: 0.8rem !important">GRUPO SANGUÍNEO:</span> {{$enrollment->blood_type ?? ''}}</td>
        <td colspan="2" style="font-weight: bold !important;font-size: 0.8rem !important">PESO:</td>
        <td>{{$enrollment->weight ?? ''}} (Kg)</td>
        <td colspan="3" style="font-weight: bold !important;font-size: 0.8rem !important">ESTATURA:</td>
        <td colspan="2">{{$enrollment->height ?? ''}} (centímetros)</td>
      </tr>
      <tr>
        <td colspan="2">LATERALIDAD</td>
        <td colspan="8">ORDEN DE NACIMIENTO</td>
        {{-- <td colspan="3">GRUPO FAMILIAR</td> --}}
      </tr>
      <tr>
        <td>IZQUIERDO ( {!! ($enrollment->laterality=='IZQUIERDA') ? 'X': '&nbsp;&nbsp;' !!} )</td>
        <td>DERECHO ( {!! ($enrollment->laterality=='DERECHA') ? 'X': '&nbsp;&nbsp;' !!} )</td>
        <td>U</td>
        <td class="{{($enrollment->order_born == '1') ? "tr_strong" : null}}" >1</td>
        <td class="{{($enrollment->order_born == '2') ? "tr_strong" : null}}" >2</td>
        <td class="{{($enrollment->order_born == '3') ? "tr_strong" : null}}" >3</td>
        <td class="{{($enrollment->order_born == '4') ? "tr_strong" : null}}" >4</td>
        <td class="{{($enrollment->order_born == '5') ? "tr_strong" : null}}" >5</td>
        <td class="{{($enrollment->order_born == '6') ? "tr_strong" : null}}" >5</td>
        <td class="{{($enrollment->order_born == '7') ? "tr_strong" : null}}" >5</td>
        {{-- <td colspan="2">&nbsp;</td> --}}
      </tr>
      <tr>
        <td colspan="2">TIENE HERMANOS EN EL COLEGIO?</td>
        <td colspan="1">SI</td>
        <td align="center">( &nbsp; {{($enrollment->status_brother == 'true') ? 'X': null}} &nbsp; )</td>
        <td colspan="1">NO</td>
        <td align="center">( &nbsp; {{($enrollment->status_brother == 'false') ? 'X': null}} &nbsp; )</td>
        <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight: bold !important;font-size: 0.8rem !important">GRADOS QUE CURSAN</td>
        <td colspan="8"></td>
      </tr>
      <tr>
        <td colspan="8">LITERAL DE PROMOCIÓN DEL GRADO ANTERIOR (SOLO PRIMARIA)</td>
        {{-- <td colspan="2">{{$enrollment->literal ?? null}}</td> --}}
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="10">MATERIA (S) PENDIENTE (S) : SI (&nbsp;&nbsp;&nbsp;) | NO (&nbsp;&nbsp;&nbsp;) . DE SER AFIRMATIVO, SEÑALALA (S)</td>
    </tbody>
    </table>

