{{--
estudiant_id
profesor_id
class
description
observations
status_active --}}

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'reason_id';
                $model = 'incident.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_reason, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-select',
                'id' => $model,
                'placeholder' => 'Selecciones',
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
                $name = 'type';
                $model = 'incident.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_type, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-select',
                'id' => $model,
                'placeholder' => 'Selecciones',
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
                $name = 'description';
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

<div class="row py-1">
    <div class="col">
        <div class="input-group mb-3">
            <div class="input-group-text">
                @php
                    $name = 'status_pedagogical';
                    $model = 'incident.' . $name;
                @endphp
                <input class="form-check-input mt-0" wire:model.defer="{{ $model ?? null }}" type="checkbox"
                    value="" aria-label="Checkbox for following text input">
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        </div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row py-1">
    <div class="col">
        <div class="input-group mb-3">
            <div class="input-group-text">
                @php
                    $name = 'status_aggression';
                    $model = 'incident.' . $name;
                @endphp
                <input class="form-check-input mt-0" wire:model.defer="{{ $model ?? null }}" type="checkbox"
                    value="" aria-label="Checkbox for following text input">
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        </div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row py-1">
    <div class="col">
        <div class="input-group mb-3">
            <div class="input-group-text">
                @php
                    $name = 'status_announcement';
                    $model = 'incident.' . $name;
                @endphp
                <input class="form-check-input mt-0" wire:model="{{ $model ?? null }}" type="checkbox" value=""
                    aria-label="Checkbox for following text input">
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        </div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
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
