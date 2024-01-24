{{--
"age" => 0
"blood_type" => "A"
"weight" => 0
"height" => 0
"laterality" => "IZQUIERDA"
"order_born" => "1"
"group_family" => 0
"status_brother" => "true"
--}}

<div class="pb-2">
    @php $name = 'age' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'blood_type' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_blood_type" wire:model="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'weight' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'height' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'laterality' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_laterality" wire:model="{{$model}}" option-key-value/>
    {{-- @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror --}}
</div>

<div class="pb-2">
    @php $name = 'order_born' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'group_family' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'status_brother' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-toggle lg label="{{$label}}" wire:model.defer="{{$model}}" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

