{{-- 'coll_political_id','representant_id','date','ammount','exchange_ammount','status','description','observation' --}}
{{-- {{Form::hidden('coll_political_id',$coll_political->id)}} --}}

<div class="container-fluid">

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="date"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['date'] ?? '' }}</label>
                {!! Form::date('date', old('date'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['date'],
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="exchange_ammount"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['exchange_ammount'] ?? '' }}</label>
                {!! Form::text('exchange_ammount', old('weighing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['exchange_ammount'],
                    'id' => 'exchange_ammount',
                    'required',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="description"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}. La razón del
                    incumplimiento</label>
                {{-- {!! Form::text('description', old('description'), ['class' => 'form-control','placeholder'=>$list_comment['description'],'id'=>'description']); !!} --}}
                {!! Form::textarea('description', old('description'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['description'],
                    'id' => 'description',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="observation"
                    class="font-weight-bold text-secondary m-0">{{ $list_comment['observation'] ?? '' }}</label>
                {{-- {!! Form::text('observation', old('observation'), ['class' => 'form-control','placeholder'=>$list_comment['observation'],'id'=>'observation']); !!} --}}
                {!! Form::textarea('observation', old('observation'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['observation'],
                    'id' => 'observation',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>
    </div>

</div>
