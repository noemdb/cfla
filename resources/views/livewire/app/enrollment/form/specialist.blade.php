{{--
"status_treated_by_specialist" => null
"specialist" => null
"status_take_medication" => null
"medication" => null
--}}

<div class="pb-2">
    @php $name = 'status_treated_by_specialist' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

@if ($enrollment->status_treated_by_specialist)
    <div class="pb-2">
        @php $name = 'specialist' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
        {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
    </div>
@endif

<div class="pb-2">
    @php $name = 'status_take_medication' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

@if ($enrollment->status_take_medication)
    <div class="pb-2">
        @php $name = 'medication' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
        {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
    </div>
@endif


