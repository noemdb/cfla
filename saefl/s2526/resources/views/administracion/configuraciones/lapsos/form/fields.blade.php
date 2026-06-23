<div class=" d-block">

    <label for="name" class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::text('name', old('name'), [
            'class' => 'form-control',
            'placeholder' => $list_comment['name'],
            'required',
        ]) !!}
    </div>

    <div class="row">
        <div class="col-6">
            <label for="code" class="font-weight-bold text-secondary m-0">{{ $list_comment['code'] ?? '' }}</label>
            <div class="form-group">
                {!! Form::text('code', old('code'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['name'],
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <label for="code_sm"
                class="font-weight-bold text-secondary m-0">{{ $list_comment['code_sm'] ?? '' }}</label>
            <div class="form-group">
                {!! Form::text('code_sm', old('code_sm'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['code_sm'],
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <label for="finicial"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['finicial'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('finicial', old('finicial'), [
                    'class' => 'form-control',
                    'placeholder' => 'Fecha inicial',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <label for="ffinal"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['ffinal'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('ffinal', old('ffinal'), [
                    'class' => 'form-control',
                    'placeholder' => 'Fecha final',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="academic_start_date"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['academic_start_date'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('academic_start_date', old('academic_start_date'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['academic_start_date'],
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>

    <label for="date_cutnote"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_cutnote'] ?? '' }}</label>
    <div class="form-group pb-1">
        {!! Form::date('date_cutnote', old('date_cutnote'), [
            'class' => 'form-control',
            'placeholder' => 'Fecha final',
            'required' => 'required',
        ]) !!}
    </div>

    <div class="row">
        <div class="col-6">
            <label for="date_start_census"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_start_census'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('date_start_census', old('date_start_census'), [
                    'class' => 'form-control',
                    'placeholder' => 'Fecha I.Censo',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <label for="date_end_census"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_end_census'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('date_end_census', old('date_end_census'), [
                    'class' => 'form-control',
                    'placeholder' => 'Fecha F.Censo',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <label for="time_start_census"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['time_start_census'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::time('time_start_census', old('time_start_census'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['time_start_census'],
                    'id' => 'time_start_census',
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <label for="time_end_census"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['time_end_census'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::time('time_end_census', old('time_end_census'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['time_end_census'],
                    'id' => 'time_end_census',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <label for="date_preclosing"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['date_preclosing'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::date('date_preclosing', old('date_preclosing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['date_preclosing'],
                ]) !!}
            </div>
        </div>
        <div class="col-6">
            <label for="time_preclosing"
                class="m-0 font-weight-bold text-secondary">{{ $list_comment['time_preclosing'] ?? '' }}</label>
            <div class="form-group pb-1">
                {!! Form::time('time_preclosing', old('time_preclosing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['time_preclosing'],
                    'id' => 'time_preclosing',
                ]) !!}
            </div>
        </div>
    </div>

    <label for="status_last"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_last'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('status_last', ['true' => 'SI', 'false' => 'NO'], old('status_last'), [
            'class' => 'form-control',
            'id' => 'status_last',
            'required',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>



</div>


{{--
'code' => 'Código',
'code_sm' => 'Abreviación',
'name' => 'Nombre',
'finicial' => 'Fecha de inicio',
'ffinal' => 'Fecha de finalización',
--}}
