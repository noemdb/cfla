<div class="container-fluid">

    <div class="row">

        {{-- 'name','number_turn','description','observations','frecuency','status' --}}

        <div class="col">
            <div class="form-group">
                {!! Form::label('name', $list_comment_assit_schedule['name'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::text('name', old('name'), [
                    'wire:model.defer' => 'name',
                    'class' => 'form-control',
                    'placeholder' => $list_comment_assit_schedule['name'],
                ]) !!}
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                {!! Form::label('description', $list_comment_assit_schedule['description'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::textarea('description', old('description'), [
                    'wire:model.defer' => 'description',
                    'class' => 'form-control',
                    'placeholder' => $list_comment_assit_schedule['description'],
                    'id' => 'description',
                    'rows' => '2',
                ]) !!}
                @error('description')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                {!! Form::label('frecuency', $list_comment_assit_schedule['frecuency'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::select('frecuency', ['SEMANAL' => 'SEMANAL', 'QUINCENAL' => 'QUINCENAL'], old('frecuency'), [
                    'wire:model.defer' => 'frecuency',
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error('frecuency')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                {!! Form::label('number_turn', $list_comment_assit_schedule['number_turn'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::selectRange('number_turn', 1, 4, old('number_turn'), [
                    'wire:model.defer' => 'number_turn',
                    'class' => 'form-control',
                    'id' => 'number_turn',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error('number_turn')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                {!! Form::label('observations', $list_comment_assit_schedule['observations'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
                {!! Form::textarea('observations', old('observations'), [
                    'wire:model.defer' => 'observations',
                    'class' => 'form-control',
                    'placeholder' => $list_comment_assit_schedule['observations'],
                    'id' => 'observations',
                    'rows' => '2',
                ]) !!}
                @error('observations')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                {!! Form::label('status', $list_comment_assit_schedule['status'], [
                    'class' => 'font-weight-bold text-muted pb-0 mb-0',
                ]) !!}
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

    </div>

</div>
