<div class="p-2 m-2">

    <div class="form-group">
        @php
            $name = 'text';
            $model = 'option.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_option[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_option[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'observation';
            $model = 'option.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_option[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_option[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        @php
                            $name = 'status_option_correct';
                            $model = 'option.' . $name;
                        @endphp
                        <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
                    </div>
                </div>
                <div class="form-control"> {{ $list_comment_option[$name] ?? null }}</div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
