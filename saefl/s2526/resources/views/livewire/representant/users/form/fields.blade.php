@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


<div class="row py-1">
    <div class="col">
        {{-- <div class="form-group"> --}}
        @php
            $name = 'username';
            $model = 'user.' . $name;
        @endphp
        <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::text($model, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        {{-- </div> --}}
    </div>
    <div class="col">
        {{-- <div class="form-group"> --}}
        @php
            $name = 'password';
            $model = 'user.' . $name;
        @endphp
        <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::password($model, [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        {{-- </div> --}}
    </div>
</div>

<div class="row py-1">
    <div class="col">
        {{-- <div class="form-group"> --}}
        @php
            $name = 'email';
            $model = 'user.' . $name;
        @endphp
        <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::email($model, old($model), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        {{-- </div> --}}
    </div>
</div>
