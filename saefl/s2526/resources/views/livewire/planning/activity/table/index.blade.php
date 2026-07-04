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
                    'options' => ['10' => '10', '20' => '20', '50' => '50', '100' => '100', '9999' => 'Todos'],
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

        @if ($profesor_id)
            @php
                $__total  = 0;
                $__above  = 0;
                foreach ($pevaluacions as $__pev) {
                    foreach ($__pev->activities as $__act) {
                        $__avr = $__act->activities_avr;
                        if (is_null($__avr)) continue;
                        $__total++;
                        if ($__act->teachingWordsMayorCount() > $__avr) $__above++;
                    }
                }
                $__pct = ($__total > 0) ? round(100 * $__above / $__total, 1) : null;
            @endphp

            @if (!is_null($__pct))
                @php
                    $__color = $__pct >= 50 ? 'success' : ($__pct >= 25 ? 'warning' : 'danger');
                    $__icon  = $__pct >= 50 ? 'fa-check-circle' : ($__pct >= 25 ? 'fa-exclamation-circle' : 'fa-times-circle');
                    $__msg   = $__pct >= 50
                        ? 'Buen desempeño: la mayoría de las actividades superan el promedio de palabras esperado.'
                        : ($__pct >= 25
                            ? 'Desempeño moderado: una parte de las actividades alcanza el promedio.'
                            : 'Atención: pocas actividades superan el promedio de palabras esperado.');
                @endphp
                <div class="w-100 px-2 mt-2">
                    <div class="alert alert-info mb-0 py-2 small border-left border-info" style="border-left-width:4px!important;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-chart-bar fa-lg mr-2 mt-1 text-info flex-shrink-0"></i>
                            <div class="flex-grow-1">
                                <strong>Indicador de Planificación — Actividades sobre el Promedio</strong><br>
                                <span class="text-muted">
                                    De <strong>{{ $__total }}</strong> actividades del profesor seleccionado,
                                    <strong>{{ $__above }}</strong> superan el promedio de palabras significativas
                                    <em>(palabras &gt; 3 letras)</em> configurado en el Plan de Estudio
                                    <!-- <code>(activities_avr)</code>. -->
                                </span>
                                <div class="mt-1 text-{{ $__color }} font-weight-bold">
                                    <i class="fa {{ $__icon }}" aria-hidden="true"></i>
                                    {{ $__msg }}
                                </div>
                            </div>
                            <div class="ml-3 text-center flex-shrink-0">
                                <span class="badge badge-{{ $__color }} px-3 py-2" style="font-size:1.1rem;">
                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                    {{ $__pct }}%
                                </span>
                                <div class="text-muted mt-1" style="font-size:.7rem;">
                                    {{ $__above }}&nbsp;/&nbsp;{{ $__total }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
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

                <td>

                    @php $show = ($modeObservation && $item->id == $pevaluacion_id) ? : false @endphp
                    @includeWhen($show, 'livewire.planning.activity.partials.observation')

                    @if ($activities_count > 0)
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                            @foreach ($activities as $subItem)
                                @php
                                    $avr        = $subItem->activities_avr;
                                    $wordCount  = $subItem->teachingWordsMayorCount();

                                    if (!is_null($avr)) {
                                        if ($wordCount < $avr) {
                                            $avrIcon      = 'fa fa-arrow-down';
                                            $avrBadgeCss  = 'badge badge-warning';
                                            $avrBorderCss = 'border-warning';
                                            $avrTitle     = "Palabras ({$wordCount}) por debajo del promedio ({$avr})";
                                        } elseif ($wordCount == $avr) {
                                            $avrIcon      = 'fa fa-minus';
                                            $avrBadgeCss  = 'badge badge-primary';
                                            $avrBorderCss = 'border-primary';
                                            $avrTitle     = "Palabras ({$wordCount}) igual al promedio ({$avr})";
                                        } else {
                                            $avrIcon      = 'fa fa-arrow-up';
                                            $avrBadgeCss  = 'badge badge-success';
                                            $avrBorderCss = 'border-success';
                                            $avrTitle     = "Palabras ({$wordCount}) por encima del promedio ({$avr})";
                                        }
                                    } else {
                                        $avrIcon = $avrBadgeCss = $avrBorderCss = $avrTitle = null;
                                    }
                                @endphp
                                <li class="nav-item position-relative" title="{{$wordCount}} Palabras GT3">
                                    @if ($avrBadgeCss)
                                        <span class="{{ $avrBadgeCss }} position-absolute"
                                              style="top:2px;right:2px;font-size:.65rem;z-index:1;"
                                              title="{{ $avrTitle }}">
                                            <i class="{{ $avrIcon }}" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                    <a class="nav-link px-2 font-weight-bold
                                            {{ $loop->first ? 'active' : null }}
                                            {{ $subItem->status ? 'text-success' : 'text-warning' }}"
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
                            {{-- Botón Nueva Actividad en la navbar de tabs --}}
                            {{-- <li class="nav-item ml-auto">
                                <a class="btn btn-info btn-sm text-white font-weight-bold px-2"
                                   href="#" wire:click.prevent="setCreate({{ $item->id }})"
                                   title="Nueva Actividad para {{ $asignatura->name ?? '' }}">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                            </li> --}}
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
                                        {{-- Acciones CRUD por actividad --}}
                                        {{-- <div class="align-self-start px-2 flex-shrink-0">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info btn-sm" wire:click.prevent="setShow({{ $subItem->id }})" title="Ver detalle">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm" wire:click.prevent="setEditAct({{ $subItem->id }})" title="Editar actividad">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" wire:click.prevent="deleteAct({{ $subItem->id }})" title="Eliminar actividad"
                                                    onclick="return confirm('¿Eliminar esta actividad? Se eliminarán también los indicadores de logro asociados.') || event.stopImmediatePropagation()">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-between p-2">
                            <span class="text-muted"><i class="fa fa-info-circle mr-1"></i> No hay actividades registradas para esta planificación.</span>
                            <button class="btn btn-sm btn-success" wire:click.prevent="setCreate({{ $item->id }})" title="Crear primera actividad">
                                <i class="fa fa-plus mr-1"></i> Nueva Actividad
                            </button>
                        </div>
                    @endif

                    @if ($item->observations)
                        <div class=" font-weight-bold text-muted">Observaciones [Coord. Evaluación]:
                            <span>{{ $item->observations ?? '' }}</span>
                        </div>
                    @else
                    @endif

                </td>

                <td>

                    <div class="btn-group">

                        <a wire:click.prevent="createObservation({{ $item->id }})" title="Registrar Observaciones"
                            class="btn btn-primary btn-sm" href="#" role="button">
                            <i class="fa fa-undo" aria-hidden="true"></i>
                        </a>

                        <a title="Resumen del Plan de Actividades" class="btn btn-info"
                            href="{{ route('plannings.activities.resume', $item->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Plan de Actividades" class="btn btn-success"
                            href="{{ route('plannings.activities.format', $item->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

{{-- {{ $pevaluacions->links() }} --}}

{{ $pevaluacions->onEachSide(1)->links('pagination::bootstrap-4') }}
