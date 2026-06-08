{{-- 'estudiant_id','evaluacion_id','nota','ajuste','user_id','observations' --}}

@php
    $profesor = $boletin ? $boletin->profesor : null;
    $estudiant = $boletin ? $boletin->estudiant : null;
    $evaluacion = $boletin ? $boletin->evaluacion : null;
@endphp

<div class="table-secondary p-2 rounded">
    <div class="form-group">
        <label for="estudiant_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['estudiant_id'] ?? '' }}</label>
        <div class="form-group pl-2">
            {{ $estudiant ? $estudiant->fullname : null }}
            {{ $estudiant ? $estudiant->ci_estudiant : null }}
        </div>
    </div>

    <div class="form-group">
        <label for="estudiant_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['nota'] ?? '' }}</label>
        <div class="form-group pl-2">
            {{ $boletin ? $boletin->nota : null }}
        </div>
    </div>

    <div class="form-group">
        <label for="estudiant_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['profesor'] ?? '' }}</label>
        <div class="form-group pl-2">
            {{ $profesor ? $profesor->fullname : null }}
        </div>
    </div>

    <div class="form-group">
        <label for="estudiant_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['evaluacion'] ?? '' }}</label>
        <div class="form-group pl-2">
            {{ $evaluacion ? $evaluacion->fullname : null }}
        </div>
    </div>
</div>

<hr>

<div class="form-group">
    <label for="evaluacion_id"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['evaluacion_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('evaluacion_id', $list_evaluacion, old('evaluacion_id'), [
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
</div>
