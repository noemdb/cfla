@php
    $class_N = 'd-none d-sm-table-cell';
    $class_plan = 'd-none d-md-table-cell';
    $class_descripcion = '';
    $class_profesor = 'd-none d-md-table-cell';
    $class_asignatura = '';
    $class_grado = 'd-none d-lg-table-cell';
    $class_lapso = 'd-none d-lg-table-cell';
    $class_action = 'nosort';
@endphp

<div class="border-bottom mb-2 pb-2">
    <div class="d-flex">
        <div class="px-2 flex-grow-1">
            {!! Form::text('name', $name, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'name',
                'placeholder' => 'Nombre del Profesor',
            ]) !!}
        </div>
        <div class="px-2 flex-grow-1">
            {!! Form::text('search', $search, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'search',
                'placeholder' => 'Nombre de la Asignatura',
            ]) !!}
        </div>
        <div class="px-2">
            @php
                $name = 'grado_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_grado, old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'placeholder' => 'Grado/Año',
            ]) !!}
        </div>
        <div class="px-2">
            @php
                $name = 'seccion_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_seccion, old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'placeholder' => 'Sección',
            ]) !!}
        </div>
        <div class="px-2">
            @php
                $name = 'lapso_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_lapso, old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'id' => 'lapso_id',
                'placeholder' => 'Momento',
            ]) !!}
        </div>
        <div class="px-2">
            <a id="" class="btn btn-light px-1 mx-1" href="{{ route('leaders.activities.index') }}"
                role="button" title="Refrescar la página">
                <i class="fas fa-redo" aria-hidden="true"></i>
            </a>
            <div wire:loading class="text-muted small fw-bold">
                Procesando...
            </div>
        </div>
    </div>
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_descripcion }}">Descripción</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado/Sección/Lapso</th>
            <th class="{{ $class_lapso }}">Fecha</th>
            <th class="{{ $class_lapso }}">Notas</th>
            <th class="{{ $class_lapso }}">Promedio</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($evaluacions as $evaluacion)
            @php $pevaluacion = $evaluacion->pevaluacion; @endphp
            @php $pensum = ($pevaluacion) ? $pevaluacion->pensum : null; @endphp
            @php $asignatura = ($pensum) ? $pensum->asignatura : null; @endphp
            @php $profesor = ($pevaluacion) ? $pevaluacion->profesor : null; @endphp
            @php $grado = ($pensum) ? $pensum->grado : null; @endphp
            @php $seccion = ($pevaluacion) ? $pevaluacion->seccion : null; @endphp
            @php $lapso = ($pevaluacion) ? $pevaluacion->lapso : null; @endphp


            @php $status_active = (!empty($profesor)) ? $profesor->status_active:null; @endphp
            @php $notas_count = (!empty($evaluacion->notas_count)) ? $evaluacion->notas_count:null; @endphp
            @php $pevaluacion = (!empty($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion:null; @endphp

            <tr data-id="{{ $evaluacion->id }}" data-evaluacion="{{ $evaluacion->id ?? '' }}"
                class="table-{{ empty($notas_count) ? 'danger' : 'default' }}">
                <td class="{{ $class_N }}">
                    {{ $loop->iteration }}
                    @php $overlay = ($modeEdit && $evaluacion->id == $evaluacion_id ) ? true : false @endphp
                    @includeWhen($overlay, 'livewire.leader.form.evaluacion.edit')
                </td>
                <td class=" text-wrap {{ $class_descripcion ?? '' }} text-uppercase"
                    title="{{ $evaluacion->description ?? '' }}">
                    {{ $evaluacion->description ?? '' }}
                </td>

                <td class="{{ $class_profesor ?? '' }} text-{{ $status_active == 'false' ? 'secondary' : 'dark' }}"
                    title="{{ $profesor->fullname ?? '' }}">
                    {{ !empty($profesor->id) ? $profesor->fullname : null }}
                </td>
                <td class=" text-nowrap {{ $class_asignatura ?? '' }}"
                    title="{{ $evaluacion->pevaluacion->pensum->asignatura->name ?? '' }}">
                    {{ $asignatura ? Str::limit($asignatura->code, 15, '...') : null }}
                    <div class="text-muted small">
                        {{ $asignatura ? $asignatura->name : null }}
                    </div>
                </td>
                <td class=" text-nowrap {{ $class_grado ?? '' }}">
                    {{ $grado->name ?? '' }}
                    {{ $seccion->name ?? '' }} ||
                    {{ $lapso->name ?? '' }}
                </td>
                <td class=" text-nowrap {{ $class_lapso ?? '' }}">
                    {{ f_date($evaluacion->fecha) ?? '' }}
                </td>
                <td class="{{ $class_lapso ?? '' }}">
                    {{ $notas_count ?? '' }}
                </td>

                <td class="{{ $class_lapso ?? '' }}">
                    {{ $evaluacion->promedio ?? '' }}
                </td>

                <td>
                    <div class="btn-group">

                        <a title="Editar Indicador" class="btn btn-warning" href="#" role="button"
                            wire:click="edit({{ $evaluacion->id }})">
                            <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

{{ $evaluacions->links() }}
