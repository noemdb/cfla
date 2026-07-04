{{-- @if ($flag_peva)    --}}

    @php
        $count_eva = $pensum->count_evaluacion($pensum->id,$lapso_id,$seccion_id);

        $p_notas_c = $pensum->p_notas_c($pensum->id,$lapso_id,$seccion_id);
        $count_notas = $pensum->count_notas($pensum->id,$lapso_id,$seccion_id);
        $total_notas = $pensum->notas($pensum->id,$lapso_id,$seccion_id)->sum('nota');

        $pevaluacion = $pensum->pevaluacions->where('lapso_id',$lapso_id)->where('seccion_id',$seccion_id)->first();
        // $c_eva_c = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions->count() : null ;
        // $c_eva_c =  $pevaluacion->evaluacions->count();
        $nota_maxima = (!empty($pevaluacion->escala)) ? $pevaluacion->escala->maximo : null ;

        $promedio = ( isset($count_notas) && !empty($total_notas) ) ? round( ($total_notas / $count_notas ), 2 ) : null;
    @endphp

    {{-- {{$count_eva ?? ''}} --}}
    {{-- {{$p_notas_c ?? ''}} --}}
    {{-- {{$total_notas ?? ''}} --}}
    {{-- {{$nota_maxima ?? ''}} --}}
    {{-- {{$count_notas ?? ''}} --}}
    @if (!empty($count_eva))
        <h3>
            <span class="badge badge-secondary" title="N° de Evaluaciones Asignadas">{{$count_eva ?? ' '}} </span>
            <span class="badge badge-dark bd-callout-bg-{{c_porc($p_notas_c) ?? ''}}" title="% de Notas Rgistradas">
                {{ ($p_notas_c) ? $p_notas_c.'%' : '0%'}}
            </span>
            <span class="badge badge-light" title="Promedio de notas">{{$promedio ?? '0'}} </span>
        </h3>
    @endif

{{-- @endif --}}
