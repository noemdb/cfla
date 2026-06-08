<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

    @include('administracion.estudiants.partials.buttons.partials.edit')

    <a title="Representante" class="btn btn-danger btn-sm"
        href="{{ route('administracion.representants.index',['search'=>$estudiant->representant->ci_representant,'estudiant_id'=>$estudiant->id]) }}" role="button">
        <i class="{{ $icon_menus['representante'] }} fa-1x"></i>
    </a>

    {{-- @includeWhen(Auth::user()->IsAdmon(), 'administracion.estudiants.partials.buttons.partials.admon') --}}

    {{-- @includeWhen(Auth::user()->IsControl(), 'administracion.estudiants.partials.buttons.partials.control') --}}

    {{-- @includeWhen(Auth::user()->IsControl(), 'administracion.estudiants.deck.button.partials.control') --}}
</div>
