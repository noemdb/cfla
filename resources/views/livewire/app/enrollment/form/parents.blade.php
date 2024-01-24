{{--
"mother_name" => null
"mother_lastname" => null
"mother_ci" => null
"mother_profession" => null
"mother_phones" => null
"mother_address" => null

--}}

<div class="pb-2">
    @php $name = 'mother_ci' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'mother_name' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'mother_lastname' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'mother_profession' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'mother_address' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'mother_phones' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<hr class="mt-4">
{{-- 
"father_name" => null
"father_lastname" => null
"father_ci" => null
"father_profession" => null
"father_phones" => null
"father_address" => null    
--}}

<div class="pb-2">
    @php $name = 'father_ci' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'father_name' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'father_lastname' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'father_profession' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'father_address' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'father_phones' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>
