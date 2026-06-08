@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Estudiante</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_profesor }}">Asignatura</th>
            <th class="{{ $class_profesor }}">Nota F.</th>
            <th class="{{ $class_profesor }}">Número</th>
            <th class="{{ $class_profesor }}">Nota Revisión</th>
            <th class="{{ $class_action }} text-center">&nbsp;</th>

        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($boletin_revisions as $boletin_revision)
            @php
                $estudiant = $boletin_revision->estudiant;
                $profesor = $boletin_revision->profesor;
                $pestudio = $estudiant->pestudio;
                $escala = $pestudio->escala;
                $aprobacion = ($escala->aprobacion) ? : '';
                $pensum = $boletin_revision->pensum;
                $asignatura = $pensum->asignatura;
                $nota = $estudiant->getNotaFinal($pensum->id);
                $nota_pf = (is_numeric($nota)) ? str_pad($nota, 2, "0", STR_PAD_LEFT) : $nota ;
            @endphp
            <tr data-id="{{$estudiant->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $profesor->fullname ?? ''}} <br>
                    <small class="text-muted">{{ $estudiant->ci_estudiant ?? ''}}</small>
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $profesor->fullname ?? ''}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $asignatura->fullname ?? ''}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $nota_pf ?? '' }}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $boletin_revision->numero ?? ''}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{ $boletin_revision->nota ?? ''}}
                </td>
                <td class="{{ $class_action ?? '' }}  text-right">
                    <div class="btn-group btn-group-sm">
                        @php $route = ($estudiant) ? route('administracion.boletin_revisions.edit',$boletin_revision->id) : "#"; @endphp
                        @include('elements.buttons.crud.default.edit',['route'=>$route])

                        @php $route = ($estudiant) ? route('administracion.boletin_revisions.create',$estudiant->id) : "#"; @endphp
                        @include('elements.buttons.crud.default.create',['route'=>$route,'title'=>'Registrar nueva revisión'])
                    </div>
                </td>

            </tr>

        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

