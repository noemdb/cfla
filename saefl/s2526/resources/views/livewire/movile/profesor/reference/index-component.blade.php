<div>

    <form wire:submit.prevent="saveReferent" class="text-start  p-2 m-2">

        <div class="form-group mb-3">
            @php
                $name = 'pevaluacion_id';
                $model = 'evaluacion.' . $name;
                $comment = $list_comment[$name];
            @endphp
            <label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
            {!! Form::select($model, $list_pevaluacions, old($model), [
                'wire:model' => $model,
                'class' => 'form-select',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        {{-- {{$pevaluacion}} --}}

        @if ($pevaluacion)

            @php
                $escala_name = !empty($pevaluacion->escala->id) ? $pevaluacion->escala->name : null;
                $evaluacions = $pevaluacion->evaluacions;
            @endphp

            <div class="ms-2 me-auto small text-muted">
                <div class="fw-bold">{{ $pevaluacion->asignatura->name }} {{ $pevaluacion->full_seccion }}</div>
                <div class=" fw-light">
                    <span class="small  ms-2">{{ $pevaluacion->description }}</span> ||
                    <span class="small">Evaluaciones: [{{ $evaluacions->count() }}]</span> ||
                    <span>Escala: {{ $escala_name }}</span>
                </div>

            </div>

            <div class="px-2 pt-1">

                @php $key = 'reference-pevaluacion-'.$pevaluacion->id; @endphp
                <div class="form-group mb-3">
                    @php
                        $name = 'description';
                        $model = 'evaluacion.' . $name;
                        $comment = $list_comment[$name];
                    @endphp
                    <label for="{{ $name }}"
                        class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
                    {!! Form::textarea($model, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'rows' => '2',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    @php
                        $name = 'fecha';
                        $model = 'evaluacion.' . $name;
                        $comment = $list_comment[$name];
                    @endphp
                    <label for="{{ $name }}"
                        class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
                    {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'required']) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <button type="button" class="btn btn-primary btn-block w-100" wire:click="saveReferent">Guardar</button>

            <hr>

            <ul class="list-group">
                <li class="list-group-item list-group-item-secondary">Listado de referentes teórico prácticos
                    registrados.</li>
                @forelse ($evaluacions as $item)
                    @php $status = $item->status_delete; @endphp
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>{{ $loop->iteration }}.- {{ $item->description }}</div>
                            <div>
                                <fieldset {{ $item->status_delete ? null : 'disabled' }}>
                                    {{-- <button type="button" class="btn btn-danger btn-sm" wire:click="delete($item->id)"> --}}
                                    <button type="button" class="btn btn-danger btn-sm"
                                        wire:click="delete({{ $item->id }})">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </fieldset>
                            </div>
                        </div>

                    </li>
                @empty
                    <li class="list-group-item disabled fw-light">
                        No hay referentes teórico prácticos registrados.
                    </li>
                @endforelse
            </ul>
        @else
            <div class=" fw-light">
                No hay plan de evaluación seleccionado
            </div>

        @endif

    </form>

</div>
