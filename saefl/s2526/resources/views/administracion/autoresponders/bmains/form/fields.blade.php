{{-- 'name','description','platform','header_key','header_value','user','password','status_active' --}}

<div class="container-fluid">

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="name"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['name'] ?? '' }}</label>
                {!! Form::text('name', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['name'],
                    'id' => 'name',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="platform"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['platform'] ?? '' }}</label>
                {!! Form::text('platform', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['platform'],
                    'id' => 'platform',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="description"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
                {!! Form::textarea('description', old('description'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['description'],
                    'id' => 'description',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="header_key"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['header_key'] ?? '' }}</label>
                {!! Form::text('header_key', old('header_key'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['header_key'],
                    'id' => 'header_key',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="header_value"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['header_value'] ?? '' }}</label>
                {!! Form::text('header_value', old('header_value'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['header_value'],
                    'id' => 'header_value',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="user"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['user'] ?? '' }}</label>
                {!! Form::text('user', old('user'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['user'],
                    'id' => 'user',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="password"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['password'] ?? '' }}</label>
                {!! Form::text('password', old('password'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['password'],
                    'id' => 'password',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="status_active"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_active'] ?? '' }}</label>
                {!! Form::select('status_active', ['true' => 'Activo', 'false' => 'Desactivo'], old('status_active'), [
                    'class' => 'form-control',
                    'id' => 'status_active',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="area"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['area'] ?? '' }}</label>
                {!! Form::select('area', $list_area, old('area'), ['class' => 'form-control', 'placeholder' => 'Seleccione']) !!}
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="status_id" class="font-weight-bold text-secondary m-0">{{$list_comment['status_id'] ?? ''}}</label>
                {!! Form::select('status_id',$list_status,old('status_id'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
            </div>
        </div>
        @if (Request::is('*edit*'))
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_messege" class="font-weight-bold text-secondary m-0">{{$list_comment['status_messege'] ?? ''}}</label>
                    {!! Form::select('status_messege',['true'=>'SI','false'=>'NO'],old('status_messege'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="status_call" class="font-weight-bold text-secondary m-0">{{$list_comment['status_call'] ?? ''}}</label>
                    {!! Form::select('status_call',['true'=>'SI','false'=>'NO'],old('status_call'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                </div>
            </div>
        @endif
    </div> --}}

</div>
