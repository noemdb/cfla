<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="body" class=" font-weight-bold m-0 small">{{ $list_comment['body'] ?? '' }}</label>
            {!! Form::textarea('body', old('body'), [
                'wire:model.defer' => 'body',
                'class' => 'form-control',
                'placeholder' => $list_comment['body'],
                'rows' => '6',
            ]) !!}
            @error('body')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="insert" class=" font-weight-bold m-0 small">{{ $list_comment['insert'] ?? '' }}</label>
            {{-- {!! Form::text('insert', old('insert'), ['wire:model.defer'=>'insert','class' => 'form-control','placeholder'=>$list_comment['insert']]); !!} --}}
            {!! Form::textarea('insert', old('insert'), [
                'wire:model.defer' => 'insert',
                'class' => 'form-control',
                'placeholder' => $list_comment['insert'],
                'rows' => '2',
            ]) !!}
            @error('insert')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment['footer'] ?? '' }}</label>
            {!! Form::text('footer', old('footer'), [
                'wire:model.defer' => 'footer',
                'class' => 'form-control',
                'placeholder' => $list_comment['footer'],
            ]) !!}
            @error('footer')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment['atte'] ?? '' }}</label>
            {!! Form::text('atte', old('atte'), [
                'wire:model.defer' => 'atte',
                'class' => 'form-control',
                'placeholder' => $list_comment['atte'],
            ]) !!}
            @error('atte')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
