<div class="card-header p-0 m-0 mb-3">
    <div class="form-row">
        <div class="col-3">
            <label for="grado_id" class="font-weight-bold m-0">Grado</label>
            {!! Form::select('grado_id', $list_grado, 'grado_id', [
                'wire:model' => 'grado_id',
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-3">
            <label for="seccion_id" class="font-weight-bold m-0">Sección</label>
            {!! Form::select('seccion_id', $list_seccion, 'seccion_id', [
                'wire:model.defer' => 'seccion_id',
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-3">
            <label for="lapso_id" class="font-weight-bold m-0">Momento</label>
            {!! Form::select('lapso_id', $list_lapso, 'lapso_id', [
                'wire:model.defer' => 'lapso_id',
                'class' => 'form-control',
                'id' => 'lapso_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-3">
            <label for="pensum_id" class="font-weight-bold m-0">Asignatura</label>
            {!! Form::select('pensum_id', $list_pensum, 'pensum_id', [
                'wire:model.defer' => 'pensum_id',
                'class' => 'form-control',
                'id' => 'pensum_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

    </div>

    <div class="form-row pt-2">
        <div class="col-3">
            <label for="profesor_id" class="font-weight-bold m-0">Profesor</label>
            {!! Form::select('profesor_id', $list_profesor, 'profesor_id', [
                'wire:model.defer' => 'profesor_id',
                'class' => 'form-control',
                'id' => 'profesor_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-3">
            <label for="finicial" class="font-weight-bold m-0">Fecha Inicial</label>
            {!! Form::date('finicial', $this->finicial ? \Carbon\Carbon::parse($this->finicial)->format('Y-m-d') : '', [
                'wire:model.defer' => 'finicial',
                'class' => 'form-control',
                'id' => 'finicial',
            ]) !!}
        </div>
        <div class="col-3">
            <label for="ffinal" class="font-weight-bold m-0">Fecha Final</label>
            {!! Form::date('ffinal', $this->ffinal ? \Carbon\Carbon::parse($this->ffinal)->format('Y-m-d') : '', [
                'wire:model.defer' => 'ffinal',
                'class' => 'form-control',
                'id' => 'ffinal',
            ]) !!}
        </div>
        <div class="col-3">

            <div class="d-flex justify-content-between">
                <div class=" flex-grow-1">
                    <label for="status_execution" class="font-weight-bold m-0">Estado</label>
                    {!! Form::select('status_execution', [true => 'Ejecutada', false => 'Pendiente'], 'status_execution', [
                        'wire:model.defer' => 'status_execution',
                        'class' => 'form-control',
                        'id' => 'status_execution',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
                <div class="pl-1">
                    <label for="pensum_id" class="font-weight-bold m-0">.</label>
                    <button class="form-control btn btn-primary" type="button" id="button-addon"
                        wire:click="search()"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                </div>
                <span class="input-text px-4" wire:loading>
                    <strong>Procesando...</strong>
                </span>
            </div>

        </div>
    </div>
</div>
