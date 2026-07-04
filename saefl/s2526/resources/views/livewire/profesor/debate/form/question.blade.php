<div class="p-2 m-2">

    <div class="form-group">
        @php
            $name = 'category';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $model }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::select($model, $list_category, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'text';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_question[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'observation';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_question[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'time';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $model }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::select($model, $list_timing, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'weighting';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $model }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::select($model, $list_weighting, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'option_max';
            $model = 'question.' . $name;
        @endphp
        <label for="{{ $name ?? null }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_question[$name] ?? '' }}</label>
        {!! Form::number($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_question[$name],
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
                            $name = 'status_active';
                            $model = 'question.' . $name;
                        @endphp
                        <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
                    </div>
                </div>
                <div class="form-control"> {{ $list_comment_question[$name] ?? null }}</div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
