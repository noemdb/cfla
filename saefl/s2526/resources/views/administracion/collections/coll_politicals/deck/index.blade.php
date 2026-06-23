<div class="container-fluid">

    <div class="row">

        @forelse ($coll_politicals as $coll_political)
            <div class="col-sm-12 col-md-12 col-lg-6 pl-1 pr-1 pb-2">
                @include('administracion.collections.coll_politicals.deck.card.index')
            </div>
        @empty
        <div class=" text-muted font-weight-bold"> No hay políticas de cobro registradas </div class=" text-muted font-weight-bold">
            @endforelse

        </div>

</div>
