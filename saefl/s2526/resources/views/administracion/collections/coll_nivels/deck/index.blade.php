<div class="card-deck">
    @forelse ($coll_politicals as $coll_political)
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Detalles Generales</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Niveles</a>
            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Mensajes</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                @include('administracion.collections.coll_politicals.deck.card.index')
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                @php $coll_nivel = $coll_political->coll_nivel; @endphp
                @include('administracion.collections.coll_nivels.deck.card.index')
            </div>
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                @php $coll_nivel = $coll_political->coll_nivel; @endphp
                @include('administracion.collections.coll_nivels.deck.card.index')
            </div>
        </div>

    @empty
        <div> No hay políticas de cobro registradas </div>
    @endforelse
</div>
