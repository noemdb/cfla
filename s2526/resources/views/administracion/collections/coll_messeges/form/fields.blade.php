{{-- 'user_id','coll_nivel_id','subject','title','subtitle','greeting','consider','sentence','waiting','footer' --}}

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
                <label for="coll_nivel_id"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['coll_nivel_id'] ?? '' }}</label>
                {!! Form::select('coll_nivel_id', $list_political_nivels, old('coll_nivel_id'), [
                    'class' => 'form-control',
                    'required',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="subject"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['subject'] ?? '' }}</label>
                {{-- {!! Form::text('subject', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['subject'],'id'=>'subject','required']) !!} --}}
                {!! Form::textarea('subject', old('subject'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['subject'],
                    'id' => 'subject',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="title"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['title'] ?? '' }}</label>
                {{-- {!! Form::text('title', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['title'],'id'=>'title','required']) !!} --}}
                {!! Form::textarea('title', old('title'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['title'],
                    'id' => 'title',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="subtitle"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['subtitle'] ?? '' }}</label>
                {{-- {!! Form::text('subtitle', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['subtitle'],'id'=>'subtitle','required']) !!} --}}
                {!! Form::textarea('subtitle', old('subtitle'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['subtitle'],
                    'id' => 'subtitle',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="greeting"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['greeting'] ?? '' }}</label>
                {{-- {!! Form::text('greeting', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['greeting'],'id'=>'greeting','required']) !!} --}}
                {!! Form::textarea('greeting', old('greeting'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['greeting'],
                    'id' => 'greeting',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="consider"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['consider'] ?? '' }}</label>
                {{-- {!! Form::text('consider', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['consider'],'id'=>'consider','required']) !!} --}}
                {!! Form::textarea('consider', old('consider'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['consider'],
                    'id' => 'consider',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="sentence"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['sentence'] ?? '' }}</label>
                {{-- {!! Form::text('sentence', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['sentence'],'id'=>'sentence','required']) !!} --}}
                {!! Form::textarea('sentence', old('sentence'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['sentence'],
                    'id' => 'sentence',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="waiting"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['waiting'] ?? '' }}</label>
                {{-- {!! Form::text('waiting', old('weighing'), ['class' => 'form-control','placeholder'=>$list_comment['waiting'],'id'=>'waiting','required']) !!} --}}
                {!! Form::textarea('waiting', old('waiting'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['waiting'],
                    'id' => 'waiting',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="footer"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['footer'] ?? '' }}</label>
                {{-- {!! Form::text('footer', old('footer'), ['class' => 'form-control','placeholder'=>$list_comment['footer'],'id'=>'footer','required']) !!} --}}
                {!! Form::textarea('footer', old('footer'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['footer'],
                    'id' => 'footer',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
    </div>

    @if (Request::is('*edit*'))
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="status"
                        class="font-weight-bold text-secondary m-0">{{ $list_comment['status'] ?? '' }}</label>
                    {!! Form::select('status', ['true' => 'Activo', 'false' => 'Desactivo'], old('status'), [
                        'class' => 'form-control',
                        'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>
            </div>
        </div>
    @endif

</div>
