{{--
estudiant_id
profesor_id
class
description
observations
status_active --}}

@php
    $estudiant = $estudiant_selected;
    $grado = $estudiant->grado;
    // $status_profesor_guia = $estudiant->isProfesorGuia($profesor->id,$lapso_current->id);
@endphp

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'profesor_id';
                $model = 'incident.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_profesor, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class=" font-weight-bold">Selección de la descripción tabulada</div>
        <div class="d-flex alert alert-secondary">
            <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 1</strong></div>
            <div class="p-2 flex-grow-1 ">
                <div class="d-flex">
                    <div class="form-group flex-grow-1">
                        @php
                            $name = 'status_valoration';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::select($model, $list_valoration, old($model), [
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

        <div class="d-flex alert alert-secondary">
            <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 2</strong></div>
            <div class="p-2 flex-grow-1 ">
                <div class="d-flex">
                    <div class="form-group flex-grow-1">
                        @php
                            $name = 'type';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::select($model, $list_type, old($model), [
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

        <div class="d-flex alert alert-secondary">
            <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 3</strong></div>
            <div class="p-2 flex-grow-1 ">
                <div class="d-flex">
                    <div class="form-group flex-grow-1">
                        @php
                            $name = 'reason_id';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::select($model, $list_reason, old($model), [
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

        <div class="d-flex alert alert-secondary">
            <div class="p-2 d-flex align-items-center text-nowrap"><strong>Paso 4</strong></div>
            <div class="d-flex">
                <div class="p-2 flex-grow-1 ">
                    <div class="form-group flex-grow-1">
                        @php
                            $name = 'description';
                            $model = 'incident.' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::select($model, $list_description, old($model), [
                            'wire:model' => $model,
                            'class' => 'form-control',
                            'id' => $model,
                            'placeholder' => 'Selecciones',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
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

<hr>

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'description_profesor';
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

{{-- @if ($status_profesor_guia) --}}

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
                        $name = 'status_pedagogical';
                        $model = 'incident.' . $name;
                    @endphp
                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
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
                        $name = 'status_reiterative';
                        $model = 'incident.' . $name;
                    @endphp
                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
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
                        $name = 'status_aggression';
                        $model = 'incident.' . $name;
                    @endphp
                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
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

{{-- @endif --}}
