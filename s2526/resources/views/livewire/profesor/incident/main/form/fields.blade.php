@php
    $estudiant = $estudiant_selected;
    $grado = $estudiant->grado;
    $status_profesor_guia = $estudiant->isProfesorGuia($profesor->id, $lapso_current->id);
@endphp

<div class="card">
    <div class="card-body">

        {{-- Paso 1 --}}
        <div class="alert alert-secondary">
            <div class="d-flex ">
                <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 1</strong></div>
                <div class="p-2 flex-grow-1 ">
                    <div class="d-flex">
                        <div class="form-group flex-grow-1">
                            @php
                                $name = 'duty_id';
                                $model = 'incident.' . $name;
                            @endphp
                            <label for="{{ $model }}"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::select($model, $list_duty, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-control',
                                'id' => $model,
                                'placeholder' => 'Selecciones',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div wire:loading wire:target="{{ $model }}">
                            <div class=" d-flex align-items-center justify-content-center h-100">
                                <div class="px-4">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">Procesando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paso 2 --}}
        <div class="alert alert-secondary">

            <div class="d-flex">

                <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 2</strong></div>
                <div class="p-2 flex-grow-1 ">
                    <div class="d-flex">
                        <div class="form-group flex-grow-1">
                            @php
                                $name = 'fault_id';
                                $model = 'incident.' . $name;
                            @endphp
                            <label for="{{ $model }}"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::select($model, $list_fault, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-control text-wrap',
                                'id' => $model,
                                'placeholder' => 'Selecciones',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div wire:loading wire:target="{{ $model }}">
                            <div class=" d-flex align-items-center justify-content-center h-100">
                                <div class="px-4">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">Procesando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($incident->fault)
                        <div class="alert alert-light" role="alert">
                            {{ $incident->fault ?? null }}

                            <hr>

                            <div class="border rounded shadow-sm">
                                <span class="px-4 font-weight-bold text-muted">Correctivos pedagógicos:</span>
                                @if ($list_corrective->isNotEmpty())
                                    <div class="px-2 pt-0 flex-grow-1 ">
                                        <div class="alert alert-light mt-0 pt-0">
                                            <ul class="list-group list-group-flush">
                                                @forelse ($list_corrective as $item)
                                                    <li class="list-group-item">{{ $item ?? null }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endif
                </div>

            </div>

        </div>

        {{-- Paso 3 --}}
        <div class="alert alert-secondary">

            <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 3</strong></div>

            <div class="row py-1">
                <div class="col">
                    <div class="form-group">
                        @php
                            $name = 'description';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::textarea($model, old($model), [
                            'wire:model.defer' => $model,
                            'class' => 'form-control',
                            'placeholder' => $list_comment[$name],
                            'rows' => '8',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row py-1">
                <div class="col">
                    <div class="form-group">
                        @php
                            $name = 'taken_actions';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::textarea($model, old($model), [
                            'wire:model.defer' => $model,
                            'class' => 'form-control',
                            'placeholder' => $list_comment[$name],
                            'rows' => '4',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        {{-- Paso 5 --}}
        @if ($status_profesor_guia)

            <div class="alert alert-secondary">

                <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 4</strong></div>

                <div class="row py-1">
                    <div class="col">
                        <div class="form-group">
                            @php
                                $name = 'observations';
                                $model = 'incident.' . $name;
                            @endphp
                            <label for="{{ $model }}"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::textarea($model, old($model), [
                                'wire:model.defer' => $model,
                                'class' => 'form-control',
                                'placeholder' => $list_comment[$name],
                                'rows' => '4',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_announcement';
                                        $model = 'incident.' . $name;
                                    @endphp
                                    <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
                                </div>
                            </div>
                            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if ($incident->status_announcement)
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                @php
                                    $name = 'date_announcement';
                                    $model = 'incident.' . $name;
                                @endphp
                                <label for="{{ $model }}"
                                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::date($model, old($model), [
                                    'wire:model.defer' => $model,
                                    'class' => 'form-control',
                                    'id' => 'date_announcement',
                                ]) !!}
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                @php
                                    $name = 'hour_announcement';
                                    $model = 'incident.' . $name;
                                @endphp
                                <label for="{{ $model }}"
                                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::time($model, old($model), [
                                    'wire:model.defer' => $model,
                                    'class' => 'form-control',
                                    'placeholder' => 'Objetivo',
                                    'id' => 'hour_announcement',
                                ]) !!}
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        @endif

    </div>
</div>
