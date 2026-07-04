<div class="border-bottom mb-2 pb-2">
    <div class="d-flex flex-wrap">
        @php
            $fields = [
                'pestudio_id' => ['options' => $list_pestudio, 'placeholder' => 'Planes de Estudio'],
                'profesor_id' => ['options' => $list_profesors, 'placeholder' => 'Profesor'],
                'grado_id' => ['options' => $list_grado, 'placeholder' => 'Grado/Año'],
                'seccion_id' => ['options' => $list_seccion, 'placeholder' => 'Sección'],
                'lapso_id' => ['options' => $list_lapso, 'placeholder' => 'Momento', 'id' => 'lapso_id'],
                'status_activities' => ['options' => ['SI' => 'SI', 'NO' => 'NO'], 'placeholder' => 'Actividades'],
                'paginate' => [
                    'options' => ['10' => '10', '20' => '20', '50' => '50', '100' => '100'],
                    'placeholder' => 'Seleccione',
                ],
                // 'pensum_id' => ['options' => $list_pensum, 'placeholder' => 'Área de Formación', 'id' => 'pensum_id'],
            ];
        @endphp

        @foreach ($fields as $name => $field)
            <div class="p-2 flex-grow-1">
                @php $model = $name @endphp
                {!! Form::select(
                    $model,
                    $field['options'],
                    old($model, $name === 'paginate' ? $paginate : null),
                    array_merge(
                        ['wire:model' => $model, 'class' => 'form-control w-100', 'placeholder' => $field['placeholder']],
                        isset($field['id']) ? ['id' => $field['id']] : [],
                    ),
                ) !!}
            </div>
        @endforeach

        <div class="px-2">
            <a id="" class="btn btn-dark w-100 p-2 mt-1" href="{{ route('plannings.activities.index') }}"
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

<table width="100%" class="table table-striped table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>N</th>
            <th>Asignatura/Grado/Sección</th>
            <th>Cant.Act.</th>
            {{-- <th>Lapso</th> --}}
            <th>Actividades</th>
            {{-- <th>Observaciones</th> --}}
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($pevaluacions as $item)
            @php
                $profesor = $item->profesor;
                $activities = $item->activities;
                $pensum = $item->pensum;
                $grado = $item->pensum->grado;
                $activities_count = $item->activities_count;
                $activities = $item->activities;
                $asignatura = $item->asignatura;
                $seccion = $item->seccion;
                $lapso = $item->lapso;
                $colorClass = $grado->class_card_color;
            @endphp
            <tr>

                <td>
                    {{ $loop->iteration }}

                </td>

                <td>
                    <div>
                        <div>{{ $asignatura->name ?? '' }}</div>
                        <div class="font-weight-bold"> <span
                                class="{{ $grado->class_text_color }}">{{ $grado->name ?? '' }}
                                {{ $seccion->name ?? '' }}</span> [{{ $lapso->name ?? '' }}]</div>
                    </div>
                </td>

                <td class="table-{{ $activities_count == 0 ? 'danger' : null }}">
                    {{ $activities_count }}
                </td>

                {{-- <td> {{ $item->lapso->name ?? ''}} </td> --}}

                <td>

                    @php $show = ($modeObservation && $item->id == $pevaluacion_id) ? : false @endphp
                    @includeWhen($show, 'livewire.planning.activity.partials.observation')

                    @if ($activities_count > 0)
                        <ul class="nav nav-tabs nav-fill" role="tablist">
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

                        <div class="tab-content border border-top-0">

                            @foreach ($activities as $subItem)
                                <div class="tab-pane pt-2 px-2 fade {{ $loop->first ? 'show active' : null }}"
                                    id="contentTab{{ $subItem->id }}" role="tabpanel"
                                    aria-labelledby="nav-tab-{{ $subItem->id }}">
                                    <div class="d-flex justify-content-between pt-2">
                                        <div class="align-self-center px-2 font-weight-bold">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="">

                                                @php $key = Str::random().$subItem->id @endphp
                                                <div class="">
                                                    <livewire:leader.comment-component :activity_id="$subItem->id"
                                                        :wire:key="$key" />
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <div>No hay datos de actividades.</div>
                    @endif

                    @if ($item->observations)
                        <div class=" font-weight-bold text-muted">Observaciones [Coord. Evaluación]:
                            <span>{{ $item->observations ?? '' }}</span></div>
                    @else
                    @endif

                </td>

                {{-- <td>
                {{ $item->observations ?? ''}}
            </td> --}}

                <td>

                    <div class="btn-group">

                        <a wire:click.prevent="createObservation({{ $item->id }})" title="Registrar Observaciones"
                            class="btn btn-primary btn-sm" href="#" role="button">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </a>

                        <a title="Resumen del Plan de Actividades" class="btn btn-info"
                            href="{{ route('plannings.activities.resume', $item->id) }}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Plan de Actividades" class="btn btn-success"
                            href="{{ route('plannings.activities.format', $item->id) }}" role="button" target="_BLANK">
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
