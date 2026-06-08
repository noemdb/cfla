@php
    $class_N = 'd-none d-sm-table-cell';
    $class_profesor = '';
    $class_asignatura = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = '';
    $user = Auth::user();
    $rol = $user ? $user->rols->where('area', $area_active)->first() : null; //dd($rols);
    $group = $rol ? $rol->group : null; //dd($group);
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

<table width="100%" class="table table-striped table-sm small p-1">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado/Sección</th>
            <th class="{{ $class_lapso }}">Lapso</th>
            <th class="{{ $class_lapso }}">Actividades</th>
            <th class="{{ $class_lapso }}">Obs.[Coord.Eval.]</th>
            @if ($group == 'imployeds')
                <th class="{{ $class_action }}">Acciones</th>
            @endif
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($pevaluacions as $pevaluacion)
            @php
                $profesor = $pevaluacion->profesor;
                $pensum = $pevaluacion->pensum;
                $grado = $pevaluacion->pensum->grado;
                $pensum = $pevaluacion->pensum;
            @endphp

            <tr data-id="{{ $pevaluacion->id }}" data-pevaluacion="{{ $pevaluacion->id ?? '' }}"
                class="table-{{ empty($pevaluacion->administrativa->id) ? 'default' : 'success' }}">
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    {{ $pevaluacion->pensum->asignatura->name ?? '' }}
                    <div class="text-muted">{{ $profesor->fullname ?? '' }}</div>

                </td>
                <td>
                    {{ $grado->name ?? '' }} {{ $pevaluacion->seccion->name ?? '' }}
                </td>
                <td>
                    {{ $pevaluacion->lapso->name ?? '' }}
                </td>

                <td>
                    @php $activities = $pevaluacion->activities; @endphp

                    @if ($activities->count())
                        <ul class="nav nav-tabs nav-fill" id="myTab{{ $pevaluacion->id }}" role="tablist">
                            @foreach ($activities as $subItem)
                                <li class="nav-item">
                                    <a class="nav-link px-2 font-weight-bold {{ $loop->first ? 'active' : null }} {{ $subItem->status ? 'text-success' : 'text-warning' }}"
                                        id="nav-tab-{{ $subItem->id }}" data-toggle="tab"
                                        href="#contentTab{{ $subItem->id }}" role="tab" aria-controls="nav-tab"
                                        aria-selected="true">
                                        {{ $loop->iteration }}
                                        @if ($subItem->status)
                                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content border border-top-0" id="myTabContent">

                            @foreach ($activities as $item)
                                @php $key = Str::random().$item->id @endphp

                                <div class="tab-pane pt-2 px-2 fade table-light {{ $loop->first ? 'show active' : null }}"
                                    id="contentTab{{ $item->id }}" role="tabpanel"
                                    aria-labelledby="nav-tab-{{ $item->id }}">

                                    <div
                                        class="p-2 bd-callout {{ $item->status_resume ? 'bd-callout-success' : 'bd-callout-warning' }} p-2">
                                        <livewire:leader.comment-component :activity_id="$item->id" :wire:key="$key" />
                                    </div>

                                </div>
                            @endforeach

                        </div>
                    @endif

                </td>

                <td>
                    {{ $pevaluacion->observations ?? '' }}
                </td>

                @if ($group == 'imployeds')
                    <td>
                        <div class="btn-group">

                            <a title="Resumen del Plan de Actividades" class="btn btn-info"
                                href="{{ route('leaders.activities.resume', $pevaluacion->id) }}" role="button"
                                target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>
                            <a title="Plan de Actividades" class="btn btn-success"
                                href="{{ route('leaders.activities.format', $pevaluacion->id) }}" role="button"
                                target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>
                    </td>
                @endif

            </tr>
        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('evaluacions.datatables.default') --}}
