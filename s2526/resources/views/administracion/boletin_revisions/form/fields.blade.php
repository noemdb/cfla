<div class="form-row">

    <div class="col-6">
        <div class="form-group">
            <label for="pensum_id" class="font-weight-bold m-0">Asignatura</label>
            {!! Form::select('pensum_id', $list_pensum, old('pensum_id', $pensum_id ?? null), [
                'class' => 'form-control',
                'id' => 'pensum_id',
                'placeholder' => 'Seleccione',
                'required',
                // Livewire (si existe el componente)
                'wire:model.defer' => 'pensum_id',
            ]) !!}
            @error('pensum_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label for="profesor_id" class="font-weight-bold m-0">
                {{ $list_comment['profesor_id'] ?? '' }}
            </label>
            {!! Form::select('profesor_id', $list_profesor, old('profesor_id', $profesor_id ?? null), [
                'class' => 'form-control',
                'id' => 'profesor_id',
                'placeholder' => 'Seleccione',
                'required',
                'wire:model.defer' => 'profesor_id',
            ]) !!}
            @error('profesor_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

</div>

<div class="form-row">

    <div class="col-6">
        <div class="form-group">
            <label for="numero" class="font-weight-bold m-0">
                {{ $list_comment['numero'] ?? '' }}
            </label>
            {!! Form::selectRange('numero', 1, 10, old('numero', $numero ?? null), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
                'required',
                'wire:model.defer' => 'numero',
                'id' => 'numero',
            ]) !!}
            @error('numero')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            @php $minimo = $escala->minimo; @endphp
            @php $maximo = $escala->maximo; @endphp
            <label for="nota" class="font-weight-bold m-0">
                {{ $list_comment['nota'] ?? '' }}
            </label>
            {!! Form::select('nota', $list_nota, old('nota', $nota ?? null), [
                'class' => 'form-control',
                'id' => 'nota',
                'placeholder' => 'Seleccione',
                'required',
                'wire:model.defer' => 'nota',
            ]) !!}
            @error('nota')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

</div>

<div class="form-row">

    <div class="col-6">
        <div class="form-group">
            <label for="description" class="font-weight-bold m-0">
                {{ $list_comment['description'] ?? '' }}
            </label>
            {!! Form::textarea('description', old('description', $description ?? null), [
                'class' => 'form-control',
                'placeholder' => $list_comment['description'] ?? '',
                'id' => 'description',
                'rows' => 4,
                'wire:model.defer' => 'description',
            ]) !!}
            @error('description')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label for="observations" class="font-weight-bold m-0">
                {{ $list_comment['observations'] ?? '' }}
            </label>
            {!! Form::textarea('observations', old('observations', $observations ?? null), [
                'class' => 'form-control',
                'placeholder' => $list_comment['observations'] ?? '',
                'id' => 'observations',
                'rows' => 4,
                'wire:model.defer' => 'observations',
            ]) !!}
            @error('observations')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

</div>

<div class="form-row">
    <div class="col-6">
        <div class="form-group">
            <label for="type" class="font-weight-bold m-0">
                {{ $list_comment['type'] ?? 'Tipo' }}
            </label>
            {!! Form::select('type', $list_types ?? [], old('type', $type ?? 'REVISION'), [
                'class' => 'form-control',
                'id' => 'type',
                'required',
                'wire:model.defer' => 'type',
            ]) !!}
            @error('type')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
