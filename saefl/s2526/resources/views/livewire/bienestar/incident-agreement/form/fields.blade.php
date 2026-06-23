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
                $name = 'description';
                $model = 'incident_agreement.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
    </div>
</div>

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'observations';
                $model = 'incident_agreement.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
