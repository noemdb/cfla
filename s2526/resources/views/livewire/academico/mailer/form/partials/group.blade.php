<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="pestudio_id" class=" font-weight-bold m-0 small">{{ $list_comment['pestudio_id'] ?? '' }}</label>
            {!! Form::select('pestudio_id', $list_pestudio, old('pestudio_id'), [
                'wire:model.defer' => 'pestudio_id',
                'wire:click' => 'loadGrado()',
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('pestudio_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="grado_id" class=" font-weight-bold m-0 small">{{ $list_comment['grado_id'] ?? '' }}</label>
            {!! Form::select('grado_id', $list_grado, old('grado_id'), [
                'wire:model.defer' => 'grado_id',
                'wire:click' => 'loadSeccion()',
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('grado_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="seccion_id" class=" font-weight-bold m-0 small">{{ $list_comment['seccion_id'] ?? '' }}</label>
            {!! Form::select('seccion_id', $list_seccion, old('seccion_id'), [
                'wire:model.defer' => 'seccion_id',
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('seccion_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="status" class=" font-weight-bold m-0 small">{{ $list_comment['status'] ?? '' }}</label>
            {!! Form::select('status', ['true' => 'Activo', 'false' => 'Desactivo'], old('status'), [
                'wire:model.defer' => 'status',
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('status')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="status_adviders"
                class=" font-weight-bold m-0 small">{{ $list_comment['status_adviders'] ?? '' }}</label>
            {!! Form::select('status_adviders', ['true' => 'SI', 'false' => 'NO'], old('status_adviders'), [
                'wire:model.defer' => 'status_adviders',
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('status_adviders')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
