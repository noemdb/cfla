<div class="btn-group-vertical text-right" role="group">

    @include('administracion.estudiants.deck.button.partials.edit')

    <a title="Representante" class="btn btn-{{ ($estudiant->representant->id=='100000000') ? 'dark' : 'danger'}} p-2"
        href="{{ route('administracion.representants.index',['search'=>$estudiant->representant->ci_representant,'estudiant_id'=>$estudiant->id]) }}" role="button">
        <i class="{{ $icon_menus['representante'] }} fa-1x"></i>
    </a>

    @includeWhen(Auth::user()->IsAdmon(), 'administracion.estudiants.deck.button.partials.admon')

    @includeWhen(Auth::user()->IsControl(), 'administracion.estudiants.deck.button.partials.control')

</div>
