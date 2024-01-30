{{--
"status_illness_cardiovascular" => null
"status_illness_cancer" => null
"status_illness_lupus" => null
"status_illness_diabetes" => null
"status_illness_renal_problems" => null
"status_illness_overweight" => null
"status_illness_other" => null
"illness_other" => null
--}}

<div class="pb-2">
    @php $name = 'status_illness_cardiovascular' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_illness_lupus' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_illness_diabetes' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_illness_overweight' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_illness_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

@if ($enrollment->status_illness_other)
    <div class="pb-2">
        @php $name = 'illness_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
        {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
    </div>
@endif


