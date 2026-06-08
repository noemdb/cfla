<h4 class=" alert alert-secondary card-title my-0">
    <a name="" id="" class="btn btn-light float-right" href="{{route('controls.estudiants.cargaXls')}}" role="button"> <i class="{{$icon_menus['sync'] ?? ''}} text-success" aria-hidden="true"></i> </a>
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark" aria-hidden="true"></i>
    Listado de <span class="font-weight-bold">Estudiantes</span> formalmente Inscritos (Administrativa y Académica).
    @if ($estudiants->isNotEmpty())
        <div class="small font-weight-bold text-right mr-2 float-lg-right p-2 border rounded bg-light">
            N°: {{ $estudiants->count() ?? ''}}
        </div>
    @endif
</h4>

{{-- @if ($estudiants->isNotEmpty()) <div class="alert m-2 p-2 text-dark text-right font-weight-bold">Total: Bs {{ f_float( $estudiants->sum('ammount'))}}</div> @endif --}}

<div class="card-body px-0 mx-0">

    @includeif('controls.content.estudiants.table.crud')

</div>
