<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="rating" class="m-0">{{ $list_comment['rating'] }}</label>
            {!! Form::selectRange('rating', 1, 5, old('rating'), [
                'class' => 'form-control',
                'id' => 'rating',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>

        <div class="form-group">
            <label for="accepted" class="m-0">{{ $list_comment['accepted'] ?? null }}</label>
            {!! Form::select('accepted', [true => 'SI', false => 'NO'], old('accepted'), [
                'class' => 'form-control',
                'id' => 'accepted',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>

        <div class="form-group">
            <label for="status_standby" class="m-0">{{ $list_comment['status_standby'] ?? null }}</label>
            {!! Form::select('status_standby', [true => 'SI', false => 'NO'], old('status_standby'), [
                'class' => 'form-control',
                'id' => 'accepted',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>

        <div class="form-group">
            <label for="observations" class="m-0">{{ $list_comment['observations'] ?? null }}</label>
            {{-- {!! Form::select('accepted',[true=>'SI',false=>'NO'],old('accepted'),['class' => 'form-control','id'=>'accepted','placeholder' => 'Seleccione']) !!} --}}
            {!! Form::textarea('observations', old('observations'), [
                'class' => 'form-control',
                'placeholder' => $list_comment['observations'],
                'id' => 'observations',
                'rows' => '4',
            ]) !!}
        </div>
    </div>
</div>
