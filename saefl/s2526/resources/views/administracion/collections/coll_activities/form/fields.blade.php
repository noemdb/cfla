{{-- 'user_id','coll_nivel_id','representant_id','description','status_id','status_messege','status_call' --}}

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="user_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['user_id'] ?? '' }}</label>
                {!! Form::select('user_id', $list_users, old('user_id'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="description"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
                {!! Form::text('description', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['description'],
                    'id' => 'description',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="coll_nivel_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['coll_nivel_id'] ?? '' }}</label>
                {!! Form::select('coll_nivel_id', $list_political_nivels, old('coll_nivel_id'), [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        {{-- <div class="col-sm-6">
            <div class="form-group">
                <label for="representant_id" class="font-weight-bold text-secondary m-0">{{$list_comment['representant_id'] ?? ''}}</label>
                {!! Form::select('representant_id',$list_representant,old('representant_id'),['class'=>'form-control','placeholder'=>'Seleccione']);!!}
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="status_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_id'] ?? '' }}</label>
                {!! Form::select('status_id', $list_status, old('status_id'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        @if (Request::is('*edit*'))
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_messege"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_messege'] ?? '' }}</label>
                    {!! Form::select('status_messege', ['true' => 'SI', 'false' => 'NO'], old('status_messege'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_call"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_call'] ?? '' }}</label>
                    {!! Form::select('status_call', ['true' => 'SI', 'false' => 'NO'], old('status_call'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
        @endif
    </div>

</div>
