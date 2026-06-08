<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="subject" class=" font-weight-bold m-0 small">{{ $list_comment['subject'] ?? '' }}</label>
            {!! Form::text('subject', old('subject'), [
                'wire:model.defer' => 'subject',
                'class' => 'form-control',
                'placeholder' => $list_comment['subject'],
            ]) !!}
            @error('subject')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="title" class=" font-weight-bold m-0 small">{{ $list_comment['title'] ?? '' }}</label>
            {!! Form::text('title', old('title'), [
                'wire:model.defer' => 'title',
                'class' => 'form-control',
                'placeholder' => $list_comment['title'],
            ]) !!}
            @error('title')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="subtitle" class=" font-weight-bold m-0 small">{{ $list_comment['subtitle'] ?? '' }}</label>
            {!! Form::text('subtitle', old('subtitle'), [
                'wire:model.defer' => 'subtitle',
                'class' => 'form-control',
                'placeholder' => $list_comment['subtitle'],
            ]) !!}
            @error('subtitle')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="greeting" class=" font-weight-bold m-0 small">{{ $list_comment['greeting'] ?? '' }}</label>
            {!! Form::text('greeting', old('greeting'), [
                'wire:model.defer' => 'greeting',
                'class' => 'form-control',
                'placeholder' => $list_comment['greeting'],
            ]) !!}
            @error('greeting')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
