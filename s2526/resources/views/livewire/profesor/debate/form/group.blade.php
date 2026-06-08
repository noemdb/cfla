<div class="p-2 m-2">

    <div class="form-group">
        @php
            $name = 'name';
            $model = 'group.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
        {!! Form::text($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_debate[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'description';
            $model = 'group.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_debate[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

</div>
