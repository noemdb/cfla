{{-- 'bmain_id','key','text','description' --}}

<div class="container-fluid">

    <div class="row">
        <div class="col-sm-6">
            <div class="container-fluid">
                @if (Request::is('*edit*'))
                    @php $bmain = $boption->bmain @endphp
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="bmain_id"
                                    class="font-weight-bold m-0">{{ $list_comment['bmain_id'] ?? '' }}</label>
                                <div class="alert alert-secondary">
                                    <div>{{ $bmain->name }}:</div>
                                    <div>{{ $bmain->description }}</div>
                                </div>
                                {{ Form::hidden('bmain_id', old('bmain_id')) }}
                            </div>
                        </div>
                    </div>
                @endif
                @if (Request::is('*create*'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="bmain_id"
                                    class="font-weight-bold text-secondary m-0">{{ $list_comment['bmain_id'] ?? '' }}</label>
                                {!! Form::select('bmain_id', $list_bmains, old('bmain_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Seleccione',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                @endif
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
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="key"
                                class="font-weight-bold text-secondary m-0">{{ $list_comment['key'] ?? '' }}</label>
                            {!! Form::text('key', old('weighing'), [
                                'class' => 'form-control',
                                'placeholder' => $list_comment['key'],
                                'id' => 'key',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="text" class="font-weight-bold m-0">{{ $list_comment['text'] ?? '' }}</label>
                @php $rows = (Request::is('*edit*')) ? round((strlen($boption)/50)) : 4 ; @endphp
                {!! Form::textarea('text', old('text'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['text'],
                    'id' => 'text',
                    'rows' => $rows,
                ]) !!}
            </div>
        </div>
    </div>



    {{-- <div class="row">
        <div class="col-sm-12">

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="description" class="font-weight-bold m-0">{{ $list_comment['description'] ?? ''}}</label>
                {!! Form::textarea('description', old('description'), ['class' => 'form-control','placeholder'=>$list_comment['description'],'id'=>'description','rows'=>"2"]) !!}
            </div>
        </div>
    </div> --}}



</div>
