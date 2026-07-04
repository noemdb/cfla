<div class=" d-block">
    <label for="estudiant_id"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['estudiant_id'] ?? '' }}</label>
    <div class="form-control alert-secondary pb-2 mb-2 text-secondary">
        {{ $estudiant->fullname ?? '' }}
        {{ Form::hidden('estudiant_id', $estudiant->id) }}
    </div>

    <label for="lapso_id" class="font-weight-bold text-secondary m-0">{{ $list_comment['lapso_id'] ?? '' }}</label>
    <div class="form-group">
        {!! Form::select('lapso_id', $list_lapso, old('lapso_id'), ['class' => 'form-control']) !!}
    </div>

    <label for="description" class="font-weight-bold text-secondary m-0">{{ $list_comment['description'] ?? '' }}</label>
    <div class="form-group">

        <textarea name="description" class="form-control" id="description" readonly required="required" rows="5"
            placeholder="{{ $list_comment['description'] }}"> {{ $list_comment['gaceta'] ?? '' }}</textarea>
        {{-- {!! Form::text('description', old('description'), ['class' => 'form-control','placeholder'=>$list_comment['description']]); !!} --}}
    </div>

    <label for="observations"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['observations'] ?? '' }}</label>
    <div class="form-group">
        <textarea name="observations" class="form-control" id="observations" required="required" rows="5"
            placeholder="{{ $list_comment['observations'] }}"></textarea>
        {{-- {!! Form::text('observations', old('observations'), ['required','class' => 'form-control','placeholder'=>$list_comment['observations']]); !!} --}}
    </div>

    <label for="name" class="font-weight-bold text-secondary m-0">Otros</label>
    <div class="form-group">
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Otros']) !!}
    </div>

</div>
