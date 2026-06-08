<tbody id="tdatos">

    @php $sum_nota = 0; @endphp
    @php $count_nota = 0; @endphp
    @foreach ($pensums as $pensum)
        @php $asignatura = (!empty($pensum->asignatura)) ? $pensum->asignatura:null ; @endphp
        @php $count = null; @endphp
        <thead style="background-color:#e0e0e0">
            <tr style="background-color:#e0e0e0">

                <td style="font-size:0.8rem;text-align:left;overflow-wrap: break-word !important;">
                    {{$asignatura->name}}
                    @php $profesors = $pensum->getProfesorsSeccionLapso($seccion->id,$lapso->id); @endphp
                    @if ($profesors->count() > 1) <br> @else || @endif
                    {{-- @if ($profesors->count() > 1) <br> @endif --}}
                    @forelse ($profesors as $item)
                        <span style="font-size:0.6rem">
                            Prof:  {{ $item->fullname ?? 'sin profesor' }}
                            {{($item->grupo_estable_name) ? '['. $item->grupo_estable_name.']' :null}}
                        </span> @if (! $loop->last ) <br> @endif
                    @empty
                        <span>sin profesor</span>
                    @endforelse                    
                </td>

                    @php $nota = $pensum->GetNota($estudiant->id,$seccion->id,$lapso->id) ; @endphp
                    @if ($nota)
                        @php $sum_nota = $sum_nota + $nota; @endphp
                        @php $count_nota = $count_nota + 1; @endphp
                    @endif

                <td style="font-size:0.8rem;text-align:right">
                </td>
            </tr>
        </thead>
        <tr>
            <td colspan="2">

                @php $evaluacions = $pensum->evaluacions_corte($lapso->id,$seccion->id) ; @endphp

                <table class="table" style="border-collapse: collapse;">

                    <tbody id="tdatos">
                        @foreach ($evaluacions as $evaluacion)
                            @php 
                                $boletin = $evaluacion->boletin_corte($lapso->id,$estudiant->id);
                                $pevaluacion = $evaluacion->pevaluacion;
                                $grupo_estable = ($pevaluacion) ? $pevaluacion->grupo_estable : null ;
                            @endphp
                            @if ($boletin)
                                @php $count++; @endphp
                                <tr style="font-size:0.7rem">
                                    <td>
                                        <div class="text_wrap" style="margin-left: 0.6rem;font-size: 0.5rem !important; overflow-wrap: break-word !important; {{($boletin) ? 'font-weight: bolder;' : null}} ">
                                            {{$loop->iteration}}.- 
                                            @if ($grupo_estable) <strong>{{($grupo_estable) ? '['.$grupo_estable->name.']': null}}</strong> @endif 
                                            {{ Str::limit(strtoupper($evaluacion->description),120,'...') }}
                                        </div>
                                    </td>
                                    <td style="font-weight: bolder;font-size: 0.8rem !important;text-align:right">
                                        {{ $boletin->nota ?? ''}}
                                        {{-- {{ $pevaluacion->id ?? ''}} || {{ $evaluacion->id ?? ''}} || {{ $grupo_estable->id ?? ''}} || {{ $boletin->nota ?? ''}} --}}                                                                               
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if ($count == null)
                            <tr style="font-size:0.5rem">
                                <td colspan="2" style="text-align: right; color: #6c757d">SIN NOTAS REGISTRADAS</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </td>
        </tr>

    @endforeach

</tbody>
