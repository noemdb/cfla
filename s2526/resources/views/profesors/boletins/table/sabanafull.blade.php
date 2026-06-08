@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

@php
    $grado = $seccion->grado;
    // $pensums = $grado->pensums;
    $pestudio = $grado->pestudio;
    $escala = $pestudio->escala;
    $estudiants = (!empty($seccion))  ? $seccion->estudiants_in:null;
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}"></th>
            <th class="{{ $class_estudiant }}"></th>

            @foreach ($pensums as $pensum)
                <th colspan="4" class="{{ $class_estudiant }} text-center table-secondary border-right" title="{{$pensum->asignatura->name ?? ''}}">
                    {{$pensum->asignatura->code ?? ''}}
                </th>
            @endforeach

            <th class="{{ $class_grado }} text-center" title="Asignaturas Aplazadas">&nbsp;</th>
            <th class="{{ $class_estudiant }}">&nbsp;</th>
            <th class="{{ $class_estudiant }}">&nbsp;</th>

        </tr>

            <tr>
                <td class="{{ $class_N ?? '' }}">N</td>
                <td class="{{ $class_estudiant ?? '' }}">Estudiante</td>
                @foreach ($pensums as $pensum)
                    @foreach ($lapsos as $lapso)
                        <td class="{{ $class_estudiant ?? '' }} p-1 m-1 text-center">{{ $lapso->name ?? '' }}</td>
                    @endforeach
                    <th class="{{ $class_estudiant ?? '' }} p-1 m-1 table-secondary border-right text-center">DEFINITIVA</th>
                @endforeach

                @php
                    $pensum = $pensums->last();
                    $enable_academic_index = $pensum->asignatura->enable_academic_index;
                @endphp
                @if ($enable_academic_index == "false")
                    <th class="{{ $class_estudiant ?? '' }} text-right">PROMEDIO</th>
                @endif

                @if ($pestudio->status_baremo == "true")
                    <th class="{{ $class_estudiant ?? '' }} text-center">LITERAL</th>
                @endif
            </tr>

    </thead>

    <tbody id="tdatos">

        @foreach($estudiants as $estudiant)
            @php
                $sum_nota = null;
                $count_nota = null;
                $count_ar = null;
                $aprobacion = ($escala->aprobacion) ? : '';
                $decimal = 1;
                $promedio = $estudiant->getPromedioFinal($decimal) ;
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

                    @foreach ($lapsos as $lapso)
                        @php
                            $nota = $estudiant->getNota($lapso->id,$pensum->id);
                        @endphp

                        <td class="{{ $class_estudiant ?? ''}} table-{{($nota) ? 'default':'danger'}} p-1 m-1 text-center">
                            {{ ($nota) ? round($nota,0):''}}
                        </td>

                    @endforeach

                    @php $nota_final = $estudiant->getNotaFinal($pensum_id,0); @endphp

                    <th class="{{ $class_estudiant ?? ''}} table-secondary p-1 m-1 border-right text-center">

                        {{ $nota_final ?? null}}

                    </th>

                    @if ($nota < $aprobacion && !empty($nota )) @php $count_ar++; @endphp @endif

                @endforeach

                @php
                    $pensum = $pensums->last();
                    $enable_academic_index = $pensum->asignatura->enable_academic_index;
                @endphp
                @if ($enable_academic_index == "false")
                    @php $promedio_final = $estudiant->getPromedioFinalPensum($pensum->id,1); @endphp
                    <th class=" font-weight-bold {{ $class_estudiant ?? ''}} text-right">
                        <span class="pr-3">
                            {{ $promedio_final ?? ''}}
                        </span>
                    </th>
                @endif

                @if ($pestudio->status_baremo == "true")
                    <th class=" font-weight-bold {{ $class_estudiant ?? ''}} text-center">
                        <span class="pr-3">
                            {{ (!empty($promedio)) ? $baremo->getLiteral($grado->pestudio->id,$promedio) : null }}
                        </span>
                    </th>
                @endif
            </tr>

        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.simple') --}}
