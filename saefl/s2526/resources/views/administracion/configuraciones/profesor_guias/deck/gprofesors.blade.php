<div class="container">
    <div class="row p-0 m-0 pt-1">
        @foreach($datas as $profesor_guia)
            <div class="col-sm-5 col-md-4 col-lg-3 pl-1 p-1">
            {{-- <div class="col-sm-5 col-md-4 col-lg-4 pl-1 pr-1 pb-2"> --}}
                @include('administracion.configuraciones.profesor_guias.deck.card.gprofesor')
            </div>
        @endforeach
    </div>
</div>
