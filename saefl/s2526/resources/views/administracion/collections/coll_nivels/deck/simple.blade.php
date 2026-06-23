@forelse ($coll_nivels as $coll_nivel)
    @php $list_comment = $coll_nivel->list_comment; @endphp
    @include('administracion.collections.coll_nivels.partials.details.simple')
@empty
<div class=" text-muted font-weight-bold"> No hay niveles de cobro registrados </div class=" text-muted font-weight-bold">
@endforelse
