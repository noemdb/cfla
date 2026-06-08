@php
    $class_N = 'd-none d-sm-table-cell';
    $class_profesor = '';
    $class_asignatura = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = '';
    $user = Auth::user();
@endphp

{!! Form::open(['route' => 'academicos.activities.index', 'method' => 'GET', 'class' => '', 'role' => 'search']) !!}
<div class="border-bottom mb-2 pb-2">
    <div class="d-flex">
        <div class="px-2">
            @php
                $name = 'grado_id';
                $model = '' . $name;
            @endphp
            {!! Form::select($model, $list_grado, $grado_id, [
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
            {!! Form::select($model, $list_seccion, $seccion_id, [
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
            {!! Form::select($model, $list_lapso, $lapso_id, [
                'wire:model' => $model,
                'class' => 'form-control px-1 mx-1',
                'id' => 'lapso_id',
                'placeholder' => 'Momento',
            ]) !!}
        </div>
        <div class="px-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <a id="" class="btn btn-light px-1 mx-1" href="{{ route('leaders.activities.index') }}"
                    role="button" title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                <div class="input-group-append" style="z-index: 0;">
                    <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<table width="100%" class="table table-striped table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado/Sección</th>
            <th class="{{ $class_lapso }}">Lapso</th>
            <th class="{{ $class_lapso }}">Actividades</th>
            {{-- <th class="{{ $class_lapso }}">Obs.[Coord.Eval.]</th> --}}
            <th class="{{ $class_action }}">Acciones</th>
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
                    @if ($pevaluacion->observations)
                        <div>Obs.[Coord.Eval.]: {{ $pevaluacion->observations ?? '' }}</div>
                    @endif

                    @php $activities = $pevaluacion->activities; @endphp

                    @if ($activities->count())
                        <ul class="nav nav-tabs nav-fill" id="myTab{{ $pevaluacion->id }}" role="tablist">
                            @foreach ($activities as $item)
                                <li class="nav-item">
                                    <a class="nav-link px-2 {{ $item->status ? 'text-success' : 'text-warning' }} {{ $loop->first ? 'active' : null }}"
                                        id="nav-tab-{{ $item->id }}" data-toggle="tab"
                                        href="#contentTab{{ $item->id }}" role="tab" aria-controls="nav-tab"
                                        aria-selected="true">
                                        <div class="d-flex justify-content-center">
                                            <div>{{ $loop->iteration }}</div>
                                            <div class="px-2">
                                                <span class="font-weight-light text-right">
                                                    @if ($item->status)
                                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                                    @else
                                                        <i class="fa fa-info text-warning" aria-hidden="true"></i>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
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
                                        <div class=""><strong>T.Generador/Énfasis:</strong> {{ $item->topic }}
                                        </div>
                                        <div class=""><strong>T.Temático/T.Indispensable:</strong>
                                            {{ $item->thematic }}</div>
                                        <div class=""><strong>Referentes/Ético</strong> {{ $item->references }}
                                        </div>

                                        <div class="{{ $item->status ? 'table-success' : 'table-warning' }}">
                                            <div class="d-flex justify-content-between">
                                                <div class=" flex-grow-1">
                                                    <strong>Comentario [Jef.Área]:</strong> {{ $item->comments }} <span
                                                        class="font-weight-bold {{ $item->status ? 'text-success' : 'text-warning' }}">{{ $item->status ? 'Aprobado' : 'En revisión' }}</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>
                    @endif

                </td>

                {{-- <td>
                    {{ $pevaluacion->observations ?? ''}}
                </td> --}}

                <td>
                    <div class="btn-group-vertical">

                        <a title="Resumen del Plan de Actividades" class="btn btn-info"
                            href="{{ route('academicos.activities.resume', $pevaluacion->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Plan de Actividades" class="btn btn-success"
                            href="{{ route('academicos.activities.format', $pevaluacion->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

{{-- partials contentivo de los scripts datatables --}}
@include('academicos.datatables.default')
