{{--
"ci_estudiant" => "32446229"
"lastname" => "GOMEZ SANCHEZ"
"name" => "ANGELES TRINIDAD"
"gender" => "Femenino"
"date_birth" => "2008-04-11"
"town_hall_birth" => "INDEPENDENCIA"
"state_birth" => "YARACUY"
"country_birth" => "VENEZUELA"
"dir_address" => "URB. COLINAS DEL NORTE, AV 1 ENTRE CALLES 8 Y 10, CASA # 055, PRADOS DEL NORTE."
"grado_id" => "9"
"pestudio_id" => "1"
"institution" => "U.E.C. LOS ANGELES"
--}}


<div class="pb-2">
    @php $name = 'ci_estudiant' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="user" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'name' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="user" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'lastname' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="user" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'gender' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="['Masculino', 'Femenino']" wire:model="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'date_birth' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-datetime-picker label="{{$label}}" placeholder="{{$label}}" wire:model="{{$model}}" :min="now()->subYearss(30)" :max="now()" without-time="false"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'town_hall_birth' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'state_birth' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'country_birth' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'dir_address' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-textarea right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>


<div class="pb-2">
    @php $name = 'grado_id' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_grado" wire:model="{{$model}}" option-key-value/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'institution' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-select label="{{$label}}" placeholder="Seleccionar" :options="$list_oinstitucions" wire:model="{{$model}}" option-key-value/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<div class="pb-2">
    @php $name = 'pending_matter' ; $model = 'enrollment.'.$name; $label=$list_comment[$name] @endphp
    <x-input right-icon="table" label="{{$label}}" wire:modeL="{{$model}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

