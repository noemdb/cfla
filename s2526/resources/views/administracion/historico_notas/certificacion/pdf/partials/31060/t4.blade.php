<div class="tr_strong" style="padding-top: 0.2rem;" align="center">COMPONENTE FORMACIÓN CIENTIFICA, TECNOLÓGICA Y PRODUCTIVA</div>
<table width="100%" border="1" cellspacing="0" cellpadding="0"  id="table_normal" style="text-transform: uppercase; line-height: 0.5rem">

    <tr>
        <td class="tr_strong valign" rowspan="2" align="center">ÁREAS DE FORMACIÓN</td>
        <td class="tr_strong" colspan="3" align="center">CALIFICACIÓN</td>
        <td class="tr_strong no_wrap valign" rowspan="2" align="center">T-E</td>
        <td class="tr_strong" colspan="2" align="center">FECHA</td>
        <td class="tr_strong valign" rowspan="2" align="center">
            <div class="rotate-text orientation" style="font-size:0.5rem;width:0.8rem">
                INST. EDUC.
            </div>
        </td>
    </tr>
    <tr>
        <td class="tr_strong" align="center">AÑO°</td>
        <td class="tr_strong" align="center">N°</td>
        <td class="tr_strong" align="center">LETRAS</td>
        <td class="tr_strong" align="center"><small>MES</small></td>
        <td class="tr_strong" align="center"><small>AÑO</small></td>
    </tr>

    <tr>
        <td rowspan="6" align="center" class="tr_strong valign">Innovación Tecnológica y Productiva</td>
        <td align="center" class="td_sb"></td>
        <td colspan="2" align="center" class="td_sb"></td>
    </tr>

    @php $notas = $estudiant->GetAllHNotas($pestudio->id,'true','true') @endphp
    {{-- {{$notas}} --}}
    @php $n=0; @endphp
    @foreach ($notas as $nota)
        
        @php $grupo_estable = (!empty($nota->grupo_estable->name)) ? $nota->grupo_estable->name : null ; @endphp
        @php $grupo_estable = (empty($nota->literal)) ? '*': $grupo_estable ; @endphp
        @php $n++; @endphp
        @php $index = $nota->grado_id - 6; @endphp
        @php
            $tevaluacion = ($nota->tevaluacion) ? $nota->tevaluacion : null ;
            $tevaluacion_id = ($tevaluacion) ? $tevaluacion->id : null ;
            $status_exonerate = ($tevaluacion_id == 15 || $tevaluacion_id == 16) ? true : false ;

            $valor = (!empty($nota->valor)) ? $nota->valor : '*' ;
            $valor = (!empty($nota->valor)) ? $nota->valor : '*' ;
            $str_valor = (!empty($nota->valor)) ? num_to_string($nota->valor): '*' ;
            $code = (!empty($nota->valor)) ?$nota->tevaluacion->code: '*' ;
            $mes = (!empty($nota->valor)) ? substr($nota->fecha,0,2) : '*' ;
            $ano = (!empty($nota->valor)) ? substr($nota->fecha,-4) : '*' ;
            $institucion = (!empty($nota->valor)) ? $arr_institucion[$nota->institucion_id] : '*' ;

            $valor = (!empty($valor) && is_numeric($valor) && $valor < 10) ? $valor = str_pad($valor, 2, "0", STR_PAD_LEFT) : $valor ;

            $valor = ($valor == -1) ? 'I' : $valor ;
            $number = ($nota->code_sm) ? $nota->code_sm[0] : null ;
        @endphp
        <tr>
            <td align="center" class="">{{$number.'º'}}</td>
            <td align="center" class="">{{ ($status_exonerate) ? 'EX' : $valor }}</td>
            <td align="center" style="font-size:0.45rem" class="">{{($status_exonerate) ? 'EXONERADA' : $str_valor}}</td>
            <td align="center" class="">{{ ($status_exonerate) ? '*' : $code }} </td>
            <td align="center" class="">{{ ($status_exonerate) ? '*' : $mes }} </td>
            <td align="center" class="">{{ ($status_exonerate) ? '*' : $ano }} </td>
            <td align="center" class="">{{ $institucion }} </td>
        </tr>
    @endforeach

    @if ($n < 5)
        @php $m = $n + 1; @endphp
        @for ($i = $m ; $i <= 5; $i++)
        <tr>
            <td align="center" class="">{{$i.'º'}} </td>
            <td align="center" class="td_notas_sm">*</td>
            <td align="center" class="td_notas_sm">*</td>
            <td align="center" class="td_notas_sm">*</td>
            <td align="center" class="td_notas_sm">*</td>
            <td align="center" class="td_notas_sm">*</td>
            <td align="center" class="td_notas_sm">*</td>
        </tr>
        @endfor
    @endif

    <tr>
        <td class="tr_strong valign" rowspan="2" align="center">ÁREAS DE FORMACIÓN</td>
        
        <td class="tr_strong" colspan="7" align="center">CALIFICACIÓN</td>
    </tr>
    <tr>
        <td align="center" class="tr_strong">AÑO</td>
        <td class="tr_strong" colspan="7" align="center">LITERAL</td>
    </tr>

    <tr>
        <td rowspan="6" align="center" class="tr_strong valign">ORIENTACIÓN <br />VOCACIONAL</td>
        <td align="center" class="td_sb"></td>
        <td colspan="2" align="center" class="td_sb"></td>
    </tr>

    @php $notas = $estudiant->GetAllHNotas($pestudio->id,'false','true') @endphp
    @php $n=0; @endphp
    @foreach ($notas as $nota)
        @if ($nota->pensum->asignatura->enable_grupo_estable == 'false' && !empty($nota->literal))
            @php $n++; @endphp
            <tr>
                <td align="center" class="">{{$n.'º'}} </td>
                @php $nota->literal = ( $nota->literal == 'EX') ? 'EXONERADA': $nota->literal;  @endphp
                <td colspan="7" align="center" class="td_notas_sm">{{ $nota->literal ?? '*' }}</td>
            </tr>
        @endif
    @endforeach

    @if ($n < 5)
        @php $m = $n + 1; @endphp
        @for ($i = $m ; $i <= 5; $i++)
        <tr>
            <td align="center" class="">{{$i.'º'}} </td>
            <td colspan="7" align="center" class="td_notas_sm">*</td>
        </tr>
        @endfor
    @endif    

</table>
