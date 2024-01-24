{{--
"status_conditions_intellectual_disability" => null
"status_conditions_motor_disability" => null
"status_conditions_visual_disability" => null
"status_conditions_hearing_impairment" => null
"status_conditions_outstanding_attitudes" => null
"status_conditions_autism" => null
"status_conditions_other" => null
"conditions_other" => null
--}}

<div class="pb-2">
    @php $name = 'status_conditions_intellectual_disability' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_motor_disability' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_visual_disability' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_hearing_impairment' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_outstanding_attitudes' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_autism' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_conditions_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

@if ($enrollment->status_conditions_other)
    <div class="pb-2">
        @php $name = 'conditions_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
        @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
    </div>
@endif


