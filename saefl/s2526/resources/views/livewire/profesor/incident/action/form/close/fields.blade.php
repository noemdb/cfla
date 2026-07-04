<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'close_observations';
                $model = '' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '4',
            ]) !!}
            @error($name)
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
                        $name = 'status_notify_close';
                        $model = '' . $name;
                    @endphp
                    <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
            @error($name)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
