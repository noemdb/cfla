<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="status_omitted"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_omitted'] ?? '' }}</label>
                {{-- {!! Form::select('status_omitted_set',['true'=>'SI','false'=>'NO'],$selected->status_omitted,['class'=>'form-control','id'=>'status_omitted_set','placeholder'=>'Seleccione','required'=>'required']) !!} --}}
                {!! Form::select('status_omitted', ['true' => 'SI', 'false' => 'NO'], old('status_omitted'), [
                    'class' => 'form-control',
                    'id' => 'status_omitted',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class=" d-block">
                <label for="observations_user"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['observations_user'] ?? '' }}</label>
                <div class="form-group">
                    {!! Form::text('observations_user', old('observations_user'), [
                        'class' => 'form-control',
                        'placeholder' => $list_comment['observations_user'],
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

</div>

{{ Form::hidden('finicial', empty($finicial) ? null : $finicial) }}
{{ Form::hidden('ffinal', empty($ffinal) ? null : $ffinal) }}
{{ Form::hidden('ci', empty($ci) ? null : $ci) }}
{{ Form::hidden('active', empty($active) ? null : $active) }}
{{ Form::hidden('status_omitted_request', empty($status_omitted_request) ? null : $status_omitted_request) }}
{{ Form::hidden('creditoafavor_id', empty($creditoafavor_id) ? null : $creditoafavor_id) }}
