<div class="tr_strong" style="padding-top: 0.2rem;" align="center">ÁREA DE FORMACIÓN</div>
{{-- <table width="100%" border="1" cellspacing="0" cellpadding="0" style="padding:1px 0px 1px 0px;margin:1px 0px 1px 0px" > --}}
<table width="100%" border="1" cellspacing="0" cellpadding="0"  id="table_normal">
    <tr>
      <td align="center" class="tr_strong"><small>ÁREA DE FORMACIÓN</small></td>
      <td align="center" class="tr_strong">AÑO</td>
      <td colspan="2" align="center" class="tr_strong">LITERAL</td>
    </tr>
    <tr>
      <td rowspan="6" align="center" class="tr_strong valign"> ORIENTACION Y<br />CONVIVENCIA</td>
      <td align="center" class="td_sb"></td>
      <td colspan="2" align="center" class="td_sb"></td>
    </tr>
    @php $notas = $estudiant->GetAllHNotas($pestudio->id,'false') @endphp
    @php $n=0; @endphp
    @foreach ($notas as $nota)
        @if ($nota->pensum->asignatura->enable_grupo_estable == 'false')
            @php $n++; @endphp
            <tr>
                <td align="center" class="">{{$n.'º'}} </td>
                <td colspan="2" class="td_notas_sm" align="center">{{ $nota->literal ?? '*'}}</td>
            </tr>
        @endif
    @endforeach

    <tr>
        <td align="center" class="tr_strong"> <small>ÁREA DE FORMACIÓN</small></td>
        <td align="center" class="tr_strong">AÑO</td>
        <td align="center" class="tr_strong">GRUPO</td>
        <td align="center" class="tr_strong">LITERAL</td>
    </tr>

    <tr>
      <td rowspan="6" align="center" class="tr_strong valign">PART. EN GRUPOS DE<br /> CREACIÓN,<br /> RECREACIÓN Y<br /> PRODUCCIÓN</td>
      <td align="center" class="td_sb"></td>
      <td colspan="2" align="center" class="td_sb"></td>
    </tr>

    @php $notas = $estudiant->GetAllHNotas($pestudio->id,'false') @endphp
    @php $n=0; @endphp
    @foreach ($notas as $nota)
        @if ($nota->pensum->asignatura->enable_grupo_estable == 'true')
            @php $grupo_estable = (!empty($nota->grupo_estable->name)) ? $nota->grupo_estable->name : null ; @endphp
            @php $grupo_estable = (empty($nota->literal)) ? '*': $grupo_estable ; @endphp
            @php $n++; @endphp
            <tr>
                <td align="center" class="">{{$n.'º'}} </td>
                <td align="center" class="td_notas_sm">{{ ( $nota->literal == 'EX' ) ? 'EXONERADA': $grupo_estable }}</td>
                <td align="center" class="td_notas_sm">{{ $nota->literal ?? '*'}}</td>
            </tr>
        @endif
    @endforeach

  </table>
