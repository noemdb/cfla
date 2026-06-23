{{--
name
ambit
reason_id
--}}


@if ($modeEdit)
    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'ambit';
                    $model = 'incident_description.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                <div class="alert alert-secondary">{{ $incident_description->ambit ?? null }}</div>
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
                    $name = 'reason_id';
                    $model = 'incident_description.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                <div class="alert alert-secondary">{{ $incident_description->incident_reason->name ?? null }}</div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
@endif


@if ($modeCreate)
    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'ambit';
                    $model = 'incident_description.' . $name;
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
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'reason_id';
                    $model = 'incident_description.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_reason, old($model), [
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
@endif

<div class="form-group pb-2">
    @php
        $name = 'name';
        $model = 'incident_description.' . $name;
    @endphp
    <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
    {!! Form::text($name, old($name), [
        'wire:model.defer' => $model,
        'class' => 'form-control',
        'placeholder' => $list_comment[$name],
    ]) !!}
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
