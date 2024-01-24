{{--
"ci_representant" => "12079224"
"name_representant" => "ANA YOMAIRA SANCHEZ ORDOÑEZ"
"relationship" => "Madre"
"profession_representant" => ""
"phone_representant" => "04125081606"
"email_representant" => "anayso1306@gmail.com"
"recommended_by" => null
--}}

<div class="pb-2">
    @php $name = 'ci_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-inputs.maskable label="{{$label}}" mask="########" placeholder="{{$label}}" wire:model.defer="{{$model}}" right-icon="table" />
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'name_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'phone_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'cellphone_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'relationship' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_relationship" wire:model="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'profession_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_profession" wire:model="{{$model}}" option-key-value/>
</div>

<div class="pb-2">
    @php $name = 'email_representant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}" suffix="@mail.com" class="pr-28"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>


<div class="pb-2">
    @php $name = 'recommended_by' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>
