<div>

    <div class="alert alert-warning">
        <div class="d-flex justify-content-between">
            <div class="flex-grow-1 pl-2">
                <div class="h5 font-weight-bold">Observación [Coord.Eval.]</div>
                @if ($pevaluacion->observations)
                    <div class="">
                        <div class="text-muted">{{ $pevaluacion->observations ?? null }}</div>
                    </div>
                @else
                    <div class="text-muted">No hay observaciones registradas.</div>
                @endif
            </div>
            <div>
                <div class="btn-group">
                    <a title="Resumen del Plan de Actividades" class="btn btn-info btn-sm"
                        href="{{ route('profesors.activities.resume', $pevaluacion->id) }}" role="button"
                        target="_BLANK">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>
                    <a title="Plan de Actividades" class="btn btn-success btn-sm"
                        href="{{ route('profesors.activities.format', $pevaluacion->id) }}" role="button"
                        target="_BLANK">
                        <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                    </a>
                    @php $disabled = (! empty($pevaluacion->observations || ! $enable_edit)) ? 'disabled' : null @endphp
                    <a wire:click="emptyActivities({{ $pevaluacion->id }})"
                        title="Eliminar todas las Actividades de este plan de evaluaciópn"
                        class="ml-3 btn btn-danger btn-sm {{ $disabled }}" href="#" role="button">
                        <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                    </a>

                    <span class="input-text px-4 font-weight-bold" wire:loading>
                        <strong>Procesando...</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    @includeWhen($modeCreator, 'livewire.profesor.activity.partials.create')

    <ul class="list-group">

        <li class="list-group-item list-group-item-secondary">

            <div class="d-flex justify-content-between">

                <div class="flex-grow-1">
                    <h4 class="mb-0">Actividades registradas</h4>
                </div>

                <div class="px-2">
                    <div class="input-group">
                        @if ($activities->isEmpty())
                            {!! Form::select('seccion_id', $list_seccions, old('seccion_id'), [
                                'wire:model.defer' => 'seccion_id',
                                'class' => 'custom-select',
                                'id' => 'seccion_id',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                            <div class="input-group-append" title="Exportar Actividades">
                                <button class="btn btn-outline-secondary" {{ $enable_edit ? null : 'disabled' }}
                                    type="button" wire:click="clone({{ $pevaluacion->id }})">
                                    <i class="{{ $icon_menus['clone'] ?? '' }}" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif

                        <button type="button" class="btn btn-primary btn-sm ml-2"
                            {{ $enable_edit ? null : 'disabled' }} wire:click="setCreate()"
                            title="Registrar nueva actividad">
                            <i class="{{ $icon_menus['nuevo'] ?? '' }}" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

            </div>

        </li>

        @forelse ($activities as $item)

            @php $achievements = $item->achievements @endphp

            <li class="list-group-item {{ $item->id == $activity_id ? 'font-weight-bold' : null }}">
                @php $modeOverlay = ( $modeEdit && $activity_id == $item->id ) ? : false @endphp
                @includeWhen($modeOverlay, 'livewire.profesor.activity.partials.create')

                <div
                    class="d-flex bd-callout {{ $item->status_resume ? 'bd-callout-success' : 'bd-callout-warning' }} p-2">
                    <div class="pr-2 align-content-center border-right">

                        <span class="h5 font-weight-bold">{{ $loop->iteration }}</span>
                    </div>
                    <div class="flex-grow-1 pl-2">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1">
                                @if ($item->status_resume)
                                    <div class="text-success p-1 rounded font-weight-bold">Act.Evaluación: <span
                                            class=" font-weight-light">{{ $item->description }}
                                            [{{ $item->finicial }}] [{{ $item->ffinal }}]</span></div>
                                @else
                                    <div class="font-weight-light text-dark table-warning p-1 rounded">Sin actividad de
                                        evaluación, no forma parte del resumen del Plan de Actividades</div>
                                @endif

                                <div class="px-2 small text-muted"><strong>Tema:</strong> {{ $item->topic }}</div>
                                <div class="px-2 small text-muted"><strong>T.Temático/T.Indispensable:</strong>
                                    {{ $item->thematic }}</div>

                                <div class="px-2 small text-muted"><strong>Enseñanza/Actividad Globalizada:
                                    </strong>{{ $item->teaching }}</div>
                                <div class="px-2 small text-muted"><strong>Aprendizaje:</strong> {{ $item->learning }}
                                </div>
                            </div>

                            <div class="px-2 border-left border-right">
                                <div class="text-muted font-weight-bold">Comentario [J.Área]</div>
                                <div class="text-muted font-weight-light">{{ $item->comments }}</div>
                            </div>

                            <div class="px-2">
                                <div class="btn-group">
                                    <button title="Editar actividad" type="button"
                                        {{ $enable_edit ? null : 'disabled' }} class="btn btn-warning btn-sm"
                                        wire:click="setEditActivity({{ $item->id }})">
                                        <i class="{{ $icon_menus['edit'] ?? '' }}" aria-hidden="true"></i>
                                    </button>
                                    @php $disabled = ($achievements->count() > 0 || ! $enable_edit) ? true : false @endphp
                                    <button title="Eliminar actividad" type="button"
                                        {{ $disabled ? 'disabled' : null }} class="btn btn-danger btn-sm"
                                        wire:click="delActivity({{ $item->id }})">
                                        <i class="{{ $icon_menus['eliminar'] ?? '' }}" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                        @if ($modeCreatorAchievement && $item->id == $activity->id)
                            <div class="border rounded m-2 p-2 shadow-lg overlay">
                                <div class="container-fluid border rounded p-2 shadow-lg">

                                    <div class="d-flex justify-content-between border-bottom">
                                        <div>
                                            <strong>Agregar o Editar Indicadores/Aprendizajes Esperados</strong>
                                        </div>
                                        <div>
                                            <span class="h4 text-muted font-weight-bold" wire:click="close()"
                                                style="cursor: pointer">&times;</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                @php
                                                    $name = 'name';
                                                    $model = 'achievement.' . $name;
                                                @endphp
                                                <label for="{{ $model }}"
                                                    class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                                                {!! Form::text($name, old($name), [
                                                    'wire:model.defer' => $model,
                                                    'class' => 'form-control',
                                                    'placeholder' => $list_comment[$name],
                                                    'required',
                                                ]) !!}
                                                @error($model)
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        @php
                                                            $name = 'status_quantitative_weighting';
                                                            $model = 'achievement.' . $name;
                                                        @endphp
                                                        <input type="checkbox" wire:model="{{ $model ?? null }}">
                                                    </div>
                                                </div>
                                                <div class="form-control small">{{ $list_comment[$name] ?? '' }}</div>
                                            </div>
                                            @error($model)
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror

                                            @if ($achievement->status_quantitative_weighting)
                                                <div class="pt-2">
                                                    <div class="form-group">
                                                        @php
                                                            $name = 'weighting';
                                                            $model = 'achievement.' . $name;
                                                        @endphp
                                                        <label for="{{ $model }}"
                                                            class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                                                        {!! Form::selectRange($name, 1, 20, old($name), [
                                                            'wire:model.defer' => $model,
                                                            'class' => 'form-control',
                                                            'required',
                                                            'placeholder' => 'Seleccione',
                                                        ]) !!}
                                                        @error($model)
                                                            <span class="text-danger small">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="row">

                                        <div class="col">

                                            <div class="input-group">
                                                {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'saveAchievement()']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="small text-muted border rounded mt-2">
                            <div class="pl-4 d-flex justify-content-between table-secondary">
                                <div class="text-muted font-weight-bold align-content-center">Indicadores/Aprendizajes
                                    Esperados</div>
                                <div>
                                    <button title="Agregar indicador de logro" {{ $enable_edit ? null : 'disabled' }}
                                        type="button" class="btn btn-info btn-sm m-1"
                                        wire:click="addAchievement({{ $item->id }})">
                                        <i class="{{ $icon_menus['nuevo'] ?? '' }}" style="cursor: pointer"
                                            aria-hidden="true" role="button"></i>
                                    </button>
                                </div>
                            </div>

                            @forelse ($achievements as $item)
                                <div class="pl-4  d-flex justify-content-between border-top">
                                    <div>-. {{ $item->name }} @if ($item->status_quantitative_weighting)
                                            [{{ $item->weighting }}]
                                        @endif
                                    </div>
                                    <div class=" {{ $achievement_id == $item->id ? 'table-secondary' : null }}">
                                        @if ($enable_edit)
                                            <i wire:click="setEditAchievement({{ $item->id }})"
                                                class="{{ $icon_menus['edit'] ?? '' }} p-1 btn-light text-dark"
                                                style="cursor: pointer" aria-hidden="true"></i>
                                            <i wire:click="deleteAchievement({{ $item->id }})"
                                                class="{{ $icon_menus['eliminar'] ?? '' }} p-1 btn-light text-danger"
                                                style="cursor: pointer" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="disabled small text-muted">No hay indicadores</div>
                            @endforelse

                            <div class="alert alert-secondary ml-4 text-muted">
                                <div class="h6 d-flex justify-content-between">
                                    <div class="flex-grow-1">Total [Ponderaciones]</div>
                                    {{-- <div class=" font-weight-bold">{{$achievements->where('status_quantitative_weighting',true)->sum('weighting')}}</div> --}}
                                    <div class=" font-weight-bold">
                                        {{ $achievements->sum(fn($item) => $item->weighting ?? 0) }}</div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </li>

        @empty
            <li class="list-group-item disabled">No hay datos</li>
        @endforelse

    </ul>

</div>
