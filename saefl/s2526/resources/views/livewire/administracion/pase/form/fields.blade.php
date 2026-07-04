<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="estudiant_id" class="font-weight-bold m-0">Estudiante</label>
                <div class="input-group">
                    <div class="input-group-append" style="z-index: 0;">
                        <input type="text" class="form-control small" placeholder="CI o nombre" id="help_estudiant"
                            wire:model.debounce.500ms="search_estudiant">
                    </div>
                    @php $name = 'estudiant_id' @endphp
                    {!! Form::select($name, $list_estudiants, null, [
                        'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                        'wire:model' => $name,
                        'id' => $name,
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'profesor_id' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_profesor, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'pensum_id' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_pensum, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'type' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_type, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'motive' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_motive, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'description' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <textarea class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" rows="3" placeholder="{{ $list_comment[$name] }}"></textarea>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'destination' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="text" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" placeholder="{{ $list_comment[$name] }}">
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'date' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="date" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" required>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'time' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                <input type="time" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}"
                    wire:model="{{ $name }}" required>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_guardian' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_teacher' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php $name = 'require_auhtorize_manager' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, $list_status, null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                @php $name = 'status_emergency' @endphp
                <label for="{{ $name }}" class="font-weight-bold m-0">{{ $list_comment[$name] }}</label>
                {!! Form::select($name, ['1' => 'SI', '0' => 'NO'], null, [
                    'class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : ''),
                    'wire:model' => $name,
                    'id' => $name,
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
@endsection
