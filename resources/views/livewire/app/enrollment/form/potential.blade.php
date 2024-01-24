{{--
"status_vaccination_schedule" => null
"status_sports_potential" => null
"sports_potential" => null
"place_where_he_practices" => null
--}}


<div class="pb-2">
    @php $name = 'status_sports_potential' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

@if ($enrollment->status_sports_potential)
    <div class="pb-2">
        @php $name = 'sports_potential' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_sports_potential" wire:model="{{$model}}" option-key-value/>
        {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
    </div>
    
    <div class="pb-2">
        @php $name = 'place_where_he_practices' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
        @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
    </div>
@endif

<div class="pb-2">
    @php $name = 'status_vaccination_schedule' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>


