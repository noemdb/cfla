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
    <x-input right-icon="user" label="{{$label ?? ''}}" wire:modeL="{{$model ?? null}}"/>
    @error($model)<span class="text-red-600 small mb-2">{{ $message }}</span> @enderror
</div>

<x-button primary label="Siguiente" wire:click="next({{$step}})"/>