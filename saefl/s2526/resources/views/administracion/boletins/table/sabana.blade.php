@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>

                @foreach ($pensums as $pensum)
                    <th class="{{ $class_estudiant }} small" title="{{$pensum->asignatura->name ?? ''}}">
                        {{$pensum->asignatura->code ?? ''}}
                    </th>
                @endforeach

                <th class="{{ $class_grado }} text-center" title="Asignaturas Aplazadas">AR</th>
                <th class="{{ $class_estudiant }} text-right">PROMEDIO</th>

            </tr>
        </thead>

        <tbody id="tdatos">
        @php $sumPromedios = null; @endphp
        @foreach($estudiants as $estudiant)
            @php
                $sum_nota = null;
                $count_nota = null;
                $count_ar = null;
                $aprobacion = ($escala->aprobacion) ? : '';
                $decimal = 2;
                // $promedio = $estudiant->getNotaFinalLapso($lapso->id,$decimal) ;
                $promedio = ($lapso) ? $estudiant->getNotaFinalLapso($lapso->id,$decimal) : $estudiant->getPromedioFinalLapso(1) ;
                $sumPromedios += $promedio;
            @endphp

            <tr data-id="{{$estudiant->id}}" class=" table-{{ ($promedio > $escala->maximo) ? 'danger':'default'}}">

                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration }}
                </td>

                <td class="{{ $class_user  ?? ''}}">
                    {{$estudiant->fullname }}
                </td>

                @foreach ($pensums as $pensum)

                    @php $pensum_id = $pensum->id; @endphp

                    @if ($lapso)
                        @php
                            $pevaluacion = $pensum->pevaluacions->where('seccion_id',$seccion_id)->where('lapso_id',$lapso->id)->first();
                            $nota = $estudiant->getNota($lapso->id,$pensum->id);
                            $ajuste = (!empty($estudiant->getAjuste($pevaluacion->id))) ? $estudiant->getAjuste($pevaluacion->id):null;
                        @endphp
                    @else
                        @php
                            $nota = $estudiant->getNotaFinal($pensum_id,0);
                            $ajuste = $estudiant->getAllAjuste($pensum_id) ;
                        @endphp
                    @endif

                    <td class="{{ $class_estudiant ?? ''}} {{($nota) ? 'default':'danger'}}">

                        <span style="{{ ($ajuste) ? ' font-style: italic; text-decoration: underline; ':null }}" >
                            {{-- {{ ($nota) ? round($nota,0):'0'}} --}}
                            {{ $nota }}
                        </span>

                        @if ($ajuste)
                            <span title="Punto de ajuste" class="text-success font-weight-bold">
                                {{ ($ajuste) ? '['.$ajuste.']':''}}
                            </span>
                        @endif

                    </td>

                    @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

                @endforeach

                <td class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center text-danger">
                    {{ $count_ar ?? '' }}
                </td>
                <td class=" font-weight-bold {{ $class_estudiant ?? ''}} text-right">
                    <span class="pr-3">
                        {{ $promedio ?? ''}}
                    </span>
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @if ($estudiants->count())
        @php $promedioSeccion = ($estudiants->count()) ? round ( ($sumPromedios / $estudiants->count() ) , 2) : null; @endphp
        <div class="alert alert-secondary" role="alert">
            <div class="d-flex font-weight-bold">
                <div class="flex-fill text-right">Promedio</div>
                <div class="flex-fill text-right">{{ $promedioSeccion ?? null }}</div>
            </div>
        </div>
    @endif


    {{-- partials contentivo de los scripts datatables --}}
    @include('administracion.datatables.simple_search')
