<div class="border-bottom mb-2 pb-2">
    <div class="d-flex justify-content-between">
        <div class="px-2">
            @php
                $name = 'pestudio_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_pestudio, old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'placeholder' => 'Planes de Estudio',
            ]) !!}
        </div>

        <div class="px-2">
            @php
                $name = 'profesor_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_profesor, old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'placeholder' => 'Profesor',
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
            @php
                $name = 'paginate';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, ['10' => '10', '20' => '20', '50' => '50', '100' => '100'], $paginate, [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="px-2">
            <a id="" class="btn btn-light px-1 mx-1" href="{{ route('evaluacions.pevaluacions.index') }}"
                role="button" title="Refrescar la página">
                <i class="fas fa-redo" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>

@php
    $class_N = '';
    $class_profesor = '';
    $class_asignatura = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = '';
@endphp

<table width="100%" class="table table-striped table-sm table-hover small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Plan de Estudio</th>
            <th class="{{ $class_asignatura }}">Asignatura/Grado/Sección/Momento</th>
            <th class="{{ $class_lapso }}">Actividades</th>
            <th class="{{ $class_lapso }}">Profesor</th>
            <th class="{{ $class_lapso }}">Descripción</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($pevaluacions as $item)
            @php
                $pestudio = $item->pestudio;
                $activities = $item->activities;
                $profesor = $item->profesor;
                $pensum = $item->pensum;
                $grado = $item->pensum->grado;
                $seccion = $item->seccion;
                $lapso = $item->lapso;
            @endphp

            <tr data-id="{{ $item->id }}" data-pevaluacion="{{ $item->id ?? '' }}"
                class="table-{{ empty($item->administrativa->id) ? 'default' : 'success' }}">
                <td class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>

                <td class="{{ $class_lapso ?? '' }} ">
                    {{ $pestudio->name }}
                </td>

                <td class="{{ $class_asignatura ?? '' }}">
                    {{ $item->pensum->asignatura->name ?? '' }}
                    <div class="font-weight-bold"> <span
                            class="{{ $grado->class_text_color }}">{{ $grado->name ?? '' }}
                            {{ $seccion->name ?? '' }}</span> [{{ $lapso->name ?? '' }}]</div>
                </td>
                <td class="{{ $class_lapso ?? '' }} ">
                    {{ $activities->count() }}
                </td>

                <td class="{{ $class_lapso ?? '' }}">
                    {{ $profesor->fullname }}
                </td>

                <td class="{{ $class_lapso ?? '' }}">
                    {{ $item->description ?? '' }}
                </td>

                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group">

                        <button title="Editar Plan de Evaluación" type="button" class="btn btn-warning btn-sm"
                            wire:click="edit({{ $item->id }})">
                            <i class="{{ $icon_menus['edit'] ?? '' }}" aria-hidden="true"></i>
                        </button>

                        @php $disabled = ($activities->count()) ? 'disabled':false ; @endphp
                        <button title="Eliminar Plan de Evaluación" {{ $disabled ?? null }} type="button"
                            class="btn btn-danger btn-sm" wire:click="delete({{ $item->id }})">
                            <i class="{{ $icon_menus['eliminar'] ?? '' }}" aria-hidden="true"></i>
                        </button>


                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

{{ $pevaluacions->links() }}

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('evaluacions.datatables.default') --}}
