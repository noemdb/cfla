@php
    $class_N = '';
    $class_profesor = '';
    $class_asignatura = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = '';
@endphp

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
                $name = 'statusAtivities';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, ['SI' => 'SI', 'NO' => 'NO'], old($model), [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'id' => 'lapso_id',
                'placeholder' => 'Actividades registradas',
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

            <div class="btn-group">

                @if (isset($grado_id) && isset($seccion_id))
                    <a title="Plan de Actividades" class="btn btn-danger btn"
                        href="{{ route('evaluacions.activities.formats.grado', ['grado_id' => $grado_id, 'seccion_id' => $seccion_id]) }}"
                        role="button" target="_BLANK">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>
                @endif

                <a id="" class="btn btn-light" href="{{ route('evaluacions.activities.index') }}"
                    role="button" title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>

            </div>

        </div>
    </div>
</div>


<table width="100%" class="table table-sm small p-1" id="table-data-default">
    {{-- <caption style="caption-side: top-right">Listado de Áreas de Formación...</caption> --}}
    <thead>
        <tr>
            <th>N</th>
            <th>Asignatura/Grado/Sección</th>
            <th>Cant.Act.</th>
            <th>Lapso</th>
            <th>Actividades</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($pevaluacions as $item)
            @php
                $profesor = $item->profesor;
                $activities = $item->activities->sortBy('finicial');
                $pensum = $item->pensum;
                $grado = $item->pensum->grado;
                $pensum = $item->pensum;
            @endphp

            <tr class="" row-id="data-{{ $item->id }}">
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    {{ $item->pensum->asignatura->name ?? '' }}
                    <div class="font-weight-bold">{{ $grado->name ?? '' }} {{ $item->seccion->name ?? '' }}</div>
                    <div>{{ $profesor->fullname }}</div>
                </td>
                <td>
                    {{ $activities->count() }}
                </td>
                <td>
                    {{ $item->lapso->name ?? '' }}
                </td>

                <td>

                    @php $activities = $item->activities; @endphp
                    @if ($activities->count())
                        <ul class="nav nav-tabs nav-fill" id="myTab{{ $item->id }}" role="tablist">
                            @foreach ($activities as $subItem)
                                @php $id="nav-tab-".$item->id."-".$subItem->id; @endphp
                                @php $content="contentTab-".$item->id."-".$subItem->id; @endphp
                                <li class="nav-item">
                                    <a class="nav-link px-2 font-weight-bold {{ $loop->first ? 'active' : null }} {{ $subItem->status ? 'text-success' : 'text-warning' }}"
                                        id="{{ $subItem->id }}" data-toggle="tab" href="#{{ $content }}"
                                        role="tab" aria-controls="nav-tab" aria-selected="true">
                                        {{ $loop->iteration }}
                                        @if ($subItem->status)
                                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content border border-top-0" id="myTabContent">

                            @forelse ($activities as $subItem)
                                @php $id="nav-tab-".$item->id."-".$subItem->id; @endphp
                                @php $content="contentTab-".$item->id."-".$subItem->id; @endphp

                                <div class="tab-pane pt-2 px-2 fade {{ $loop->first ? 'show active' : null }}"
                                    id="{{ $content }}" role="tabpanel" aria-labelledby="{{ $id }}">
                                    <div class="d-flex justify-content-between pt-2">
                                        <div class="align-self-center px-2 font-weight-bold">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="">
                                                <div class=""><strong>T.Generador/Énfasis:</strong>
                                                    {{ $subItem->topic }}</div>
                                                <div class=""><strong>T.Temático/T.Indispensable:</strong>
                                                    {{ $subItem->thematic }}</div>
                                                <div class=""><strong>Referentes/Ético</strong>
                                                    {{ $subItem->references }}</div>
                                                <div class=""><strong>Comentario [Jef.Área]:</strong>
                                                    {{ $subItem->comments }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div>No hay datos</div>
                            @endforelse

                        </div>
                    @else
                        <div>No hay datos</div>
                    @endif
                </td>

                <td>
                    {{ $item->observations ?? '' }}
                </td>

                <td>

                    <div class="btn-group">

                        <a wire:click="createObservation({{ $item->id }})" title="Registrar Observaciones"
                            class="btn btn-primary btn-sm" href="#" role="button">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </a>

                        <a title="Resumen del Plan de Actividades" class="btn btn-info"
                            href="{{ route('evaluacions.activities.resume', $item->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Plan de Actividades" class="btn btn-success"
                            href="{{ route('evaluacions.activities.format', $item->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

{{ $pevaluacions->links() }}

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('evaluacions.datatables.default') --}}
