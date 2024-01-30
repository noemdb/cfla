{{--
"coexistence" => null
"status_transport_private_vehicle" => null
"status_transport_public_vehicle" => null
"status_transport_walking" => null
"status_transport_other" => null
"transport_other" => null
--}}

<div class="pb-2">
    @php $name = 'coexistence' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_coexistence" wire:model.live="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'status_transport_private_vehicle' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_transport_public_vehicle' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_transport_walking' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'status_transport_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.live="{{$model}}" />
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

@if ($enrollment->status_transport_other)
    <div class="pb-2">
        @php $name = 'transport_other' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
        <x-input right-icon="table" label="{{$label}}" wire:model.live="{{$model}}"/>
        {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
    </div>
@endif


